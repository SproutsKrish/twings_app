<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Sim;
use App\Models\Camera;
use App\Models\Device;
use App\Models\ModelHasRole;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
use App\Models\User;

class ImportController extends BaseController
{

    public function sim_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // network_id
                    1 => 'required|unique:sims,sim_imei_no', // sim_imei_no (unique in 'sims' table)
                    2 => 'required|unique:sims,sim_mob_no', // sim_mob_no (unique in 'sims' table)
                    3 => 'required', // valid_from
                    4 => 'required', // valid_to
                    5 => 'required', // purchase_date
                    6 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Sim::create([
                    'network_id' => $row[0],
                    'sim_imei_no' => $row[1],
                    'sim_mob_no' => $row[2],
                    'valid_from' => $row[3],
                    'valid_to' => $row[4],
                    'purchase_date' => $row[5],
                    'created_by' => $row[6]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Sim Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function device_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // supplier_id
                    1 => 'required', // device_type_id
                    2 => 'required', // device_category_id
                    3 => 'required', // device_model_id
                    4 => 'required|unique:devices,device_imei_no', // device_imei_no (unique in 'devices' table)
                    10 => 'required', // purchase_date
                    11 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Device::create([
                    'supplier_id' => $row[0],
                    'device_type_id' => $row[1],
                    'device_category_id' => $row[2],
                    'device_model_id' => $row[3],
                    'device_imei_no' => $row[4],
                    'ccid' => $row[5],
                    'uid' => $row[6],
                    'start_date' => $row[7],
                    'end_date' => $row[8],
                    'sensor_name' => $row[9],
                    'purchase_date' => $row[10],
                    'created_by' => $row[11]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Device Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }

    public function camera_import(Request $request)
    {
        $file_path = $request->input('file_path');
        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required', // supplier_id
                    1 => 'required', // camera_type_id
                    2 => 'required', // camera_category_id
                    3 => 'required', // camera_model_id
                    4 => 'required|unique:cameras,serial_no', // device_imei_no (unique in 'devices' table)
                    6 => 'required', // purchase_date
                    7 => 'required' // created_by
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                Camera::create([
                    'supplier_id' => $row[0],
                    'camera_type_id' => $row[1],
                    'camera_category_id' => $row[2],
                    'camera_model_id' => $row[3],
                    'serial_no' => $row[4],
                    'id_no' => $row[5],
                    'purchase_date' => $row[6],
                    'created_by' => $row[7]
                ]);
            }

            DB::commit();

            return $this->sendSuccess('Camera Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }


    public function user_import(Request $request)
    {
        $file_path = $request->input('file_path');

        if (!$file_path) {
            return $this->sendError("No File Path Provided");
        }

        $validator = Validator::make($request->all(), ['file_path' => 'required']);

        if ($validator->fails()) {
            return $this->sendError("Invalid File Format");
        }

        try {
            $path = $file_path;
            $data = array_map('str_getcsv', file($path));

            DB::beginTransaction();

            foreach ($data as $row) {
                $rowValidator = Validator::make($row, [
                    0 => 'required|unique:users,name', // name (unique in 'users' table)
                    1 => 'required|unique:users,email', // email (unique in 'users' table)
                    2 => 'required', // password
                    3 => 'required', // secondary_password
                    4 => 'required', // role_id
                ]);

                if ($rowValidator->fails()) {
                    DB::rollBack();
                    return $this->sendError($rowValidator->errors());
                }

                $user =  User::create([
                    'name' => $row[0],
                    'email' => $row[1],
                    'password' => bcrypt($row[2]),
                    'secondary_password' => bcrypt($row[3]),
                    'role_id' => $row[4]
                ]);

                $role_id = $row[4];
                $role = Role::find($role_id);
                $permissions = $role->permissions;
                $user->syncPermissions($permissions);

                $data['role_id']  = $row[4];
                $data['model_type'] = "App\Models\User";
                $data['model_id'] = $user->id;
                $model_has_role = new ModelHasRole($data);
                $model_has_role->save();

                if ($role_id == 6) {
                    $tenant = Tenant::create(['id' => $user->id]);
                    $tenant->domains()->create(['domain' => $user->name . '.' . 'localhost']);
                }
            }

            DB::commit();

            return $this->sendSuccess('User Imported Successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('An error occurred during CSV import: ' . $e->getMessage());
        }
    }
}
