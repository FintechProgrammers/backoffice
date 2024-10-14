<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Admin2faNotification extends Notification
{
    use Queueable;

    protected $admin;
    protected $link;

    /**
     * Create a new notification instance.
     *
     * @param $admin
     */
    public function __construct($admin)
    {
        $this->admin = $admin;
        $this->link = generateTemporatyLink($admin, 'admin.security.enable-2fa', 60); // Generate the temporary link
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
        $firstName = ucfirst($this->admin->first_name); // Capitalize first name

        return (new MailMessage)
            ->subject('Set Up Two-Factor Authentication')
            ->greeting('Hello ' . $firstName . ',')
            ->line('You are receiving this email because you need to set up two-factor authentication for your account.')
            ->action('Set Up 2FA', $this->link) // Add the generated 2FA setup link
            ->line('This link will expire in 60 minutes.')
            ->line('Thank you for ensuring the security of your account!');
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
