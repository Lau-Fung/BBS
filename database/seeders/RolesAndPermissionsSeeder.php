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

        // ---- Permissions (central list used by roles) ----
        $perms = [
            // Users & admin
            'users.view',
            'users.manage',                 // create/update/delete + assign roles
            'users.assign-roles',
            'admin.reference.manage',       // carriers and reference data
            'activity_logs.view',

            // Clients
            'clients.view',
            'clients.create',
            'clients.update',
            'clients.delete',
            'clients.export',

            // Assignments (also used by import/upload/export routes)
            'assignments.view',
            'assignments.create',           // add / upload
            'assignments.update',           // edit
            'assignments.delete',
            'assignments.restore',
            'assignments.export',

            // Records (generic data records)
            'records.view',
            'records.create',
            'records.update',
            'records.delete',

            // Manager-only special: allow granting edit to data entry (hook for UI)
            'grant.edit.permission',
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
        // Admin: FULL access (including delete on clients/data)
        $admin->syncPermissions($perms);

        // Manager: add, upload, view, edit, and can grant edit permission to Data Entry (no deletes)
        $manager->syncPermissions([
            'users.view',
            'activity_logs.view',
            'admin.reference.manage',
            'clients.view','clients.create','clients.update','clients.export',
            'assignments.view','assignments.create','assignments.update','assignments.export',
            'records.view','records.create','records.update',
            'grant.edit.permission',
        ]);

        // Data Entry: add, upload and view only (no edit/delete)
        $entry->syncPermissions([
            'users.view',
            'clients.view','clients.create',
            'assignments.view','assignments.create',
            'records.view','records.create',
        ]);

        // Viewer: view only + export; cannot upload
        $viewer->syncPermissions([
            'clients.view','clients.export',
            'assignments.view','assignments.export',
            'records.view',
            'activity_logs.view',
        ]);

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
