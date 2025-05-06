<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        try {
            $this->authorize('view-admins');
            $admins = Admin::with('roles')->paginate(10);
            return view('admin.admins.index', compact('admins'));
        } catch (\Exception $e) {
            Log::error('Error loading admins: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading admins. Please try again.');
        }
    }

    public function create()
    {
        try {
            $this->authorize('create-admins');
            $roles = Role::all();
            return view('admin.admins.create', compact('roles'));
        } catch (\Exception $e) {
            Log::error('Error loading create admin form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading create form. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $this->authorize('create-admins');

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:admins'],
                'password' => ['required', 'confirmed', Password::defaults()],
                'roles' => ['required', 'array'],
                'roles.*' => ['exists:roles,id'],
            ]);

            $admin = Admin::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $admin->roles()->attach($validated['roles']);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating admin: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error creating admin. Please try again.')
                ->withInput();
        }
    }

    public function edit(Admin $admin)
    {
        try {
            $this->authorize('edit-admins');
            $roles = Role::all();
            return view('admin.admins.edit', compact('admin', 'roles'));
        } catch (\Exception $e) {
            Log::error('Error loading edit admin form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading edit form. Please try again.');
        }
    }

    public function update(Request $request, Admin $admin)
    {
        try {
            $this->authorize('edit-admins');

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,' . $admin->id],
                'password' => ['nullable', 'confirmed', Password::defaults()],
                'roles' => ['required', 'array'],
                'roles.*' => ['exists:roles,id'],
            ]);

            $admin->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
            ]);

            if ($validated['password']) {
                $admin->update(['password' => Hash::make($validated['password'])]);
            }

            $admin->roles()->sync($validated['roles']);

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating admin: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating admin. Please try again.')
                ->withInput();
        }
    }

    public function destroy(Admin $admin)
    {
        try {
            $this->authorize('delete-admins');

            if ($admin->isSuperAdmin()) {
                return response()->json([
                    'error' => 'Cannot delete super admin.'
                ], 422);
            }

            // Check for related data
            if ($admin->tasks()->count() > 0) {
                return response()->json([
                    'error' => 'Cannot delete admin with assigned tasks. Please reassign or delete the tasks first.'
                ], 422);
            }

            // Delete related data
            $admin->comments()->delete();
            $admin->sentMessages()->delete();
            $admin->receivedMessages()->delete();
            $admin->roles()->detach();

            // Delete admin
            $admin->delete();

            if (request()->wantsJson()) {
                return response()->json(['message' => 'Admin deleted successfully']);
            }

            return redirect()->route('admin.admins.index')
                ->with('success', 'Admin deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting admin: ' . $e->getMessage());

            if (request()->wantsJson()) {
                return response()->json([
                    'error' => 'Failed to delete admin. Please try again.'
                ], 500);
            }

            return redirect()->route('admin.admins.index')
                ->with('error', 'Failed to delete admin.');
        }
    }
}
