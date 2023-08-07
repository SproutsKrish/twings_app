<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Models\LiveData;
use Illuminate\Support\Facades\DB;

class LiveDataController extends BaseController
{
    public function multi_dashboard()
    {
        $results = DB::select("SELECT ranked.*, B.vehicle_name, B.vehicle_make, B.vehicle_model, B.vehicle_year, C.vehicle_type
        FROM (
            SELECT *,
                   ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn
            FROM live_data
        ) AS ranked INNER JOIN vehicles B on ranked.vehicle_id = b.id
        			INNER JOIN vehicle_types C on b.vehicle_type_id = c.id
        WHERE rn = 1
        ORDER BY ranked.id");


        if (!$results) {
            return $this->sendError('No Live Data Found');
        }

        return $this->sendSuccess($results);
    }

    public function single_dashboard($id)
    {
        $data['vehicle_details'] = DB::select("SELECT A.*, B.vehicle_name, B.vehicle_make, B.vehicle_model, B.vehicle_year, C.vehicle_type
        FROM `live_data` A INNER JOIN vehicles B on a.vehicle_id = b.id
        INNER JOIN vehicle_types C ON b.vehicle_type_id = c.id
        WHERE A.vehicle_id = $id ORDER BY id DESC LIMIT 1");
        $data['live_details'] = LiveData::select('lattitute', 'longitute', 'speed', 'angle')->where('vehicle_id', $id)->get();
        //$data['vehicle_latlng'] = DB::select("");
        //dd($data);
        if (!$data) {
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($data);
    }

    public function vehicle_count()
    {
        $moving = DB::select("
        SELECT COUNT(*) AS count
        FROM (
            SELECT *, ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn
            FROM live_data
            WHERE device_updatedtime < (DATE_SUB(NOW(), INTERVAL 10 MINUTE))
        ) AS ranked
        INNER JOIN vehicles AS B ON ranked.vehicle_id = B.id
        INNER JOIN vehicle_types AS C ON B.vehicle_type_id = C.id
        WHERE rn = 1 AND ignition = 1 AND speed > 0");

        $parking = DB::select("
        SELECT COUNT(*) AS count
        FROM (
            SELECT *, ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn
            FROM live_data
            WHERE device_updatedtime < (DATE_SUB(NOW(), INTERVAL 10 MINUTE))
        ) AS ranked
        INNER JOIN vehicles AS B ON ranked.vehicle_id = B.id
        INNER JOIN vehicle_types AS C ON B.vehicle_type_id = C.id
        WHERE rn = 1 AND ignition = 0 AND speed > 0");

        $idle = DB::select("
        SELECT COUNT(*) AS count
        FROM (
            SELECT *, ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY id DESC) AS rn
            FROM live_data
            WHERE device_updatedtime < (DATE_SUB(NOW(), INTERVAL 10 MINUTE))
        ) AS ranked
        INNER JOIN vehicles AS B ON ranked.vehicle_id = B.id
        INNER JOIN vehicle_types AS C ON B.vehicle_type_id = C.id
        WHERE rn = 1 AND ignition = 1 AND speed = 0");

        $all = DB::select("SELECT count(DISTINCT(ID)) as Total_vehicles FROM live_data");


        $no_network = $all[0]->Total_vehicles - ($moving[0]->count + $idle[0]->count + $parking[0]->count);

        $vehicle_count = array('moving' => $moving[0]->count, 'idle' => $idle[0]->count, 'parking' => $parking[0]->count, 'no_network' => $no_network, 'all' =>  $all[0]->Total_vehicles);

        if (!$vehicle_count) {
            return $this->sendError('Live Data Not Found');
        }

        return $this->sendSuccess($vehicle_count);
    }
}
