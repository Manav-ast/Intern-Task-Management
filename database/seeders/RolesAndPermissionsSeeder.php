<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Admin Management
            'view-admins',
            'create-admins',
            'edit-admins',
            'delete-admins',

            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Permission Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',

            // Intern Management
            'view-interns',
            'create-interns',
            'edit-interns',
            'delete-interns',

            // Task Management
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'delete-tasks',

            // Comment Management
            'view-comments',
            'create-comments',
            'edit-comments',
            'delete-comments',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'admin'
            ]);
        }

        // Create roles and assign permissions
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'guard_name' => 'admin',
            'slug' => 'super-admin',
            'description' => 'Super Administrator with all permissions'
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::create([
            'name' => 'Admin',
            'guard_name' => 'admin',
            'slug' => 'admin',
            'description' => 'Administrator with limited permissions'
        ]);
        $admin->givePermissionTo([
            'view-admins',
            'view-roles',
            'view-permissions',
            'view-interns',
            'view-tasks',
            'create-tasks',
            'edit-tasks',
            'view-comments',
            'create-comments',
            'delete-comments',
        ]);

        // Create a super admin user
        $superAdminUser = Admin::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        $superAdminUser->assignRole($superAdmin);

        // Create a regular admin user
        $adminUser = Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $adminUser->assignRole($admin);
    }
}
