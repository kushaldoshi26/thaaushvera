<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SystemHealthService
{
    public function scan()
    {
        $health = [];

        try {
            DB::connection()->getPdo();
            $health['database'] = 'Connected';
        } catch (\Exception $e) {
            $health['database'] = 'Failed';
        }

        $logPath = storage_path('logs/laravel.log');
        if (file_exists($logPath)) {
            $logs = file_get_contents($logPath);
            $health['error_count'] = substr_count($logs, 'ERROR');
        }

        return $health;
    }
}
