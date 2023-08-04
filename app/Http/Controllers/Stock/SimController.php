<?php

namespace App\Http\Controllers\Stock;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Validator;

use App\Models\Sim;


class SimController extends BaseController
{
    public function index()
    {
        $sims = Sim::all();

        if ($sims->isEmpty()) {
            return $this->sendError('No Sims Found');
        }

        return $this->sendSuccess($sims);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'network_id' => 'required|max:255',
            'sim_imei_no' => 'required|max:255',
            'sim_mob_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $sim = new Sim($request->all());
        if ($sim->save()) {
            return $this->sendSuccess("Sim Inserted Successfully");
        } else {
            return $this->sendError('Failed to Insert Sim');
        }
    }

    public function show($id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        return $this->sendSuccess($sim);
    }

    public function update(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        $validator = Validator::make($request->all(), [
            'network_id' => 'required|max:255',
            'sim_imei_no' => 'required|max:255',
            'sim_mob_no' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        if ($sim->update($request->all())) {
            return $this->sendSuccess("Sim Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Sim');
        }
    }

    public function destroy(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        $sim->status = 0;
        $sim->deleted_by = $request->deleted_by;
        $sim->save();
        if ($sim->delete()) {
            return $this->sendSuccess('Sim Deleted Successfully');
        } else {
            return $this->sendError('Failed to Delete Sim');
        }
    }

    public function sim_assign(Request $request, $id)
    {
        $sim = Sim::find($id);

        if (!$sim) {
            return $this->sendError('Sim Not Found');
        }

        if ($sim->update($request->all())) {
            return $this->sendSuccess("Sim Updated Successfully");
        } else {
            return $this->sendError('Failed to Update Sim');
        }
    }
}
