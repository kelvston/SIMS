<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class GeneralReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $reportData;
    public string $pdfContent;
    public string $filename;

    public function __construct(array $reportData, string $pdfContent, string $filename)
    {
        $this->reportData = $reportData;
        $this->pdfContent = $pdfContent;
        $this->filename   = $filename;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'General Business Report: ' . $this->reportData['startDate'] . ' to ' . $this->reportData['endDate'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.general_report',
            with: [
                'startDate'       => $this->reportData['startDate'],
                'endDate'         => $this->reportData['endDate'],
                'totalRevenue'    => $this->reportData['totalRevenue'],
                'netProfit'       => $this->reportData['netProfit'],
                'profitMargin'    => $this->reportData['profitMargin'],
                'totalExpenses'   => $this->reportData['totalExpenses'],
                'totalSalesCount' => $this->reportData['totalSalesCount'],
                'availablePhones' => $this->reportData['availablePhones'],
                'pendingInstallments' => $this->reportData['pendingInstallments'],
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->pdfContent, $this->filename)
                ->withMime('application/pdf'),
        ];
    }
}
