<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MigratedUserNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $data)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $route = route('login');

        return (new MailMessage)
            ->subject('Your Account Has Been Successfully Migrated')
            ->greeting("Hello {$this->data['name']},")
            ->line('We are pleased to inform you that your account has been successfully migrated to our new platform.')
            ->line('Your login details are as follows:')
            ->line("**Username:** {$this->data['username']}")
            ->line("**Password:** {$this->data['password']}")
            ->action('Login to Your Account', $route)
            ->line('For security reasons, we recommend that you change your password after your first login.')
            ->line('If you have any questions or need assistance, please contact our support team.')
            ->line('Thank you for continuing to use our services!')
            ->salutation('Best regards, The Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
