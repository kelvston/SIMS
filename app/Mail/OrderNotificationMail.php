<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;

class OrderNotificationMail extends Mailable
{
    public function __construct(
        public Order $order,
        public string $event,
        public string $invoicePdf,
    ) {
    }

    public function build(): self
    {
        $subject = $this->event === 'confirmed'
            ? 'Your order has been confirmed: ' . $this->order->invoice_number
            : 'We received your order: ' . $this->order->invoice_number;

        return $this->subject($subject)
            ->view('emails.orders.notification')
            ->attachData($this->invoicePdf, $this->order->invoice_number . '.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
