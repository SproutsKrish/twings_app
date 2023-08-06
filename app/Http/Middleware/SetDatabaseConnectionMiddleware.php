<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetDatabaseConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    // public function handle($request, Closure $next)
    // {
    //     // Set the database connection based on the tenant's database name
    //     config(['database.connections.mysql.database' => 'twings_customer3']);

    //     // Reconnect to the new database
    //     DB::purge('mysql');
    //     DB::reconnect('mysql');

    //     return $next($request);
    // }
}
