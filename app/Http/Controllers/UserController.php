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
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $users = User::with('roles', 'permissions')->when($search, function ($query) use ($search) {
            return $query->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
        })->paginate(10)->appends(['search' => $search]);
        return view('Users.index', compact('users', 'search'));
    }

    public function create()
    {
        $roles = Role::all();
        $permissions = \App\Models\Permission::all();
        return view('Users.create', compact('roles', 'permissions'));
    }
    // public function store(Request $data)
    // {
    //     // dd($data);
    //     $data->validate([
    //         'name' => 'required|string|max:255',
    //         'password' => 'required|string|min:4|confirmed',
    //         'type' => 'required|string|exists:roles,name',
    //         'permissions' => 'array',
    //         'permissions.*' => 'exists:permissions,id',
    //     ]);
    //     $request_data  = $data->except(['password', 'password_confirmation', 'permissions']);
    //     $request_data['password'] = bcrypt($data->password);
    //     $user = User::create($request_data);
    //     if ($data->has('roles') && !empty($request->roles)) {
    //         $user->roles()->attach($request_data['type']);
    //     }

    //     if ($data->has('permissions') && !empty($request->permissions)) {
    //         $user->permissions()->attach($data->permissions);
    //     }
      
    //     return redirect()->route('users.index')->with(['success' => 'تم الحفظ بنجاح']);
    // }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:4|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'array',
        ]);

        $user = User::create([
            'name' => $request->name,
            // 'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('roles') && !empty($request->roles)) {
            $user->roles()->attach($request->roles);
        }

        if ($request->has('permissions') && !empty($request->permissions)) {
            $permissionIds = \App\Models\Permission::whereIn('name', $request->permissions)->pluck('id');
            $user->permissions()->attach($permissionIds);
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
        $user->load('roles');
        return view('Users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
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
