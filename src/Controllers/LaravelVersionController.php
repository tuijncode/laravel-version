<?php

namespace Tuijncode\LaravelVersion\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class LaravelVersionController
{
    public function index(Request $request)
    {
        if (empty(config('laravel_version.token'))) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Token Is Not Configured Yet.',
            ], 400);
        }

        if (! $request->exists('token')) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Token Is Required.',
            ], 400);
        }

        if ($request->get('token') !== config('laravel_version.token')) {
            return response()->json([
                'status' => 'ERROR',
                'message' => 'Unauthorized.',
            ], 401);
        }

        return response()->json([
            'status' => 'OK',
            'versions' => [
                'webserver' => [
                    'name' => $this->getWebserverNAme(),
                    'version' => $this->getWebserverVersion(),
                ],
                'laravel' => [
                    'name' => config('app.name'),
                    'version' => app()->version(),
                ],
                'database' => [
                    'name' => DB::getDriverName(),
                    'version' => $this->getDatabaseVersion(),
                ],
                'php' => [
                    'name' => php_sapi_name(),
                    'version' => phpversion(),
                ]
            ]
        ]);
    }

    /**
     * Get Webserver Name.
     */
    public function getWebserverName()
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';

        if (strpos($serverSoftware, 'Apache') !== false) {
            return 'Apache';
        } elseif (strpos($serverSoftware, 'nginx') !== false) {
            return 'Nginx';
        } else {
            return 'N/A';
        }
    }

    /**
     * Get Webserver Version.
     */
    public function getWebserverVersion()
    {
        $serverSoftware = $_SERVER['SERVER_SOFTWARE'] ?? 'N/A';

        if (strpos($serverSoftware, 'Apache') !== false) {
            return function_exists('apache_get_version') ? apache_get_version() : $serverSoftware;
        } elseif (strpos($serverSoftware, 'nginx') !== false) {
            return $serverSoftware;
        } else {
            return $serverSoftware;
        }
    }

    /**
     * Get Database Version.
     */
    public function getDatabaseVersion()
    {
        $driver = DB::getDriverName();

        switch ($driver) {
            case 'mysql':
            case 'mariadb':
                return DB::scalar('SELECT VERSION()');
            case 'pgsql':
                return DB::scalar('SHOW server_version');
            case 'sqlite':
                return sqlite_libversion();
            case 'sqlsrv':
                return DB::scalar("SELECT SERVERPROPERTY('ProductVersion') as version");
            default:
                return 'N/A';
        }
    }
}
