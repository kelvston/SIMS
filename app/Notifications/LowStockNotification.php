<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\VonageMessage; // For SMS (formerly Nexmo)
// use NotificationChannels\Twilio\TwilioSmsMessage; // If using Twilio directly

class LowStockNotification extends Notification
{
    use Queueable;

    protected $stockLevel;

    /**
     * Create a new notification instance.
     */
    public function __construct($stockLevel)
    {
        $this->stockLevel = $stockLevel;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // You can send via multiple channels, e.g., ['mail', 'vonage'] or ['mail', TwilioChannel::class]
        // For SMS, we'll use 'vonage' (formerly Nexmo) or 'twilio' if you install the Twilio package.
        // For this example, we'll assume Vonage (formerly Nexmo) is configured.
        return ['vonage'];
    }

    /**
     * Get the Vonage / Nexmo SMS representation of the notification.
     */
    public function toVonage(object $notifiable): VonageMessage
    {
        $productName = $this->stockLevel->product->name ?? 'Unknown Product';
        $message = "Low Stock Alert! Medicine: {$productName}. Current stock: {$this->stockLevel->current_stock}. Threshold: {$this->stockLevel->low_stock_threshold}.";

        return (new VonageMessage())
            ->content($message);
    }

    /*
    // Example if you're using the Twilio notification channel package:
    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        $productName = $this->stockLevel->product->name ?? 'Unknown Product';
        $message = "Low Stock Alert! Medicine: {$productName}. Current stock: {$this->stockLevel->current_stock}. Threshold: {$this->stockLevel->low_stock_threshold}.";

        return (new TwilioSmsMessage())
                    ->content($message);
    }
    */

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'stock_level_id' => $this->stockLevel->id,
            'product' => $this->stockLevel->product->name ?? 'N/A',
            'current_stock' => $this->stockLevel->current_stock,
            'threshold' => $this->stockLevel->low_stock_threshold,
            'message' => "Low Stock Alert for " . ($this->stockLevel->product->name ?? 'Unknown Product'),
        ];
    }
}
