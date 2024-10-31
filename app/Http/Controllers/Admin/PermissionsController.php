<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Traits\SortingValidation;

class PermissionsController extends AdminController
{
    use SortingValidation;

    protected const CREATE_MODAL_VIEW = 'Admin/Permissions/Create';
    protected const DELETE_MODAL_VIEW = 'Admin/Modals/DeleteConfirmationModal';
    protected const BASE_ROUTE = 'admin.permissions';

    protected function getModel(): string
    {
        return Permission::class;
    }

    protected function getModelTable(): string
    {
        return (new Permission)->getTable();
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['sortKey', 'sortOrder', 'search']);
        $itemsPerPage = (int) $request->input('itemsPerPage', 10);

        [$sortKey, $sortOrder] = $this->validateSorting(
            $filters['sortKey'] ?? null,
            $filters['sortOrder'] ?? null,
            'permissions'
        );

        // Eager load users and select only necessary columns
        $permissions = Permission::with(['users' => function($query) {
            $query->select('id', 'name');
        }])
            ->withTrashed()
            ->filter($filters)
            ->orderBy($sortKey, $sortOrder)
            ->paginate($itemsPerPage)
            ->through(fn ($permission) => $this->mapPermission($permission));

        return Inertia::render('Admin/Permissions/Index', [
            'permissions' => $permissions,
            'sortKey' => $sortKey,
            'sortOrder' => $sortOrder,
            'search' => $filters['search'] ?? '',
            'itemsPerPage' => $itemsPerPage,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show(int $id): \Inertia\Response
    {
        $permission = Permission::with(['users'])->withTrashed()->findOrFail($id);
        return Inertia::render('Admin/Permissions/Show', [
            'permission' => $this->mapPermission($permission),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        try {
            Permission::create($request->only('name'));
            return redirect()->back()->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['name' => 'Failed to create permission.']);
        }
    }

    /**
     * Map the given permission.
     *
     * @param Permission $permission
     * @return array
     */
    private function mapPermission(Permission $permission): array
    {
        return [
            'id' => $permission->id,
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
            'created_at' => $permission->created_at,
            'deleted_at' => $permission->deleted_at,
            'users' => $permission->users,
        ];
    }
}
