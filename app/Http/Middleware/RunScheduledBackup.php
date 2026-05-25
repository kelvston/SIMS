<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RunScheduledBackup
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $settings = Setting::whereIn('key', [
                'backup_enabled',
                'backup_frequency',
                'backup_time',
                'backup_last_run',
            ])->pluck('value', 'key');

            // Skip if backup is disabled
            if (($settings['backup_enabled'] ?? '0') !== '1') {
                return $next($request);
            }

            $now       = Carbon::now();
            $lastRun   = $settings['backup_last_run'] ?? null;
            $frequency = $settings['backup_frequency'] ?? 'daily';
            $time      = $settings['backup_time'] ?? '00:00';

            [$hour, $minute] = explode(':', $time);

            // Check if it's the right time
            $isCorrectTime = (int)$now->hour === (int)$hour;

            if (!$isCorrectTime) {
                return $next($request);
            }

            // Check if enough time has passed since last run
            $shouldRun = false;

            if (!$lastRun) {
                $shouldRun = true;
            } else {
                $last = Carbon::parse($lastRun);
                $shouldRun = match($frequency) {
                    'daily'   => $now->diffInHours($last) >= 24,
                    'weekly'  => $now->diffInDays($last) >= 7,
                    'monthly' => $now->diffInDays($last) >= 30,
                    default   => false,
                };
            }

            if (!$shouldRun) {
                return $next($request);
            }

            // Run the backup
            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return $next($request);
            }

            $orgName  = Setting::where('key', 'organization_name')->first()->value ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-auto-backup-' . $now->format('Y-m-d_H-i-s') . '.sqlite';

            // Create backups folder if not exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Copy SQLite file
            copy($dbPath, storage_path('app/backups/' . $fileName));

            // Save last run time and flag for user notification
            Setting::updateOrCreate(['key' => 'backup_last_run'],          ['value' => $now->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'backup_last_file'],         ['value' => $fileName]);
            Setting::updateOrCreate(['key' => 'backup_pending_download'],  ['value' => '1']);

        } catch (\Exception $e) {
            // Silently fail — never break the app because of backup
        }

        return $next($request);
    }
}
