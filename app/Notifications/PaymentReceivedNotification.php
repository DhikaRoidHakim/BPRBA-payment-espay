<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class PaymentReceivedNotification extends Notification
{
    public $transaction;

    /**
     * Create a new notification instance.
     */
    public function __construct($transaction)
    {
        //
        $this->transaction = $transaction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        // database untuk history, broadcast untuk realtime
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'trx_id' => $this->transaction->trx_id,
            'va_number' => $this->transaction->va_number,
            'amount' => $this->transaction->paid_amount,
            'message' => 'Pembayaran diterima',
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'trx_id' => $this->transaction->trx_id,
            'message' => 'Pembayaran diterima',
            'type' => 'payment_received',
        ]);
    }
}
