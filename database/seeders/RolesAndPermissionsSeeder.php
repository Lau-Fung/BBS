<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear cached roles/permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions (expand later for your domain)
        $perms = [
            'users.view',
            'users.assign-roles',
            'records.view',
            'records.create',
            'records.update',
            'records.delete',
        ];
        foreach ($perms as $p) {
            Permission::firstOrCreate(['name' => $p]);
        }

        // Roles
        $admin   = Role::firstOrCreate(['name' => 'Admin']);
        $manager = Role::firstOrCreate(['name' => 'Manager']);
        $entry   = Role::firstOrCreate(['name' => 'Data Entry']);
        $viewer  = Role::firstOrCreate(['name' => 'Viewer']);

        // Map permissions → roles
        $admin->syncPermissions($perms);
        $manager->syncPermissions(['records.view','records.create','records.update']);
        $entry->syncPermissions(['records.view','records.create','records.update']);
        $viewer->syncPermissions(['records.view']);

        // Create first admin
        $user = User::firstOrCreate(
            ['email' => 'admin@admin.it'],
            ['name' => 'System Admin', 'password' => Hash::make('ChangeMe#1234')]
        );
        $user->syncRoles(['Admin']);
    }
}
