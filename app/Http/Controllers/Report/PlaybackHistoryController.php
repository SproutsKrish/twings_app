<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PlayBackHistory;
use Illuminate\Http\Request;

class PlaybackHistoryController extends BaseController
{
    public function get_playback_report(Request $request)
    {
        $playbackReports = PlayBackHistory::whereBetween('created_at', [$request->input('start_day'), $request->input('end_day')])
            ->where('deviceimei', $request->input('deviceimei'))
            ->get();

        if ($playbackReports->isEmpty()) {
            return $this->sendError('No PlayBack Data Found');
        }

        return $this->sendSuccess($playbackReports);
    }
}
