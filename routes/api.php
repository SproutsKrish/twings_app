<?php

use App\Http\Controllers\API\AddressController;
use App\Http\Controllers\API\AlertTypeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleHasPermissionController;
use App\Http\Controllers\API\ModelHasRoleController;
use App\Http\Controllers\API\ModelHasPermissionController;
use App\Http\Controllers\API\ModuleController;
use App\Http\Controllers\API\ParentMenuController;
use App\Http\Controllers\API\ChildMenuController;
use App\Http\Controllers\API\LanguageController;
use App\Http\Controllers\API\ImportController;
use App\Http\Controllers\API\CountryController;
use App\Http\Controllers\API\RoleRightsController;

use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\DistributorController;
use App\Http\Controllers\User\DealerController;
use App\Http\Controllers\User\SubDealerController;
use App\Http\Controllers\User\ClientController;
use App\Http\Controllers\User\VehicleOwnerController;
use App\Http\Controllers\User\UserController;

use App\Http\Controllers\Stock\NetworkProviderController;
use App\Http\Controllers\Stock\SupplierController;
use App\Http\Controllers\Stock\CameraCategoryController;
use App\Http\Controllers\Stock\CameraModelController;
use App\Http\Controllers\Stock\CameraTypeController;
use App\Http\Controllers\Stock\DeviceModelController;
use App\Http\Controllers\Stock\SimController;
use App\Http\Controllers\Stock\DeviceController;
use App\Http\Controllers\Stock\CameraController;

use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Vehicle\VehicleDocumentController;
use App\Http\Controllers\Vehicle\VehicleServiceController;
use App\Http\Controllers\Vehicle\VehicleTypeController;

use App\Http\Controllers\License\PointTypeController;
use App\Http\Controllers\License\PointController;
use App\Http\Controllers\License\LicenseController;
use App\Http\Controllers\License\FeatureController;
use App\Http\Controllers\License\PackageController;
use App\Http\Controllers\License\PeriodController;
use App\Http\Controllers\License\PlanController;
use App\Http\Controllers\License\RechargeController;

use App\Http\Controllers\Report\AcReportController;
use App\Http\Controllers\Report\AlertReportController;
use App\Http\Controllers\Report\AssignGeofenceController;
use App\Http\Controllers\Report\DistanceReportController;
use App\Http\Controllers\Report\GeofenceController;
use App\Http\Controllers\Report\GeofenceReportController;
use App\Http\Controllers\Report\IdleReportController;
use App\Http\Controllers\Report\KeyOnKeyOffReportController;
use App\Http\Controllers\Report\LiveDataController;
use App\Http\Controllers\Report\OverSpeedReportController;
use App\Http\Controllers\Report\ParkingReportController;
use App\Http\Controllers\Report\PlaybackReportController;
use App\Http\Controllers\Report\RoutedeviationReportController;
use App\Http\Controllers\Report\TemperatureReportController;
use App\Http\Controllers\Report\TripPlanReportController;
use App\Http\Controllers\Stock\DeviceMakeController;
use App\Http\Controllers\UserDomainController;
use App\Http\Controllers\VehicleSetting\AlertNotificationController;
use App\Http\Controllers\VehicleSetting\ConfigurationController;
use App\Http\Controllers\VehicleSetting\NotificationController;
use App\Http\Controllers\VehicleSetting\ShareLinkController;
use App\Http\Controllers\ExecutiveReportController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\HourMeterReportController;
use App\Http\Controllers\OnlineController;
use App\Http\Controllers\Report\ExecutiveReportCheckController;
use App\Http\Controllers\Report\VehicleHistoryController;
use App\Http\Controllers\RpmController;
use App\Http\Controllers\SmartReportController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::controller(VehicleController::class)->group(function () {
//     Route::get('vehicle_list', 'index');
// });

Route::resource('country', CountryController::class);
Route::resource('vehicle_type', VehicleTypeController::class);

Route::controller(OnlineController::class)->group(function () {
    Route::post('online_user_save', 'store');
    Route::post('online_vehicle_save', 'vehicle_store');
    Route::post('validate_user_details', 'validate_user_details');
});

Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'login');
});

Route::controller(UserController::class)->group(function () {
    Route::post('mymethod', 'mymethod');
});

Route::controller(LiveDataController::class)->group(function () {
    Route::post('today_distance', 'today_distance');
});


