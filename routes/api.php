<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\LoginController;
use App\Http\Controllers\API\ModelHasPermissionController;
use App\Http\Controllers\API\ModelHasRoleController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\RoleHasPermissionController;
use App\Http\Controllers\Country\CountryController;

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

use App\Http\Controllers\User\AdminController;
use App\Http\Controllers\User\DistributorController;
use App\Http\Controllers\User\DealerController;
use App\Http\Controllers\User\SubDealerController;
use App\Http\Controllers\User\ClientController;
use App\Http\Controllers\User\VehicleOwnerController;
use App\Http\Controllers\User\UserController;

use App\Http\Controllers\Vehicle\VehicleController;
use App\Http\Controllers\Vehicle\VehicleDocumentController;
use App\Http\Controllers\Vehicle\VehicleServiceController;

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
    Route::controller(UserController::class)->group(function () {
        Route::post('user/store', 'store');
        Route::get('user', 'index');
        Route::get('user/show/{id}', 'show');
        Route::put('user/update/{id}', 'update');
        Route::delete('user/delete/{id}', 'destroy');
        Route::get('user/details', 'showdetails');
        // Route::get('user/gett', 'gett');
    });
    Route::controller(LoginController::class)->group(function () {
        Route::post('logout', 'logout');
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::group(['middleware' => ['auth']], function () {
        Route::resource('country', CountryController::class);
        Route::resource('permission', PermissionController::class);
        Route::resource('role', RoleController::class);
    });
});

Route::get('roles/permissions', [RoleHasPermissionController::class, 'index']);
Route::get('roles/permissions/{role}', [RoleHasPermissionController::class, 'show']);
Route::delete('roles/permissions/{role}', [RoleHasPermissionController::class, 'destroy']);
Route::post('roles/permissions', [RoleHasPermissionController::class, 'store']);

Route::get('users/permissions', [ModelHasPermissionController::class, 'index']);
Route::get('users/permissions/{id}', [ModelHasPermissionController::class, 'show']);
Route::delete('users/permissions/{id}', [ModelHasPermissionController::class, 'destroy']);
Route::post('users/permissions', [ModelHasPermissionController::class, 'store']);

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
