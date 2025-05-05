<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        // Super admin has all permissions
        Gate::before(function ($admin, $permission) {
            if ($admin->isSuperAdmin()) {
                return true;
            }
        });

        // Admin Management Gates
        Gate::define('view-admins', function ($admin) {
            return $admin->hasPermission('view-admins');
        });

        Gate::define('create-admins', function ($admin) {
            return $admin->hasPermission('create-admins');
        });

        Gate::define('edit-admins', function ($admin) {
            return $admin->hasPermission('edit-admins');
        });

        Gate::define('delete-admins', function ($admin) {
            return $admin->hasPermission('delete-admins');
        });

        // Role Management Gates
        Gate::define('view-roles', function ($admin) {
            return $admin->hasPermission('view-roles');
        });

        Gate::define('create-roles', function ($admin) {
            return $admin->hasPermission('create-roles');
        });

        Gate::define('edit-roles', function ($admin) {
            return $admin->hasPermission('edit-roles');
        });

        Gate::define('delete-roles', function ($admin) {
            return $admin->hasPermission('delete-roles');
        });

        // Permission Management Gates
        Gate::define('view-permissions', function ($admin) {
            return $admin->hasPermission('view-permissions');
        });

        Gate::define('create-permissions', function ($admin) {
            return $admin->hasPermission('create-permissions');
        });

        Gate::define('edit-permissions', function ($admin) {
            return $admin->hasPermission('edit-permissions');
        });

        Gate::define('delete-permissions', function ($admin) {
            return $admin->hasPermission('delete-permissions');
        });

        // Intern Management Gates
        Gate::define('view-interns', function ($admin) {
            return $admin->hasPermission('view-interns');
        });

        Gate::define('create-interns', function ($admin) {
            return $admin->hasPermission('create-interns');
        });

        Gate::define('edit-interns', function ($admin) {
            return $admin->hasPermission('edit-interns');
        });

        Gate::define('delete-interns', function ($admin) {
            return $admin->hasPermission('delete-interns');
        });

        // Task Management Gates
        Gate::define('view-tasks', function ($admin) {
            return $admin->hasPermission('view-tasks');
        });

        Gate::define('create-tasks', function ($admin) {
            return $admin->hasPermission('create-tasks');
        });

        Gate::define('edit-tasks', function ($admin) {
            return $admin->hasPermission('edit-tasks');
        });

        Gate::define('delete-tasks', function ($admin) {
            return $admin->hasPermission('delete-tasks');
        });
    }
}
