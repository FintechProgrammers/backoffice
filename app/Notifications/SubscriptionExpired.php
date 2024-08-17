<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SubscriptionExpired extends Notification
{
    use Queueable;

    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(UserSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail']; // You can add 'database' or other channels as needed
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Subscription Has Expired')
            ->greeting('Hello ' . $notifiable->full_name . ',')
            ->line('We wanted to inform you that your subscription to the "' . $this->subscription->service->name . '" plan has expired.')
            ->line('Expiration Date: ' . $this->subscription->end_date->toFormattedDateString())
            ->line('Please renew your subscription to continue enjoying our services.')
            // ->action('Renew Now', url('/subscriptions/renew'))
            ->line('Thank you for being a valued member!');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'message' => 'Your subscription expired on ' . $this->subscription->end_date->toFormattedDateString(),
            'action_url' => url('/subscriptions/renew'),
        ];
    }
}
