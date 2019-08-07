<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TradeDecline extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ad, $client)
    {
        $this->ad = $ad;
        $this->client = $client;
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
        ->subject('Your engaged '.$this->ad->type.' Trade Ad '.$this->ad->referenceNo.' has been declined')
        ->greeting('Hello '.$this->client->user->first_name.' '.$this->client->user->last_name)
        ->line('This is to notify you that the owner of the '.$this->ad->type.' Trade Ad '.$this->ad->referenceNo.
        ' you engaged has declined your engagement. Please reachout to them for more info; '.
        $this->ad->creator->first_name.' '.$this->ad->creator->last_name.', '.$this->ad->creator->phone )
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
