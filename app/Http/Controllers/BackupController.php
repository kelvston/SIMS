<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class BackupController extends Controller
{
    /**
     * Run backup manually (page redirect)
     */
    public function runNow()
    {
        try {
            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return redirect()->route('settings.edit')->with('error', 'Database file not found at: ' . $dbPath);
            }

            $orgName  = Setting::where('key', 'organization_name')->first()->value ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            copy($dbPath, storage_path('app/backups/' . $fileName));

            Setting::updateOrCreate(['key' => 'backup_last_run'],  ['value' => now()->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'backup_last_file'], ['value' => $fileName]);

            return redirect()->route('settings.edit')->with('success', 'Backup completed. File: ' . $fileName);

        } catch (\Exception $e) {
            return redirect()->route('settings.edit')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Run backup via AJAX (called from JS button)
     */
    public function runNowAjax()
    {
        try {
            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return response()->json(['success' => false, 'message' => 'Database file not found.']);
            }

            $orgName  = Setting::where('key', 'organization_name')->first()->value ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            copy($dbPath, storage_path('app/backups/' . $fileName));

            Setting::updateOrCreate(['key' => 'backup_last_run'],  ['value' => now()->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'backup_last_file'], ['value' => $fileName]);

            return response()->json([
                'success'  => true,
                'message'  => 'Backup completed.',
                'fileName' => $fileName,
                'lastRun'  => now()->toDateTimeString(),
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Download the current SQLite database file
     */
    public function download()
    {
        try {
            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return redirect()->route('settings.edit')->with('error', 'Database file not found at: ' . $dbPath);
            }

            $orgName  = Setting::where('key', 'organization_name')->first()->value ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            Setting::updateOrCreate(
                ['key' => 'backup_last_download'],
                ['value' => now()->toDateTimeString()]
            );

            return response()->download($dbPath, $fileName, [
                'Content-Type'        => 'application/octet-stream',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length'      => filesize($dbPath),
                'Cache-Control'       => 'no-cache, no-store, must-revalidate',
                'Pragma'              => 'no-cache',
                'Expires'             => '0',
            ]);

        } catch (\Exception $e) {
            return redirect()->route('settings.edit')->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Dismiss the automated backup notification
     */
    public function dismissNotify()
    {
        try {
            Setting::updateOrCreate(
                ['key' => 'backup_pending_download'],
                ['value' => '0']
            );

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
