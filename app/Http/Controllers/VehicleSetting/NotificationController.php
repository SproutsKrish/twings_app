<?php

namespace App\Http\Controllers\VehicleSetting;

use App\Http\Controllers\API\BaseController as BaseController;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function show(Request $request)
    {
        $notification = Notification::where('client_id', $request->input('client_id'))->get();

        if ($notification->isEmpty()) {
            Notification::create(['client_id' => $request->input('client_id')]);
            $notification = Notification::where('client_id', $request->input('client_id'))->get();
        }

        return $this->sendSuccess($notification);
    }

    public function update(Request $request, $id)
    {
        $notification = Notification::find($id);

        if (!$notification) {
            return $this->sendError('Notification Not Found');
        }

        if ($notification->update($request->all())) {
            return $this->sendSuccess("Notification Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Notification');
        }
    }
}
