<?php

namespace Database\Seeders;

use App\Models\Feature;
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

        $features = [
            'AC',
            'Angle Sensor',
            'Engine RPM',
            'Escort BLE Fuel',
            'Escort LLS Fuel',
            'Fuel',
            'Temp Sensor',
            'iButton',
            'MDVR',
            'Normal',
            'Temp Sensor(BLE)',
            'Temp Sensor(1Wire)'
        ];
        foreach ($features as $feature) {
            Feature::create(['feature_name' => $feature]);
        }

        $packages = [
            [
                'package_code' => 'AC',
                'package_name' => 'AC',
            ],
            [
                'package_code' => 'Angle Sensor',
                'package_name' => 'Angle Sensor',
            ],
            [
                'package_code' => 'EngineRPM',
                'package_name' => 'Engine RPM',
            ],
            [
                'package_code' => 'EscortBLEFuel',
                'package_name' => 'Escort BLE Fuel',
            ],
            [
                'package_code' => 'EscortLLSFuel',
                'package_name' => 'Escort LLS Fuel',
            ],
            [
                'package_code' => 'Fuel',
                'package_name' => 'Fuel',
            ],
            [
                'package_code' => 'FuelAngleSensor',
                'package_name' => 'Fuel + Angle Sensor',
            ],
            [
                'package_code' => 'FuelTempSensor',
                'package_name' => 'Fuel + Temp Sensor',
            ],
            [
                'package_code' => 'FueliButton',
                'package_name' => 'Fuel + iButton',
            ],
            [
                'package_code' => 'FueliButtonMDVR',
                'package_name' => 'Fuel + iButton + MDVR',
            ],
            [
                'package_code' => 'FuelMDVR',
                'package_name' => 'Fuel + MDVR',
            ],
            [
                'package_code' => 'FuelSensor',
                'package_name' => 'Fuel + Sensor',
            ],
            [
                'package_code' => 'FuelSensorMDVR',
                'package_name' => 'Fuel + Sensor + MDVR',
            ],
            [
                'package_code' => 'Normal',
                'package_name' => 'Normal',
            ],
            [
                'package_code' => 'NormaliButton',
                'package_name' => 'Normal + iButton',
            ],
            [
                'package_code' => 'NormalMDVR',
                'package_name' => 'Normal + MDVR',
            ],
            [
                'package_code' => 'RPMEscortBLEFuel',
                'package_name' => 'RPM + Escort BLE Fuel',
            ],
            [
                'package_code' => 'TempSensorAngleSensor',
                'package_name' => 'Temp Sensor + Angle Sensor',
            ],
            [
                'package_code' => 'TempSensorBLE',
                'package_name' => 'Temp Sensor (BLE)',
            ],
            [
                'package_code' => 'TempSensor1Wire',
                'package_name' => 'Temp Sensor (1 wire)',
            ],
        ];

        DB::table('packages')->insert($packages);

        $periods = [
            [
                'period_name' => '3 Month',
                'period_days' => 30,
                'description' => '3',
            ],
            [
                'period_name' => '6 Month',
                'period_days' => 90,
                'description' => '6',
            ],
            [
                'period_name' => '9 Month',
                'period_days' => 180,
                'description' => '9',
            ],
            [
                'period_name' => '1 Year',
                'period_days' => 365,
                'description' => '12'
            ],
        ];

        DB::table('periods')->insert($periods);

        $plans = [
            [
                'package_id' => 1,
                'period_id' => 1,
            ],
            [
                'package_id' => 1,
                'period_id' => 2,
            ],
            [
                'package_id' => 1,
                'period_id' => 3,
            ],
            [
                'package_id' => 1,
                'period_id' => 4,
            ],

        ];

        DB::table('plans')->insert($plans);

        $user = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'mobile_no' => '7904600101',
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


        $data = [
            ['role_id' => 1, 'rights_id' => 2],
            ['role_id' => 1, 'rights_id' => 8],
            ['role_id' => 2, 'rights_id' => 3],
            ['role_id' => 2, 'rights_id' => 8],
            ['role_id' => 3, 'rights_id' => 4],
            ['role_id' => 3, 'rights_id' => 8],
            ['role_id' => 4, 'rights_id' => 5],
            ['role_id' => 4, 'rights_id' => 6],
            ['role_id' => 4, 'rights_id' => 8],
            ['role_id' => 5, 'rights_id' => 6],
            ['role_id' => 5, 'rights_id' => 8],
            ['role_id' => 6, 'rights_id' => 7],
            ['role_id' => 6, 'rights_id' => 7],
        ];

        // Insert the data into the role_rights table
        DB::table('role_rights')->insert($data);

        $data = [
            ['vehicle_type' => "Bike"],
            ['vehicle_type' => "Car"],
            ['vehicle_type' => "Van"],
            ['vehicle_type' => "Bus"],
            ['vehicle_type' => "Truck"],
            ['vehicle_type' => "Container Truck"],
            ['vehicle_type' => "RMC Truck"],
            ['vehicle_type' => "Cylinder Truck"],
            ['vehicle_type' => "Long Chassis Container Truck"],
            ['vehicle_type' => "JCB"],
            ['vehicle_type' => "Loader"],
            ['vehicle_type' => "Ace"],
            ['vehicle_type' => "Tipper"],
            ['vehicle_type' => "Tractor"],
            ['vehicle_type' => "Generator"]
        ];

        // Insert the data into the role_rights table
        DB::table('vehicle_types')->insert($data);


        $data = [
            [
                'country_name' => 'India',
                'short_name' => 'IND',
                'phone_code' => '+91',
                'timezone_name' => 'Asia/Kolkata',
                'timezone_offset' => '5:30',
                'timezone_minutes' => '330'
            ]
        ];

        DB::table('countries')->insert($data);

        $data = [
            [
                'network_provider_name' => 'Airtel'
            ],
            [
                'network_provider_name' => 'Jio'
            ],
            [
                'network_provider_name' => 'BSNL'
            ]
        ];

        DB::table('network_providers')->insert($data);


        $data = [
            [
                'supplier_name' => 'No Metioned'
            ],
            [
                'supplier_name' => 'KK PVT LTD'
            ],
            [
                'supplier_name' => 'KABIL CORP'
            ]
        ];

        DB::table('suppliers')->insert($data);


        $data = [
            [
                'point_type' => 'New Point'
            ],
            [
                'point_type' => 'Recharge Point'
            ]
        ];

        // Insert the data into the role_rights table
        DB::table('point_types')->insert($data);

        DB::table('device_types')->insert([
            ['device_type' => 'TELTONIKA', 'status' => 1],
            ['device_type' => 'ACUTE', 'status' => 1],
            ['device_type' => 'CONCOX', 'status' => 1],
            ['device_type' => 'PROTRACK', 'status' => 1],
            ['device_type' => 'BENWAY', 'status' => 1],
            ['device_type' => 'LIBITECH', 'status' => 1],
            ['device_type' => 'COBAL', 'status' => 1],
            ['device_type' => 'CABTRACK', 'status' => 1],
            ['device_type' => 'SEAWORLD', 'status' => 1],
            ['device_type' => 'ROADPOINT', 'status' => 1],
            ['device_type' => 'TELTONIKA', 'status' => 1],
            ['device_type' => 'RDM', 'status' => 1]
        ]);

        DB::table('device_models')->insert([
            ['device_model' => 'FMB910',  'status' => 1],
            ['device_model' => 'FMB920',  'status' => 1],
            ['device_model' => 'FMB120',  'status' => 1],
            ['device_model' => 'FMB125',  'status' => 1],
            ['device_model' => 'FMB130',  'status' => 1],
            ['device_model' => 'FMB140',  'status' => 1],
            ['device_model' => 'AC104',  'status' => 1],
            ['device_model' => 'AC106',  'status' => 1],
            ['device_model' => 'AC109',  'status' => 1],
            ['device_model' => 'AC140',  'status' => 1],
            ['device_model' => 'V5',  'status' => 1],
            ['device_model' => 'VT05C',  'status' => 1],
            ['device_model' => 'VT08S',  'status' => 1],
            ['device_model' => 'VT05S',  'status' => 1],
            ['device_model' => 'VT05R',  'status' => 1],
            ['device_model' => 'BW08',  'status' => 1],
            ['device_model' => 'BW09',  'status' => 1],
            ['device_model' => 'ET300',  'status' => 1],
            ['device_model' => 'MT100',  'status' => 1],
            ['device_model' => 'MT02',  'status' => 1],
            ['device_model' => 'MT200',  'status' => 1],
            ['device_model' => 'TK103',  'status' => 1],
            ['device_model' => '303F',  'status' => 1],
            ['device_model' => '306A',  'status' => 1],
            ['device_model' => 'G05',  'status' => 1],
            ['device_model' => 'S102',  'status' => 1],
            ['device_model' => 'S106',  'status' => 1],
            ['device_model' => 'GAGAN140',  'status' => 1],
            ['device_model' => 'RP01',  'status' => 1],
            ['device_model' => '140',  'status' => 1],
            ['device_model' => '104',  'status' => 1],
            ['device_model' => '106',  'status' => 1],
            ['device_model' => '109',  'status' => 1],
            ['device_model' => 'S116',  'status' => 1],
        ]);
    }
}
