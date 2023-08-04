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

        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'country-list',
            'country-create',
            'country-edit',
            'country-delete'
        ];
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $user = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => bcrypt('123456'),
            'secondary_password' => bcrypt('twingszxc'),
            'role_id' => 1
        ]);

        // Retrieve the Super Admin role
        $superAdminRole = Role::where('name', 'Super Admin')->first();

        // // Assign the Super Admin role to the user
        $user->assignRole([$superAdminRole->id]);

        $permissions = Permission::pluck('id', 'id')->all();

        $superAdminRole->syncPermissions($permissions);
        $user->syncPermissions($permissions);
    }
}
