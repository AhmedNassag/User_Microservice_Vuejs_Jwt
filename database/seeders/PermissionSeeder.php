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
            'read-roles',
            'show-roles',
            'create-roles',
            'update-roles',
            'delete-roles',

            'read-users',
            'show-users',
            'create-users',
            'update-users',
            'delete-users',
        ];

        foreach ($permissions as $permission)
        {
            Permission::create(['name' => $permission]);
        }
    }
}
