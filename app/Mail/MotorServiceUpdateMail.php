<?php

namespace App\Mail;

use App\Models\MotorService;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MotorServiceUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public MotorService $service,
        public string $action,
    ) {
    }

    public function build(): self
    {
        $prefix = $this->action === 'created' ? 'Service job received' : 'Service job update';

        return $this->subject($prefix . ': ' . $this->service->job_number)
            ->view('emails.motor_services.update');
    }
}
