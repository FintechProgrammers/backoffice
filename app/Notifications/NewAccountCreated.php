<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAccountCreated extends Notification
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
            ->subject('Your New Account Has Been Created')
            ->greeting("Hello {$this->data['name']},")
            ->line('We are pleased to inform you that an account has been created for you.')
            ->line("Your login details are as follows:")
            ->line("**Username:** {$this->data['username']}")
            ->line("**Password:** {$this->data['password']}")
            ->action('Login to Your Account', $route)
            ->line('Thank you for using our application!')
            ->salutation('Best regards,')
            ->salutation('The Team');
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
