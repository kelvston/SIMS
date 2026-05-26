<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RunAutomatedBackup extends Command
{
    protected $signature = 'backup:automated {--force : Run without checking schedule}';

    protected $description = 'Run the configured automated SQLite backup when it is due.';

    public function handle(): int
    {
        $settings = Setting::whereIn('key', [
            'organization_name',
            'backup_enabled',
            'backup_frequency',
            'backup_time',
            'backup_last_auto_run',
            'backup_save_path',
            'backup_retention',
        ])->pluck('value', 'key');

        if (($settings['backup_enabled'] ?? '0') !== '1') {
            $this->info('Automated backup is disabled.');
            return self::SUCCESS;
        }

        $now = Carbon::now();

        if (! $this->option('force') && ! $this->backupIsDue($settings, $now)) {
            $this->info('Automated backup is not due.');
            return self::SUCCESS;
        }

        $dbPath = config('database.connections.sqlite.database');
        if (! str_starts_with($dbPath, '/')) {
            $dbPath = database_path($dbPath);
        }

        if (! File::exists($dbPath)) {
            $this->error('Database file not found at: ' . $dbPath);
            return self::FAILURE;
        }

        $destination = $this->backupDestination($settings['backup_save_path'] ?? null);
        if (! str_starts_with($destination, '/')) {
            $this->error('Backup save path must be a full server path, for example /home/yoga/Desktop/test.');
            return self::FAILURE;
        }

        if (! File::isDirectory($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        if (! File::isWritable($destination)) {
            $this->error('Backup folder is not writable: ' . $destination);
            return self::FAILURE;
        }

        $orgName = $settings['organization_name'] ?? 'backup';
        $orgName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $orgName);
        $fileName = $orgName . '-auto-backup-' . $now->format('Y-m-d_H-i-s') . '.sqlite';
        $targetPath = $destination . DIRECTORY_SEPARATOR . $fileName;

        File::copy($dbPath, $targetPath);

        Setting::updateOrCreate(['key' => 'backup_last_run'], ['value' => $now->toDateTimeString()]);
        Setting::updateOrCreate(['key' => 'backup_last_auto_run'], ['value' => $now->toDateTimeString()]);
        Setting::updateOrCreate(['key' => 'backup_last_file'], ['value' => $fileName]);
        Setting::updateOrCreate(['key' => 'backup_last_path'], ['value' => $targetPath]);
        Setting::updateOrCreate(['key' => 'backup_pending_download'], ['value' => '0']);

        $this->deleteExpiredBackups($destination, (int) ($settings['backup_retention'] ?? 7), $orgName);

        $this->info('Backup saved to: ' . $targetPath);
        return self::SUCCESS;
    }

    private function backupIsDue($settings, Carbon $now): bool
    {
        $time = $settings['backup_time'] ?? '00:00';
        [$hour, $minute] = array_pad(explode(':', $time), 2, 0);

        $scheduled = $now->copy()->setTime((int) $hour, (int) $minute);
        if ($now->lt($scheduled)) {
            return false;
        }

        $lastRun = $settings['backup_last_auto_run'] ?? null;
        if (! $lastRun) {
            return true;
        }

        $last = Carbon::parse($lastRun);

        return match ($settings['backup_frequency'] ?? 'daily') {
            'daily' => $last->lt($scheduled),
            'weekly' => $last->diffInDays($now) >= 7,
            'monthly' => $last->format('Y-m') !== $now->format('Y-m'),
            default => false,
        };
    }

    private function backupDestination(?string $path): string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return storage_path('app/backups');
        }

        if (str_starts_with($path, '~/')) {
            return rtrim((string) getenv('HOME'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . substr($path, 2);
        }

        return $path;
    }

    private function deleteExpiredBackups(string $destination, int $retentionDays, string $orgName): void
    {
        $cutoff = Carbon::now()->subDays(max(1, $retentionDays));

        foreach (File::files($destination) as $file) {
            if (! str_ends_with($file->getFilename(), '.sqlite')) {
                continue;
            }

            if (! str_starts_with($file->getFilename(), $orgName . '-auto-backup-')) {
                continue;
            }

            if (Carbon::createFromTimestamp($file->getMTime())->lt($cutoff)) {
                File::delete($file->getPathname());
            }
        }
    }
}
