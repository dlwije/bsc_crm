<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::unprepared('
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE model_has_roles;
            TRUNCATE TABLE roles;
            SET FOREIGN_KEY_CHECKS = 1;
        ');

        $permissions = Permission::all();

        $roleAdmin = Role::create(['name' => 'sysadmin', 'guard_name' => 'api']);
        Role::create(['name' => 'sysuser', 'guard_name' => 'api']);
        Role::create(['name' => 'customer', 'guard_name' => 'api']);

        $roleAdmin->syncPermissions($permissions);
    }
}
