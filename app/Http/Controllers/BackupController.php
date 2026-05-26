<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\Setting;
use Illuminate\Http\Request;

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

            $orgName  = Setting::where('key', 'organization_name')->value('value') ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $backupPath = storage_path('app/backups/' . $fileName);
            copy($dbPath, $backupPath);

            Setting::updateOrCreate(['key' => 'backup_last_run'],  ['value' => now()->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'backup_last_file'], ['value' => $fileName]);
            Setting::updateOrCreate(['key' => 'backup_last_path'], ['value' => $backupPath]);

            return redirect()->route('settings.edit')->with('success', 'Backup completed. File: ' . $fileName);

        } catch (\Exception $e) {
            return redirect()->route('settings.edit')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Run backup via AJAX (called from JS button)
     */
    public function runNowAjax(Request $request)
    {
        try {
            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return response()->json(['success' => false, 'message' => 'Database file not found.']);
            }

            $orgName  = Setting::where('key', 'organization_name')->value('value') ?? 'backup';
            $orgName  = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
            $fileName = $orgName . '-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            $backupPath = storage_path('app/backups/' . $fileName);
            copy($dbPath, $backupPath);

            Setting::updateOrCreate(['key' => 'backup_last_run'],  ['value' => now()->toDateTimeString()]);
            Setting::updateOrCreate(['key' => 'backup_last_file'], ['value' => $fileName]);
            Setting::updateOrCreate(['key' => 'backup_last_path'], ['value' => $backupPath]);
            if ($request->boolean('automated')) {
                Setting::updateOrCreate(['key' => 'backup_last_auto_run'], ['value' => now()->toDateTimeString()]);
            }

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
            $lastFile = Setting::where('key', 'backup_last_file')->value('value');
            $lastPath = Setting::where('key', 'backup_last_path')->value('value');
            $backupPath = $lastPath ?: ($lastFile ? storage_path('app/backups/' . $lastFile) : null);

            if ($backupPath && file_exists($backupPath)) {
                $downloadName = $lastFile ?: basename($backupPath);

                Setting::updateOrCreate(
                    ['key' => 'backup_last_download'],
                    ['value' => now()->toDateTimeString()]
                );
                Setting::updateOrCreate(['key' => 'backup_pending_download'], ['value' => '0']);

                return response()->download($backupPath, $downloadName, [
                    'Content-Type'        => 'application/octet-stream',
                    'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                    'Content-Length'      => filesize($backupPath),
                    'Cache-Control'       => 'no-cache, no-store, must-revalidate',
                    'Pragma'              => 'no-cache',
                    'Expires'             => '0',
                ]);
            }

            $dbPath = config('database.connections.sqlite.database');

            if (!str_starts_with($dbPath, '/')) {
                $dbPath = database_path($dbPath);
            }

            if (!file_exists($dbPath)) {
                return redirect()->route('settings.edit')->with('error', 'Database file not found at: ' . $dbPath);
            }

            $orgName  = Setting::where('key', 'organization_name')->value('value') ?? 'backup';
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
