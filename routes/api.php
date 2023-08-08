<?php

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
use App\Http\Controllers\DemoReportController;
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
use App\Http\Controllers\Stock\DeviceCategoryController;
use App\Http\Controllers\Stock\DeviceModelController;
use App\Http\Controllers\Stock\DeviceTypeController;
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
use App\Http\Controllers\Report\IdleReportController;
use App\Http\Controllers\Report\KeyOnKeyOffReportController;
use App\Http\Controllers\Report\LiveDataController;
use App\Http\Controllers\Report\ParkingReportController;
use App\Http\Controllers\Report\PlaybackHistoryController;
use App\Http\Controllers\VehicleSetting\ConfigurationController;
use App\Http\Controllers\VehicleSetting\NotificationController;

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


Route::controller(LoginController::class)->group(function () {
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::middleware('switch.database')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::post('user/store', 'store');
            Route::get('user', 'index');
            Route::get('user/show/{id}', 'show');
            Route::put('user/update/{id}', 'update');
            Route::delete('user/delete/{id}', 'destroy');
            Route::get('user/details', 'showdetails');
        });
        Route::controller(LiveDataController::class)->group(function () {
            Route::get('multi_dashboard', 'multi_dashboard');
            Route::get('single_dashboard/{id}', 'single_dashboard');
            Route::get('vehicle_count', 'vehicle_count');
        });
        Route::controller(KeyOnKeyOffReportController::class)->group(function () {
            Route::post('get_keyonoff_report', 'get_keyonoff_report');
        });
        Route::controller(IdleReportController::class)->group(function () {
            Route::post('get_idle_report', 'get_idle_report');
        });
        Route::controller(ParkingReportController::class)->group(function () {
            Route::post('get_parking_report', 'get_parking_report');
        });
        Route::controller(PlaybackHistoryController::class)->group(function () {
            Route::post('get_playback_report', 'get_playback_report');
        });
        Route::controller(NotificationController::class)->group(function () {
            Route::get('notify/show', 'show');
            Route::put('notify/update/{id}', 'update');
        });
        Route::controller(ConfigurationController::class)->group(function () {
            Route::get('config/show', 'show');
            Route::put('config/update/{id}', 'update');
        });
    });

    Route::controller(LoginController::class)->group(function () {
        Route::post('logout', 'logout');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::resource('country', CountryController::class);
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
        Route::resource('role_right', RoleRightsController::class);
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

Route::resource('admin', AdminController::class);
Route::resource('distributor', DistributorController::class);
Route::resource('dealer', DealerController::class);
Route::resource('subdealer', SubDealerController::class);
Route::resource('client', ClientController::class);
Route::resource('vehicle_owner', VehicleOwnerController::class);

Route::resource('network', NetworkProviderController::class);
Route::resource('supplier', SupplierController::class);

Route::resource('device_type', DeviceTypeController::class);
Route::resource('device_category', DeviceCategoryController::class);
Route::resource('device_model', DeviceModelController::class);

Route::resource('camera_type', CameraTypeController::class);
Route::resource('camera_category', CameraCategoryController::class);
Route::resource('camera_model', CameraModelController::class);

Route::resource('sim', SimController::class);
Route::resource('device', DeviceController::class);
Route::resource('camera', CameraController::class);

Route::resource('vehicle', VehicleController::class);
Route::resource('vehicle_document', VehicleDocumentController::class);
Route::resource('vehicle_service', VehicleServiceController::class);
Route::resource('vehicle_type', VehicleTypeController::class);

Route::resource('point_type', PointTypeController::class);
Route::resource('point', PointController::class);
Route::resource('license', LicenseController::class);

Route::resource('feature', FeatureController::class);
Route::resource('package', PackageController::class);
Route::resource('period', PeriodController::class);
Route::resource('plan', PlanController::class);

Route::resource('module', ModuleController::class);
Route::resource('parent_menu', ParentMenuController::class);
Route::resource('child_menu', ChildMenuController::class);

Route::get('greeting', [LanguageController::class, 'index'])
    ->middleware('localization');


Route::post('sim_import', [ImportController::class, 'sim_import']);
Route::post('device_import', [ImportController::class, 'device_import']);
Route::post('camera_import', [ImportController::class, 'camera_import']);

Route::post('recharge', [RechargeController::class, 'recharge']);


Route::put('sim_assign/{id}', [SimController::class, 'sim_assign']);
Route::put('device_assign/{id}', [DeviceController::class, 'device_assign']);
