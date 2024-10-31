<?php

namespace Tuijncode\LaravelVersion\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        // Composer

        $fault = false;

        try {
            $dependencies = $this->getComposerDependencies();
        } catch (Exception $e) {
            $fault = true;
            $composer = [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ];
        }

        if ($fault == false) {
            $composer = [
                'status' => 'OK',
                'dependencies' => $dependencies,
            ];
        }

        // Npm

        $fault = false;

        try {
            $dependencies = $this->getNpmDependencies();
        } catch (Exception $e) {
            $fault = true;
            $npm = [
                'status' => 'ERROR',
                'message' => $e->getMessage(),
            ];
        }

        if ($fault == false) {
            $npm = [
                'status' => 'OK',
                'dependencies' => $dependencies,
            ];
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
                ],
                'composer' => [
                    'response' => $composer,
                ],
                'npm' => [
                    'response' => $npm,
                ],
            ],
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

    /**
     * Get Composer Dependencies.
     */
    public function getComposerDependencies()
    {
        $file = base_path('composer.json');

        if (! file_exists($file)) {
            throw new Exception('File composer.json json found.');
        }

        $data = json_decode(file_get_contents($file), true);

        if (! isset($data['require'])) {
            throw new Exception('Invalid composer.json format.');
        }

        $dependencies = [];

        foreach (['require-dev', 'require'] as $type) {
            if (! array_key_exists($type, $data)) {
                continue;
            }

            foreach ($data[$type] as $name => $version) {
                $dependencies[$name] = [
                    'version' => $version,
                    'type' => $type,
                ];
            }
        }

        return $dependencies;
    }

    /**
     * Get Npm Dependencies.
     */
    public function getNpmDependencies()
    {
        $path = base_path('package.json');

        if (! file_exists($path)) {
            throw new Exception('File package.json not found.');
        }

        $data = json_decode(file_get_contents($path), true);

        if (! isset($data['dependencies'])) {
            throw new Exception('Invalid package.json format.');
        }

        foreach (['devDependencies', 'dependencies'] as $type) {
            if (! array_key_exists($type, $data)) {
                continue;
            }

            foreach ($data[$type] as $name => $version) {
                $dependencies[$name] = [
                    'version' => $version,
                    'type' => $type,
                ];
            }
        }

        return $dependencies;
    }
}
