<?php

namespace App\Console\Commands;

use App\Mail\InventoryReportMail;
use App\Services\InventoryReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendInventoryReport extends Command
{
    protected $signature = 'reports:send-inventory {frequency : daily or weekly}';

    protected $description = 'Send a daily or weekly sales, stock received, and profit report.';

    public function handle(InventoryReportService $reportService): int
    {
        $frequency = strtolower((string) $this->argument('frequency'));

        if (! in_array($frequency, ['daily', 'weekly'], true)) {
            $this->error('Frequency must be daily or weekly.');

            return self::FAILURE;
        }

        $recipient = config('reports.inventory_recipient');

        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error('Set a valid INVENTORY_REPORT_RECIPIENT in the environment configuration.');

            return self::FAILURE;
        }

        $endDate = $frequency === 'daily'
            ? now()->subDay()->endOfDay()
            : now()->subWeek()->endOfWeek();
        $startDate = $frequency === 'daily'
            ? $endDate->copy()->startOfDay()
            : $endDate->copy()->startOfWeek();

        Mail::to($recipient)->send(new InventoryReportMail(
            $reportService->build($startDate, $endDate),
            $frequency,
        ));

        $this->info(ucfirst($frequency) . " inventory report sent to {$recipient}.");

        return self::SUCCESS;
    }
}
