<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SellingCrypto extends Notification
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
        ->subject('Concerning the selling of '.$this->sell->cryptocurrency)
        ->greeting('Hello '.$this->user->first_name.' '.$this->user->last_name)
        ->line('Thank you for your interest to sell '.
        round($this->sell->value, 10, PHP_ROUND_HALF_UP).$this->sell->cryptocurrency.
        ' plus a charge of '.round($this->sell->commission->amount, 7, PHP_ROUND_HALF_UP)
        .$this->sell->cryptocurrency.' for N'.($this->sell->amount).
        '. Your transaction is pending at the moment but you will be '.
        'notified when your transaction is reviewed and confirmed.')
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
