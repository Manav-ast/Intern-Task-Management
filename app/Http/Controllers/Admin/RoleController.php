<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RoleController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $this->authorize('view-roles');

            $roles = Role::with('permissions')->paginate(10);
            return view('admin.roles.index', compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error loading roles: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $this->authorize('create-roles');

            $permissions = Permission::all();
            return view('admin.roles.create', compact('permissions'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error accessing create role page: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create-roles');

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255', 'unique:roles'],
                'slug' => ['required', 'string', 'max:255', 'unique:roles'],
                'description' => ['nullable', 'string'],
                'permissions' => ['required', 'array'],
                'permissions.*' => ['exists:permissions,id'],
            ]);

            $role = Role::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'],
            ]);

            $role->permissions()->attach($validated['permissions']);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error creating role: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Role $role)
    {
        $this->authorize('edit-roles');

        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Cannot edit super admin role.');
        }

        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $this->authorize('edit-roles');

        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Cannot edit super admin role.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id],
            'slug' => ['required', 'string', 'max:255', 'unique:roles,slug,' . $role->id],
            'description' => ['nullable', 'string'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'],
        ]);

        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $this->authorize('delete-roles');

        if ($role->slug === 'super-admin') {
            return back()->with('error', 'Cannot delete super admin role.');
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    public function show(Role $role)
    {
        $this->authorize('view-roles');
        $role->load('permissions');
        return view('admin.roles.show', compact('role'));
    }
}
