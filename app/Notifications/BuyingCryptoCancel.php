<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BuyingCryptoCancel extends Notification
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
        ->subject('Cancellation of transaction')
        ->greeting('Hello '.$this->user->first_name.' '.$this->user->last_name)
        ->line('We are sorry to inform you that the purchase of '.
        round($this->buy->value, 5, PHP_ROUND_HALF_UP).$this->buy->cryptocurrency.
        ' could not be processed at this time and it\'s therefore cancelled'.
        '. You can reachout to us for more info or better still make another transaction.')
        ->line('We are so sorry for any inconviences and we highly appreciate your patronage!');
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