Route::post('admin_import', [ImportController::class, 'admin_import']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('date_import', [ImportController::class, 'date_import']);

    Route::post('admin_import', [ImportController::class, 'admin_import']);
    Route::post('distributor_import', [ImportController::class, 'distributor_import']);
    Route::post('dealer_import', [ImportController::class, 'dealer_import']);
    Route::post('user_import', [ImportController::class, 'user_import']);
    Route::post('vehicle_import', [ImportController::class, 'vehicle_import']);
    Route::get('about_us', [LoginController::class, 'about_us']);

    Route::post('change_user_password', [UserController::class, 'change_user_password']);
    Route::post('change_password', [UserController::class, 'change_password']);

    Route::post('login_image_save', [UserDomainController::class, 'login_image_save']);

    Route::post('change_sim', [VehicleController::class, 'change_sim']);
    Route::post('change_device', [VehicleController::class, 'change_device']);

    Route::resource('plan', PlanController::class);

    Route::controller(SimController::class)->group(function () {
        Route::post('sim/store', 'store');
        Route::post('sim_store', 'sim_store');
        Route::post('sim_transfer', 'sim_transfer');
        Route::post('sim_list', 'sim_list');
        Route::post('sim_stock_list', 'sim_stock_list');
        Route::post('sim/update', 'update');
        Route::post('sim/delete', 'destroy');
        Route::get('sim/{id}', 'show');
    });

    Route::controller(DeviceController::class)->group(function () {
        Route::post('device/store', 'store');
        Route::post('device_store', 'device_store');
        Route::post('device_transfer', 'device_transfer');
        Route::post('device_list', 'device_list');
        Route::post('device_stock_list', 'device_stock_list');
        Route::post('device/update', 'update');
        Route::post('delete/delete', 'destroy');
        Route::get('device/{id}', 'show');
    });





    Route::controller(AlertTypeController::class)->group(function () {
        Route::get('get_alert_list', 'get_alert_list');
    });


    Route::controller(UserController::class)->group(function () {
        Route::get('user/yourMethod', 'yourMethod');
    });
    Route::controller(LiveDataController::class)->group(function () {
        Route::post('role_based_vehicle_count', 'role_based_vehicle_count');
    });

    Route::controller(UserController::class)->group(function () {
        Route::post('role_based_user_list', 'role_based_user_list');
        Route::post('user_point_list', 'user_point_list');

        Route::post('user/store', 'store');
        Route::post('user/update', 'update');
        Route::delete('user/delete/{id}', 'destroy');

        Route::get('user', 'index');
        Route::get('user/show/{id}', 'show');
        Route::get('user/details', 'showdetails');
    });

    Route::post('user_list', [UserController::class, 'user_list']);


    Route::post('role_rights_list', [RoleRightsController::class, 'role_rights_list']);
    Route::post('point_stock_list', [PointController::class, 'point_stock_list']);

    Route::controller(LicenseController::class)->group(function () {
        Route::post('user_license_list', 'user_license_list');
    });

    Route::controller(PlanController::class)->group(function () {
        Route::post('user_plan_list', 'user_plan_list');
    });



    Route::controller(ExecutiveReportCheckController::class)->group(function () {
        Route::post('executive_report_check_list', 'executive_report_check_list');
        Route::post('executive_report_check_update', 'executive_report_check_update');
    });




    Route::controller(ShareLinkController::class)->group(function () {
        Route::post('link_list', 'link_list');
        Route::post('link_save', 'link_save');
        Route::get('link_show/{id}', 'link_show');
        Route::delete('link_delete/{id}', 'link_delete');

        Route::post('share_link_list', 'share_link_list');
        Route::post('share_link_save', 'share_link_save');
        Route::get('share_link_show/{id}', 'share_link_show');
        Route::delete('share_link_delete/{id}', 'share_link_delete');
    });

    Route::middleware('switch.database')->group(function () {
        Route::post('dist_rpt_1', [DistanceReportController::class, 'dist_rpt_1']);
        Route::post('distance_summary', [DistanceReportController::class, 'distance_summary']);


        Route::controller(LiveDataController::class)->group(function () {
            Route::get('multi_dashboard', 'multi_dashboard');
            Route::get('single_dashboard/{id}', 'single_dashboard');
            Route::get('vehicle_count', 'vehicle_count');
        });

        Route::controller(IdleReportController::class)->group(function () {
            Route::post('get_idle_report', 'get_idle_report');
        });
        Route::controller(ParkingReportController::class)->group(function () {
            Route::post('get_parking_report', 'get_parking_report');
        });
        Route::controller(KeyOnKeyOffReportController::class)->group(function () {
            Route::post('get_keyonoff_report', 'get_keyonoff_report');
        });
        Route::controller(PlaybackReportController::class)->group(function () {
            Route::post('get_playback_report', 'get_playback_report');
        });
        Route::controller(DistanceReportController::class)->group(function () {
            Route::post('get_distance_report', 'get_distance_report');
        });
        Route::controller(RoutedeviationReportController::class)->group(function () {
            Route::post('route_deviation_report', 'route_deviation_report');
        });
        Route::controller(OverSpeedReportController::class)->group(function () {
            Route::post('speed_report', 'speed_report');
        });
        Route::controller(GeofenceReportController::class)->group(function () {
            Route::post('geofence_report', 'geofence_report');
        });
        Route::controller(TripPlanReportController::class)->group(function () {
            Route::post('trip_plan_report', 'trip_plan_report');
        });
        Route::controller(AcReportController::class)->group(function () {
            Route::post('ac_report', 'ac_report');
        });
        Route::controller(ExecutiveReportController::class)->group(function () {
            Route::post('executive_report', 'index');
            Route::post('executive_summary', 'executive_summary');
        });

        Route::controller(TemperatureReportController::class)->group(function () {
            Route::post('temperature_report', 'temperature_report');
        });
        Route::controller(VehicleController::class)->group(function () {
            Route::put('change_vehicletype/{id}', 'change_vehicletype');
        });
        Route::resource('geo_fence', GeofenceController::class);
        Route::resource('assign_geo_fence', AssignGeofenceController::class);
        Route::resource('trip_plan', TripPlanReportController::class);

        Route::controller(NotificationController::class)->group(function () {
            Route::get('notify/show', 'show');
            Route::put('notify/update/{id}', 'update');
        });

        Route::controller(VehicleHistoryController::class)->group(function () {
            Route::post('vehicle_history_details', 'vehicle_history_details');
        });

        Route::controller(ConfigurationController::class)->group(function () {
            Route::get('config/show', 'show');
            Route::put('config/update/{id}', 'update');
            Route::put('config/immobilizer_option/{id}', 'immobilizer_option');
            Route::put('config/safe_parking/{id}', 'safe_parking');
            Route::put('config/odometer_update/{id}', 'odometer_update');
            Route::put('config/speed_update/{id}', 'speed_update');
        });

        Route::controller(AlertNotificationController::class)->group(function () {
            Route::post('alert_notifications_list', 'alert_notifications_list');
            Route::post('alert_notification/store', 'store');
            Route::post('alert_notification/update', 'update');
        });
        Route::controller(AlertReportController::class)->group(function () {
            Route::post('all_alert', 'all_alert');
            Route::post('device_alert', 'device_alert');
        });

        Route::controller(AssignGeofenceController::class)->group(function () {
            Route::post('assigned_fence_list', 'assigned_fence_list');
            Route::get('assign_geofencelist/{geofenceId}', 'assign_geofencelist');
            Route::get('geofence_not_assign_vehicles', 'geofence_not_assign_vehicles');
            Route::put('notify/update/{id}', 'update');

            Route::post('geofence_assign_vehicle', 'geofence_assign_vehicle');
            Route::post('geofence_not_assign_vehicle', 'geofence_not_assign_vehicle');
        });
        // Route::resource('executive_report', ExecutiveReportController::class);
        Route::resource('smart_report', SmartReportController::class);
        Route::resource('fuel_fill_dip_report', FuelController::class);
        Route::resource('rpm_controller', RpmController::class);
        Route::resource('hour_meter_report', HourMeterReportController::class);
    });

    Route::controller(LoginController::class)->group(function () {
        Route::post('logout', 'logout');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('role_right', RoleRightsController::class);
        Route::resource('vehicle', VehicleController::class);

        Route::controller(ConfigurationController::class)->group(function () {
            Route::post('config/store', 'store');
            Route::post('config/show', 'show');
            Route::post('config/store_all', 'store_all');
        });

        Route::controller(LiveDataController::class)->group(function () {
            Route::post('client_multi_dashboard', 'client_multi_dashboard');
            Route::post('client_single_dashboard', 'client_single_dashboard');
            Route::post('client_vehicle_count', 'client_vehicle_count');
        });

        Route::controller(VehicleController::class)->group(function () {
            Route::post('customer_vehicle_update', 'customer_vehicle_update');
            Route::post('customer_vehicle_delete', 'customer_vehicle_delete');
        });
    });



    Route::group(['middleware' => ['auth', 'checkrole:4,5']], function () {
    });
});


