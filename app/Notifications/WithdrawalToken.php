<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalToken extends Notification implements ShouldQueue
{
    use Queueable;

    public $data;

    /**
     * Create a new notification instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $code = $this->data['code'];
        $name = $this->data['name'];

        return (new MailMessage)
            ->greeting("Hello {$name},")
            ->line('We have received your request to withdraw funds from your account.')
            ->line('To proceed with this request, please use the following One-Time Password (OTP) to verify your identity:')
            ->line("**{$code}**")
            ->line('Thank you for using our application!')
            ->salutation('Best regards,')
            ->salutation(config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
