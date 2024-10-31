<?php
namespace App\Http\Controllers\Admin;

use App\Events\PasswordChanged;
use App\Http\Controllers\Controller;
use App\Models\PasswordChangeLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\SortingValidation;
use Jenssegers\Agent\Agent;
use Laravolt\Avatar\Avatar;

class UsersController extends AdminController
{
    use SortingValidation;

    protected const CREATE_MODAL_VIEW = 'Admin/Users/Create';
    protected const DELETE_MODAL_VIEW = 'Admin/Modals/DeleteConfirmationModal';
    protected const BASE_ROUTE = 'admin.users';
    private const COOKIE_EXPIRATION_DAYS = 30;

    protected function getModel(): string
    {
        return User::class;
    }

    protected function getModelTable(): string
    {
        return (new User)->getTable();
    }

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['sortKey', 'sortOrder', 'search']);
        $searchableColumns = ['name', 'email', 'username'];
        $itemsPerPage = (int) $request->input('itemsPerPage', 10);

        [$sortKey, $sortOrder] = $this->validateSorting(
            $filters['sortKey'] ?? null,
            $filters['sortOrder'] ?? null,
            'users'
        );

        // Eager load roles and select only necessary columns
        $users = User::withTrashed()
            ->with(['roles:id,name'])
            ->filter($filters, $searchableColumns)
            ->orderBy($sortKey, $sortOrder)
            ->paginate($itemsPerPage)
            ->through(fn ($user) => $this->mapUser($user));

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
            'search' => $filters['search'] ?? '',
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    public function show($username): \Inertia\Response
    {
        $user = User::where('username', $username)
            ->with(['passwordChangeLogs' => function ($query) {
                $query->orderBy('changed_at', 'desc')->take(1);
            }])
            ->firstOrFail();

        $lastSession = Cache::remember("last_session_{$user->id}", 600, function() use ($user) {
            return DB::table('sessions')
                ->where('user_id', $user->id)
                ->orderBy('last_activity', 'desc')
                ->first();
        });

        $lastPasswordChange = $user->passwordChangeLogs->isNotEmpty()
            ? $user->passwordChangeLogs->first()->changed_at
            : null;

        return Inertia::render('Admin/Users/Show', [
            'user' => array_merge($this->mapUser($user), [
                'session' => $lastSession,
                'lastPasswordChange' => $lastPasswordChange,
            ]),
        ]);
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users|regex:/^[A-Za-z0-9_]+$/',
            'name' => 'required|string|max:255|min:5',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        try {
            User::create($request->only(['username', 'name', 'email', 'password']));
            return redirect()->back()->with('success', 'User created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['name' => 'Failed to create user.']);
        }
    }

    public function showSessions(User $user): \Inertia\Response
    {
        $currentSessionId = session()->getId();
        $sessions = $this->getSessions($user->id, $currentSessionId);

        return Inertia::render('Admin/Users/Sessions', [
            'user' => $this->mapUser($user),
            'sessions' => $sessions,
        ]);
    }

    private function getSessions(int $userId, string $currentSessionId)
    {
        return DB::table('sessions')
            ->where('user_id', $userId)
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) use ($currentSessionId) {
                $agent = new Agent();
                $agent->setUserAgent($session->user_agent);
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'last_activity' => $session->last_activity,
                    'device' => $agent->device(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                    'is_current_device' => $session->id === $currentSessionId,
                ];
            });
    }

    public function logoutSession(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $currentUser = Auth::user();

        session()->getHandler()->destroy($sessionId);
        DB::table('sessions')->where('id', $sessionId)->delete();

        if ($sessionId === session()->getId()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $currentUser->setRememberToken(Str::random(60));
            $currentUser->save();
            cookie()->queue(cookie()->forget(Auth::getRecallerName()));
        } else {
            DB::table('users')->where('id', $currentUser->id)->update(['remember_token' => Str::random(60)]);
        }
        return redirect()->back()->with('success', 'Session logged out successfully.');
    }

    public function logoutAllOtherSessions(Request $request)
    {
        $currentUser = Auth::user();
        $currentSessionId = session()->getId();
        $userId = $currentUser->id;
        $otherSessions = DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->get();

        foreach ($otherSessions as $session) {
            session()->getHandler()->destroy($session->id);
        }

        DB::table('sessions')
            ->where('user_id', $userId)
            ->where('id', '!=', $currentSessionId)
            ->delete();

        DB::table('users')
            ->where('id', $userId)
            ->update(['remember_token' => Str::random(60)]);

        $currentUser->setRememberToken(Str::random(60));
        $currentUser->save();
        cookie()->queue(cookie()->forget(Auth::getRecallerName()));

        if ($request->has('remember')) {
            $request->session()->put('remember_token', $currentUser->getRememberToken());
            cookie()->queue(Auth::getRecallerName(), $currentUser->getRememberToken(), 60*24*self::COOKIE_EXPIRATION_DAYS);
        }

        return redirect()->back()->with('success', 'Logged out from all other sessions successfully.');
    }

    public function showAvatarUploadModal(User $user)
    {
        return Inertia::modal('Admin/Users/AvatarUpload', ['user' => $user])
            ->baseRoute('admin.users.show', $user)
            ->refreshBackdrop();
    }

    public function showChangePassword(User $user): \Inertia\Response
    {
        $passwordUpdates = PasswordChangeLog::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $agent = new Agent();
        $passwordUpdates->transform(function ($update) use ($agent) {
            $agent->setUserAgent($update->user_agent);
            $update->device = $agent->device();
            $update->platform = $agent->platform();
            $update->browser = $agent->browser();
            return $update;
        });

        return Inertia::render('Admin/Users/ChangePassword', [
            'user' => $user,
            'passwordUpdates' => $passwordUpdates,
        ]);
    }

    public function updatePassword(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);
        event(new PasswordChanged($request->user(), $request->ip(), $request->header('User-Agent')));
        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function uploadAvatar(Request $request, User $user)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Process the uploaded avatar file
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar_url = Storage::url($avatarPath);
            $user->save();

            return redirect()->back()->with('success', 'Avatar updated successfully.');
        }

        return redirect()->back()->with('success', 'Avatar not updated.');
    }

    private function mapUser(User $user): array
    {
        $avatarUrl = $user->avatar_url;

        if (!$avatarUrl) {
            $avatar = new Avatar(config('laravolt.avatar'));
            $avatarUrl = $avatar->create($user->name)->toBase64();
        }

        return [
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_url' => $avatarUrl,
            'roles' => $user->roles,
            'created_at' => $user->created_at,
            'deleted_at' => $user->deleted_at,
        ];
    }
}