//Role Has Permissions
Route::get('roles/permissions', [RoleHasPermissionController::class, 'index']);
Route::get('roles/permissions/{role}', [RoleHasPermissionController::class, 'show']);
Route::delete('roles/permissions/{role}', [RoleHasPermissionController::class, 'destroy']);
Route::post('roles/permissions', [RoleHasPermissionController::class, 'store']);

//Model Has Permissions
Route::get('users/permissions', [ModelHasPermissionController::class, 'index']);
Route::get('users/permissions/{id}', [ModelHasPermissionController::class, 'show']);
Route::delete('users/permissions/{id}', [ModelHasPermissionController::class, 'destroy']);
Route::post('users/permissions', [ModelHasPermissionController::class, 'store']);

//Model Has Roles
Route::get('users/roles', [ModelHasRoleController::class, 'index']);
Route::get('users/rolebyuser/{id}', [ModelHasRoleController::class, 'role_user']);
Route::get('users/usersbyrole/{id}', [ModelHasRoleController::class, 'users_role']);
Route::put('users/roleupdate/{id}', [ModelHasRoleController::class, 'user_role_update']);

Route::post('vehicle_list', [VehicleController::class, 'vehicle_list']);
Route::post('report_vehicle_list', [VehicleController::class, 'report_vehicle_list']);


