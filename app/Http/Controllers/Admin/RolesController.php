<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Traits\SortingValidation;
use Laravolt\Avatar\Avatar;

class RolesController extends AdminController
{
    use SortingValidation;

    protected const CREATE_MODAL_VIEW = 'Admin/Roles/Create';
    protected const DELETE_MODAL_VIEW = 'Admin/Modals/DeleteConfirmationModal';
    protected const BASE_ROUTE = 'admin.roles';

    protected function getModel(): string
    {
        return Role::class;
    }

    protected function getModelTable(): string
    {
        return (new Role)->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['sortKey', 'sortOrder', 'search']);
        $itemsPerPage = (int) $request->input('itemsPerPage', 10);

        [$sortKey, $sortOrder] = $this->validateSorting(
            $filters['sortKey'] ?? null,
            $filters['sortOrder'] ?? null,
            'roles'
        );

        // Eager load users with constraints and select only necessary columns
        $roles = Role::withTrashed()
            ->with(['users' => function ($query) {
                $query->select('id', 'name', 'username', 'avatar_url')->take(10); // Limit number of associated users
            }
            ])
            ->withCount('users as total_users_count')
            ->filter($filters)
            ->orderBy($sortKey, $sortOrder)
            ->paginate($itemsPerPage)
            ->through(fn ($role) => $this->mapRoleBasic($role));

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
            'search' => $filters['search'] ?? '',
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Role $role
     * @return \Inertia\Response
     */
    public function show(Role $role): \Inertia\Response
    {
        $permissions = Permission::all();

        $role->loadCount('users as total_users_count');
        $role->load([
            'permissions',
            'users' => function ($query) {
                $query->take(10)->with('roles');
            }
        ]);

        return Inertia::render('Admin/Roles/Show', [
            'role' => $this->mapRoleWithPermissions($role),
            'permissions' => $permissions,
        ]);
    }

    private function mapRoleBasic($role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'guard_name' => $role->guard_name,
            'created_at' => $role->created_at,
            'deleted_at' => $role->deleted_at,
            'users' => $role->users->map(function ($user) {
                return $this->mapUser($user);
            })->toArray(),
            'total_users_count' => $role->total_users_count,
        ];
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
            'avatar_url' => $avatarUrl,
        ];
    }

    private function mapRoleWithPermissions($role): array
    {
        return array_merge($this->mapRoleBasic($role), [
            'permissions' => $role->permissions->map(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                ];
            })->toArray(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->validateRoleName($request);

        try {
            Role::create(['name' => $request->name]);
            return redirect()->back()->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['name' => 'Failed to create role.']);
        }
    }

    private function validateRoleName(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:roles,name']);
    }

    /**
     * Update the specified resource's permissions in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermissions(Request $request, Role $role): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role->permissions()->sync($request->get('permissions', []));

            return redirect()->back()->with('success', 'Permissions updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update permissions.');
        }
    }
}
