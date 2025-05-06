<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Admin;

class CheckAndFixPermissions extends Command
{
    protected $signature = 'permissions:check-and-fix';
    protected $description = 'Check and fix permissions for admin users';

    public function handle()
    {
        $this->info('Checking permissions...');

        // Required permissions
        $requiredPermissions = [
            'create-comments',
            'delete-comments',
            'view-comments',
        ];

        // Check and create missing permissions
        foreach ($requiredPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Get admin role
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            // Add missing permissions to admin role
            foreach ($requiredPermissions as $permission) {
                if (!$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                    $this->info("Added {$permission} to Admin role");
                }
            }
        }

        // Get super admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            // Add missing permissions to super admin role
            foreach ($requiredPermissions as $permission) {
                if (!$superAdminRole->hasPermissionTo($permission)) {
                    $superAdminRole->givePermissionTo($permission);
                    $this->info("Added {$permission} to Super Admin role");
                }
            }
        }

        $this->info('Permissions check completed!');
    }
}
