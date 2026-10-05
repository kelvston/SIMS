<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /** Legacy retail-inventory permissions remain active internally but are not part of garage role setup. */
    private const HIDDEN_LEGACY_PERMISSIONS = ['view phones', 'receive phones', 'edit phones', 'delete phones'];
    public function __construct()
    {
        // Only authenticated users with 'manage roles' permission can access these actions
        $this->middleware(['auth', 'permission:manage roles']);
    }

    /**
     * Display a listing of the roles.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(10);
        $hiddenPermissions = self::HIDDEN_LEGACY_PERMISSIONS;
        return view('roles.index', compact('roles', 'hiddenPermissions'));
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissions = Permission::whereNotIn('name', self::HIDDEN_LEGACY_PERMISSIONS)->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'], // Ensure selected permissions exist
        ]);

        $role = Role::create(['name' => $request->name]);

        // Assign permissions to the role
        if ($request->has('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully!');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:permissions,name'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,name'],
        ]);

        $permission = Permission::create(['name' => $request->name]);

        if ($request->filled('roles')) {
            foreach ($request->roles as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role) {
                    $role->givePermissionTo($permission);
                }
            }
        }

        return redirect()->route('roles.index')->with('success', 'Permission created and assigned successfully!');
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $permissions = Permission::whereNotIn('name', self::HIDDEN_LEGACY_PERMISSIONS)->get();
        $rolePermissions = $role->permissions->pluck('name')->toArray(); // Get permissions assigned to this role
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->name = $request->name;
        $role->save();

        // Sync permissions
        // Keep legacy inventory permissions already assigned to this role; they are deliberately hidden from garage setup.
        $hiddenAssignedPermissions = $role->permissions()
            ->whereIn('name', self::HIDDEN_LEGACY_PERMISSIONS)
            ->pluck('name')
            ->all();
        $role->syncPermissions(array_unique(array_merge($request->permissions ?? [], $hiddenAssignedPermissions)));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully!');
    }

    /**
     * Remove the specified role from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Role $role)
    {
        // Prevent deleting 'admin' role or other critical roles if desired
        if ($role->name === 'admin') {
            return redirect()->back()->with('error', 'Cannot delete the admin role.');
        }

        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Role deleted successfully!');
    }

    public function createPermission()
    {

        $permissions = Permission::all(); // Get all available permissions
        $roles = Role::all();
        return view('roles.create_permission', compact('permissions','roles'));
    }

}
