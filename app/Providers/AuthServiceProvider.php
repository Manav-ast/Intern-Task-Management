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
        Gate::before(function ($admin, $permission) {
            if ($admin->isSuperAdmin()) {
                return true;
            }

            return $admin->hasPermission($permission);
        });
    }
}
