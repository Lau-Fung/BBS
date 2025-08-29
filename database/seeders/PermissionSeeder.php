<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LookupSeeder extends Seeder {
    public function run()
    {
        $perms = [
            'assignments.view','assignments.create','assignments.update','assignments.delete','assignments.restore'
        ];
        foreach ($perms as $p) { Permission::firstOrCreate(['name'=>$p]); }

        $admin = Role::firstOrCreate(['name'=>'Admin']);
        $admin->givePermissionTo($perms);

        $manager = Role::firstOrCreate(['name'=>'Manager']);
        $manager->givePermissionTo(['assignments.view','assignments.create','assignments.update']);

        // give your user the Admin role
        $user = \App\Models\User::first(); // or find by email
        if ($user) $user->assignRole('Admin');
    }
}
