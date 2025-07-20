<?php

namespace Database\Seeders;

use App\Models\PermissionHead;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-management',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'product-list',
            'product-create',
            'product-edit',
            'product-delete',
        ];

        foreach ($permissions as $permission) {
            // Extract the first part of the permission before the hyphen
            $prefix = Str::before($permission, '-');

            // Find the header ID from the permissionHead table
            $header = PermissionHead::where('permission_title', $prefix)->first();

            Permission::firstOrCreate([
                'header_id' => $header ? $header->id : 0, // Default to 0 if no match is found
                'name' => $permission,
            ]);
        }
    }
}
