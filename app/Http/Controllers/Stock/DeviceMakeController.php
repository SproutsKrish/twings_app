<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\DeviceMake;
use Illuminate\Http\Request;

class DeviceMakeController extends BaseController
{

    public function index()
    {
        $device_makes = DeviceMake::all();

        if ($device_makes->isEmpty()) {
            return $this->sendError('No Device Makes Found');
        }

        return $this->sendSuccess($device_makes);
    }
}
