<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AddressAddedForWhitelisting extends Notification
{
    use Queueable;

    protected $address;

    public function __construct($address)
    {
        $this->address = $address;
    }

    public function via($notifiable)
    {
        return ['mail']; // You can add 'database', 'sms', or other channels if needed
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Address Submitted for Whitelisting')
            ->line('A new email address has been submitted for whitelisting:')
            ->line($this->address)
            ->line('Please review it and proceed with the whitelisting process.')
            // ->action('Review Whitelisting Requests', url('/admin/whitelisting-requests'))
            ->line('Thank you for your prompt attention!');
    }
}
