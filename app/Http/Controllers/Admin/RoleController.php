<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;

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
            Log::error('Error loading roles: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading roles. Please try again.');
        }
    }

    public function create()
    {
        try {
            $this->authorize('create-roles');
            $permissions = Permission::all();
            return view('admin.roles.create', compact('permissions'));
        } catch (\Exception $e) {
            Log::error('Error loading create role form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading create form. Please try again.');
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
            Log::error('Error creating role: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating role. Please try again.')
                ->withInput();
        }
    }

    public function edit(Role $role)
    {
        try {
            $this->authorize('edit-roles');

            if ($role->slug === 'super-admin') {
                return back()->with('error', 'Cannot edit super admin role.');
            }

            $permissions = Permission::all();
            return view('admin.roles.edit', compact('role', 'permissions'));
        } catch (\Exception $e) {
            Log::error('Error loading edit role form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading edit form. Please try again.');
        }
    }

    public function update(Request $request, Role $role)
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Error updating role: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating role. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Role $role)
    {
        try {
            $this->authorize('delete-roles');

            if ($role->slug === 'super-admin') {
                return response()->json([
                    'error' => 'Cannot delete super admin role.'
                ], 422);
            }

            // Check if role has any admins assigned
            if ($role->users()->count() > 0) {
                return response()->json([
                    'error' => 'Cannot delete role that has admins assigned. Please remove all admins from this role first.'
                ], 422);
            }

            // Delete role and its relationships
            $role->permissions()->detach();
            $role->delete();

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Role deleted successfully']);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting role: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'Error deleting role. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Error deleting role. Please try again.');
        }
    }

    public function show(Role $role)
    {
        try {
            $this->authorize('view-roles');
            $role->load('permissions');
            return view('admin.roles.show', compact('role'));
        } catch (\Exception $e) {
            Log::error('Error viewing role: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error viewing role. Please try again.');
        }
    }
}
