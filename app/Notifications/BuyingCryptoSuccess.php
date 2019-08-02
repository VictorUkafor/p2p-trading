<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BuyingCryptoSuccess extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $buy)
    {
        $this->user = $user;
        $this->buy = $buy;
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
        ->subject('Purchase of '.round($this->buy->value, 5, PHP_ROUND_HALF_UP).
        $this->buy->cryptocurrency.' successful')
        ->greeting('Hello '.$this->user->first_name.' '.$this->user->last_name)
        ->line('We are glad to inform you that your transaction for the purchase of '.
        round($this->buy->value, 5, PHP_ROUND_HALF_UP).$this->buy->cryptocurrency.
        ' has been successful and your wallet credited accordingly. '.
        'You can login into your account to view your balance.')
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
