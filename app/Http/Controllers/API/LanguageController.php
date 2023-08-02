<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;

class LanguageController extends BaseController
{
    public function index(Request $request)
    {
        $data = [
            'user_mgnt' => trans('messages.user_mgnt'),
            'stock_mgnt' => trans('messages.stock_mgnt'),
            'vehicle_mgnt' => trans('messages.vehicle_mgnt'),
            'license_mgnt' => trans('messages.license_mgnt')
        ];

        if (!$data) {
            return $this->sendError('Language Not Found');
        }

        return $this->sendSuccess($data);
    }
}
