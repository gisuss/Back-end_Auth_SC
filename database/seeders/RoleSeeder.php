<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role1 = Role::create(['name' => 'coordinator']);
        $role2 = Role::create(['name' => 'student']);
        $role3 = Role::create(['name' => 'tutor']);

        Permission::create(['name' => 'tasks'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'workplan'])->syncRoles([$role1, $role2, $role3]);
        Permission::create(['name' => 'projects'])->syncRoles([$role1, $role2, $role3]);
    }
}
