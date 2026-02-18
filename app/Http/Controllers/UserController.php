<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(['permission:users_read'])->only('index');
        $this->middleware(['permission:users_read'])->only('create');
        $this->middleware(['permission:users_create'])->only('store');
        $this->middleware(['permission:users_update'])->only('edit');
        $this->middleware(['permission:users_update'])->only('update');
        $this->middleware(['permission:users_delete'])->only('destroy');
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::whereHasRole(['Admin' , 'employe' ])->with('roles', 'permissions')->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })->paginate(10)->appends(['search' => $search]);

        // Get all permissions from database for reference
        $allPermissions = \App\Models\Permission::all();
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        return view('Users.index', compact('users', 'search','roles' , 'permissions', 'allPermissions'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        return view('Users.create', compact('roles', 'permissions'));
    }
    

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:users|string|max:255',
            'password' => 'required|string|min:4|confirmed',
            'roles' => 'required|exists:roles,id',
            'permissions' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'password' => Hash::make($request->password),
        ]);

        // Get role names from IDs and sync
        if ($request->has('roles') && !empty($request->roles)) {
            $roleNames = Role::whereIn('id', (array)$request->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        }

        // Get permission names from IDs and sync
        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionNames = \App\Models\Permission::whereIn('id', (array)$request->permissions)->pluck('name')->toArray();
            $user->syncPermissions($permissionNames);
        }
        
        return redirect()->route('users.index')->with('success', 'تم انشاء المستخدم بنجاح.');
    }

    // public function show(User $user)
    // {
    //     $user->load('roles');
    //     return view('Users.show', compact('user'));
    // }

    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        $user->load('roles', 'permissions');
        return view('Users.edit', compact('user', 'roles', 'permissions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:users,name,' . $user->id,
            'password' => 'nullable|string|min:4|confirmed',
            'roles' => 'nullable|exists:roles,id',
            'permissions' => 'nullable|array',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        // Get role names from IDs and sync
        if ($request->has('roles') && !empty($request->roles)) {
            $roleNames = Role::whereIn('id', (array)$request->roles)->pluck('name')->toArray();
            $user->syncRoles($roleNames);
        } else {
            $user->syncRoles([]);
        }

        // Get permission names from IDs and sync
        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionNames = \App\Models\Permission::whereIn('id', (array)$request->permissions)->pluck('name')->toArray();
            $user->syncPermissions($permissionNames);
        } else {
            $user->syncPermissions([]);
        }

        return redirect()->route('users.index')->with('success', 'تم تعديل المستخدم بنجاح.');
    }

    /**
     * Get user data for AJAX request (modal)
     */
public function getUserData(User $user)
    {
        $user->load('roles', 'permissions');
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        
        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Get roles and permissions for create modal (AJAX)
     */
    public function getRolesPermissions()
    {
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        
        return response()->json([
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::find($request->role_id);
        $user->assignRole($role);

        return redirect()->back()->with('success', 'Role assigned successfully.');
    }

    public function removeRole(User $user, Role $role)
    {
        $user->removeRole($role);
        return redirect()->back()->with('success', 'Role removed successfully.');
    }
}
