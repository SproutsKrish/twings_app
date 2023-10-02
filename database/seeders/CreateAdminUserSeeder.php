<?php

namespace Database\Seeders;

use App\Models\AlertType;
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
            'Basic',
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

        $alert_types = [
            'AC On',
            'AC Off',
            'Ignition On',
            'Ignition Off',
            'Over Speed Alert',
            'SOS Alert',
            'Power Off',
            'Geo In',
            'Geo Out',
            'Harsh Acceleration',
            'Harsh Braking',
            'Accident',
            'Fuel Fill',
            'Fuel Dip',
            'Power On',
            'Internal Battery Low',
            'SIM Tray Open',
            'Internal Battery Normal',
            'Internal Battery Removed',
            'Parking',
            'Idling',
            'Towing',
            'Renewal Vehicle Insurance',
            'Poor Tyre Condition',
            'Hub In',
            'Hub Out',
            'Route Deviation Out',
            'Route Deviation In',
            'Vibration Alert',
            'Fuel Sensor Disconnected or Zero value',
            'Temperature Low Alert',
            'Temperature High Alert',
            'Charge On',
            'Charge Off',
            'Vehicle Delay',
            'Safe Parking On',
            'Safe Parking Off',
            'GPS On',
            'GPS Off'
        ];

        foreach ($alert_types as $alert_type) {
            AlertType::create(['alert_type' => $alert_type]);
        }

        $packages = [
            [
                'package_code' => 'Basic',
                'package_name' => 'Basic',
            ],
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
            ]

        ];

        DB::table('plans')->insert($plans);

        $user = User::create([
            'name' => 'superadmin',
            'email' => 'superadmin@gmail.com',
            'mobile_no' => '7904600101',
            'password' => bcrypt('twingszxc'),
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
            ['vehicle_type' => "Truck"],
            ['vehicle_type' => "Bus"],
            ['vehicle_type' => "Car"],
            ['vehicle_type' => "Bike"],
            ['vehicle_type' => "Tractor"],
            ['vehicle_type' => "Taxi"],
            ['vehicle_type' => "JCB"],
            ['vehicle_type' => "Lifter"],
            ['vehicle_type' => "Person"],
            ['vehicle_type' => "Loader"],
            ['vehicle_type' => "Tanker"],
            ['vehicle_type' => "Marker"],
            ['vehicle_type' => "Pet"],
            ['vehicle_type' => "Ship"],
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

        DB::table('device_makes')->insert([
            ['device_make' => 'ACUTE140', 'status' => 1],
            ['device_make' => 'BENWAY', 'status' => 1],
            ['device_make' => 'COBAN', 'status' => 1],
            ['device_make' => 'CONCOX', 'status' => 1],
            ['device_make' => 'CONCOX-9001', 'status' => 1],
            ['device_make' => 'ET301', 'status' => 1],
            ['device_make' => 'G05', 'status' => 1],
            ['device_make' => 'GEOSAT NEO', 'status' => 1],
            ['device_make' => 'MT02', 'status' => 1],
            ['device_make' => 'Siwi', 'status' => 1],
            ['device_make' => 'Teltonika', 'status' => 1],
            ['device_make' => 'TK103', 'status' => 1],
            ['device_make' => 'VOLTY_Z1', 'status' => 1],
            ['device_make' => 'Zhongxun-[ID Card]', 'status' => 1],
        ]);

        DB::table('device_models')->insert([
            ['make_id' => '1', 'device_model' => 'AIS-140 IRNSS', 'status' => 1],
            ['make_id' => '2', 'device_model' => 'BW08', 'status' => 1],
            ['make_id' => '2', 'device_model' => 'ET300', 'status' => 1],
            ['make_id' => '2', 'device_model' => 'BW09', 'status' => 1],
            ['make_id' => '3', 'device_model' => 'COBAN', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'CONCOX', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'Magnet Tracker', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'VT05C', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'RP05/RP05E/RP-SUPERIOR', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'VT08S', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'VT03E', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'RDM AIS140', 'status' => 1],
            ['make_id' => '4', 'device_model' => 'AT4', 'status' => 1],
            ['make_id' => '5', 'device_model' => 'CONCOX-9001', 'status' => 1],
            ['make_id' => '6', 'device_model' => 'ET301', 'status' => 1],
            ['make_id' => '7', 'device_model' => 'G05', 'status' => 1],
            ['make_id' => '8', 'device_model' => 'GEOSAT NEO', 'status' => 1],
            ['make_id' => '9', 'device_model' => 'MT02', 'status' => 1],
            ['make_id' => '10', 'device_model' => 'AC104', 'status' => 1],
            ['make_id' => '10', 'device_model' => 'AC106', 'status' => 1],
            ['make_id' => '10', 'device_model' => 'AC109', 'status' => 1],
            ['make_id' => '10', 'device_model' => 'AC109T', 'status' => 1],
            ['make_id' => '11', 'device_model' => 'Teltonika', 'status' => 1],
            ['make_id' => '12', 'device_model' => 'TK103', 'status' => 1],
            ['make_id' => '14', 'device_model' => 'Zhongxun-[ID Card]', 'status' => 1],
            ['make_id' => '13', 'device_model' => 'VOLTY_Z1', 'status' => 1],
        ]);
    }
}