Route::resource('admin', AdminController::class);
Route::resource('distributor', DistributorController::class);
Route::resource('dealer', DealerController::class);
Route::resource('subdealer', SubDealerController::class);
Route::resource('client', ClientController::class);
Route::resource('vehicle_owner', VehicleOwnerController::class);

Route::resource('network', NetworkProviderController::class);
Route::resource('supplier', SupplierController::class);

Route::resource('device_model', DeviceModelController::class);
Route::resource('device_make', DeviceMakeController::class);


Route::post('model_list', [DeviceModelController::class, 'model_list']);


Route::resource('camera_type', CameraTypeController::class);
Route::resource('camera_category', CameraCategoryController::class);
Route::resource('camera_model', CameraModelController::class);


Route::post('user/delete', [UserController::class, 'destroy']);

Route::resource('camera', CameraController::class);

Route::resource('vehicle_document', VehicleDocumentController::class);
Route::post('upload_vehicle_document', [VehicleDocumentController::class, 'upload_vehicle_document']);

Route::get('delete_dummy_data', [ImportController::class, 'delete_dummy_data']);

Route::resource('vehicle_service', VehicleServiceController::class);

Route::resource('point_type', PointTypeController::class);
Route::resource('point', PointController::class);
Route::resource('license', LicenseController::class);

Route::resource('feature', FeatureController::class);
Route::resource('package', PackageController::class);
Route::resource('period', PeriodController::class);

Route::resource('module', ModuleController::class);
Route::resource('parent_menu', ParentMenuController::class);
Route::resource('child_menu', ChildMenuController::class);

Route::post('sim_import', [ImportController::class, 'sim_import']);
Route::post('device_import', [ImportController::class, 'device_import']);
Route::post('camera_import', [ImportController::class, 'camera_import']);
Route::post('recharge', [RechargeController::class, 'recharge']);
Route::post('generate_fcm_token', [LoginController::class, 'generate_fcm_token']);

Route::post('plan_days', [PlanController::class, 'plan_days']);

Route::get('change_live_data', [VehicleController::class, 'change_live_data']);
Route::get('gen_pass', [UserController::class, 'gen_pass']);
Route::get('getData', [ImportController::class, 'getData']);



//App Contact
Route::get('contact_address/{id}', [ClientController::class, 'contact_address']);
Route::post('live_address', [AddressController::class, 'live_address']);

Route::get('demo_time', [LiveDataController::class, 'demo_time']);

Route::post('due_vehicle_list', [VehicleController::class, 'due_vehicle_list']);
Route::post('vehicle_due_update', [VehicleController::class, 'vehicle_due_update']);


//Not Use
Route::get('greeting', [LanguageController::class, 'index'])
    ->middleware('localization');

Route::post('vehicleimageUpload', [VehicleDocumentController::class, 'vehicleimageUpload']);
Route::post('vehicleimageRetrieve', [VehicleDocumentController::class, 'vehicleimageRetrieve']);

Route::post('demo_alert', [ImportController::class, 'demo_alert']);
