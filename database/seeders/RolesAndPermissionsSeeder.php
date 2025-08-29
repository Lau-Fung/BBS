<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // clear cached roles & permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // ---- Permissions (add what your policies/controllers check) ----
        $perms = [
            // Assignments (used by AssignmentPolicy)
            'assignments.view',
            'assignments.create',
            'assignments.update',
            'assignments.delete',
            'assignments.restore',

            // You can keep your user/record perms too if you use them elsewhere
            'users.view',
            'users.assign-roles',
            'records.view',
            'records.create',
            'records.update',
            'records.delete',
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ---- Roles ----
        $admin   = Role::firstOrCreate(['name' => 'Admin',      'guard_name' => 'web']);
        $manager = Role::firstOrCreate(['name' => 'Manager',    'guard_name' => 'web']);
        $entry   = Role::firstOrCreate(['name' => 'Data Entry', 'guard_name' => 'web']);
        $viewer  = Role::firstOrCreate(['name' => 'Viewer',     'guard_name' => 'web']);

        // ---- Map permissions to roles ----
        $admin->syncPermissions($perms);

        $manager->syncPermissions([
            'assignments.view','assignments.create','assignments.update',
            'records.view','records.create','records.update',
        ]);

        $entry->syncPermissions([
            'assignments.view','assignments.create','assignments.update',
            'records.view','records.create','records.update',
        ]);

        $viewer->syncPermissions(['assignments.view','records.view']);

        // ---- Bootstrap first admin user ----
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.it'],
            ['name' => 'System Admin', 'password' => Hash::make('ChangeMe#1234')]
        );
        $user->syncRoles(['Admin']);

        // recache
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
