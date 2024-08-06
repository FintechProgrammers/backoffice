<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalNotification extends Notification
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

        $name = $this->data['name'];
        $amount = number_format($this->data['amount'], 2);
        $status = $this->data['status'];

        $mailMessage = (new MailMessage)
            ->greeting("Hello {$name},");

        if ($status === 'completed') {
            $mailMessage->line("We wanted to inform you that your withdrawal of \${$amount} has been successfully processed.");
        } else {
            $mailMessage->line("We regret to inform you that your withdrawal of \${$amount} has failed.")
                ->line("Please check your account and try again.");
        }

        $mailMessage->line('Thank you for using our application!')
            ->salutation('Best regards,')
            ->salutation(config('app.name'));

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'name' => $this->data['name'],
            'amount' => $this->data['amount'],
            'status' => $this->data['status'],
        ];
    }
}
