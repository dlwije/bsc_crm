<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::unprepared('
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE TABLE users;
            SET FOREIGN_KEY_CHECKS = 1;
        ');
        $sysAdminRole = Role::where('name', 'sysadmin')->first();
        $sysUserRole = Role::where('name', 'sysuser')->first();

        $sysAdmin = User::create([
            'userName' => 'sysadmin@test.com',
            'password' => bcrypt('12345678'),
            'name' => 'System Admin',
            'last_ip' => '127.0.0.1'
        ]);

        $sysUser = User::create([
            'userName' => 'sysuser@test.com',
            'password' => bcrypt('12345678'),
            'name' => 'System User',
            'last_ip' => '127.0.0.1'
        ]);

        $sysAdmin->assignRole($sysAdminRole);
        $sysUser->assignRole($sysUserRole);
    }
}
