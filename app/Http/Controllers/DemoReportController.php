<?php

namespace App\Http\Controllers;

use App\Models\DemoReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DemoReportController extends Controller
{

    public function index()
    {
        config([
            'database.connections.mysql.database' => 'twings_customer3',
        ]);

        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $reports = DemoReport::all();
        return json_encode($reports);
    }
}
