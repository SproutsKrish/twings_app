<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetDatabaseConnectionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    // public function handle(Request $request, Closure $next)
    // {
    //     return $next($request);
    // }

    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->role_id == 6) {
            $tenantData = json_decode(Tenant::find($user->client_id), true);
            $tenantDbName = $tenantData['tenancy_db_name'];
            // dd($tenantDbName);

            // Assuming the tenant model has a `tenancy_db_name` attribute to get the tenant's database name
            // $tenantDbName = $tenant->tenancy_db_name;

            // Switch the database connection to the tenant's database
            config([
                'database.connections.mysql.database' => $tenantDbName,
            ]);

            // Purge the connection, reconnect, and set the default connection to the tenant's database
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
        } else {
            $tenantData = json_decode(Tenant::find($request->client_id), true);
            $tenantDbName = $tenantData['tenancy_db_name'];
            // dd($tenantDbName);

            // Assuming the tenant model has a `tenancy_db_name` attribute to get the tenant's database name
            // $tenantDbName = $tenant->tenancy_db_name;

            // Switch the database connection to the tenant's database
            config([
                'database.connections.mysql.database' => $tenantDbName,
            ]);

            // Purge the connection, reconnect, and set the default connection to the tenant's database
            DB::purge('mysql');
            DB::reconnect('mysql');
            DB::setDefaultConnection('mysql');
        }

        return $next($request);
    }
}
