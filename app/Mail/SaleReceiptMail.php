<?php

namespace App\Mail;

use App\Models\Sale;
use App\Models\SaleReceipt;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SaleReceiptMail extends Mailable
{
    public $sale;
    public $receipt;
    public $pdfContent;

    public function __construct($sale, $receipt, $pdfContent)
    {
        $this->sale = $sale;
        $this->receipt = $receipt;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->view('emails.sale.receipt')
        ->attachData($this->pdfContent, $this->receipt->receipt_number . '.pdf', [
            'mime' => 'application/pdf',
        ]);
    }
}





