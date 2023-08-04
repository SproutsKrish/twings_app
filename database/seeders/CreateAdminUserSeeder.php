<?php

namespace Database\Seeders;

use App\Models\ModelHasRole;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;


class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $user = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('123456'),
            'secondary_password' => bcrypt('twingszxc'),
            'role_id' => 1
        ]);

        $roles = [
            'Super Admin',
            'Admin',
            'Distributor',
            'Dealer',
            'SubDealer',
            'Client',
            'Vehicle Owner',
            'Staff'
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
        // Retrieve the Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        // Assign the Super Admin role to the user
        $user->assignRole([$superAdminRole->id]);

        $permissions = Permission::pluck('id', 'id')->all();

        $role->syncPermissions($permissions);
        $user->syncPermissions($permissions);
    }
}
