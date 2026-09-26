<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InventoryReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $report,
        public string $frequency,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject(ucfirst($this->frequency) . ' inventory report - ' . $this->report['period'])
            ->view('emails.inventory-report');
    }
}
