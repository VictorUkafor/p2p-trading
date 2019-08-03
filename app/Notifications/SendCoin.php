<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SendCoin extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($transfer, $commission)
    {
        $this->transfer = $transfer;
        $this->commission = $commission;
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
        ->subject('Transfer of '.$this->transfer->amount.$this->transfer->coin.' successful')
        ->greeting('Hello '.$this->transfer->sender->user->first_name.' '.
        $this->transfer->sender->user->last_name)
        ->line('This is to notify you that the transfer of  '.
        $this->transfer->amount.$this->transfer->coin.' to '.
        $this->transfer->receiver->user->first_name.' '.
        $this->transfer->receiver->user->last_name.' was successful. This transaction '.
        'attracted a charge of '.$this->commission->amount.$this->transfer->coin)
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
