<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name'     => 'Ahmed Nabil',
            'email'    => 'ahmednassag@gmail.com',
            'password' => bcrypt('12345678'),
            // 'roles'    => ["Admin"],
        ]);
        $role        = Role::create(['name' => 'Admin']);
        $permissions = Permission::pluck('name')->all();
        $role->syncPermissions($permissions);
        $user->assignRole([$role->name]);
    }
}
