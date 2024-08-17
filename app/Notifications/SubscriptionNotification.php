<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class SubscriptionNotification extends Notification
{
    use Queueable;

    protected $message;
    protected $service;

    /**
     * Create a new notification instance.
     */
    public function __construct($message, $service)
    {
        $this->message = $message;
        $this->service = $service;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail']; // Specify the channels you want to use (e.g., mail, database, etc.)
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Subscription Notification')
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line($this->message)
            ->line('Service: ' . $this->service->name)
            // ->action('View Service', url('/services/' . $this->service->id)) // Adjust the URL as needed
            ->line('Thank you for using our service!');
    }

    /**
     * Get the array representation of the notification (for database storage).
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'service_name' => $this->service->name,
            'service_id' => $this->service->id,
        ];
    }
}