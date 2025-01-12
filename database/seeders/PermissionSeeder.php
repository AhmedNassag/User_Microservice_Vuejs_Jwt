<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            //role permissions
            'read-role',
            'show-role',
            'create-role',
            'update-role',
            'delete-role',

            //user permissions
            'read-user',
            'show-user',
            'create-user',
            'update-user',
            'delete-user',
        ];

        foreach ($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }
    }
}
