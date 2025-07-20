<?php

namespace Database\Seeders;

use App\Models\PermissionHead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionHeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PermissionHead::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $permissionHead = [
            'Role',
            'User',
            'Product',
        ];

        foreach ($permissionHead as $permission) {
            PermissionHead::create(['permission_title' => $permission]);
        }
    }
}
