<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SellingCryptoSuccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $sell)
    {
        $this->user = $user;
        $this->sell = $sell;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
        ->subject(round($this->sell->value, 10, PHP_ROUND_HALF_UP).
        $this->sell->cryptocurrency.' sold successfully')
        ->greeting('Hello '.$this->user->first_name.' '.$this->user->last_name)
        ->line('We are glad to inform you that your transaction for the selling of '.
        round($this->sell->value, 5, PHP_ROUND_HALF_UP).$this->sell->cryptocurrency.
        ' with a charge of '.round($this->sell->commission->value, 5, PHP_ROUND_HALF_UP)
        .$this->sell->cryptocurrency.' for N'.($this->sell->amount+$this->sell->commission->amount).
        ' has been successful and your bank account accordingly.')
        ->line('Thank you for patronizing us!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
