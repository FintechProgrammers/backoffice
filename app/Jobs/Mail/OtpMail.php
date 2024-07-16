<?php

namespace App\Jobs\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class OtpMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $data;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        logger("hello");

        $body = new OtpMail($this->data);

        $email = $this->data['email'];

        $sent = sendMailByDriver('mailgun', $email, $body);

        // Send mail via send grid driver if mailgun fails
        if (!$sent) {
            logger('smtp');
            sendMailByDriver('smtp', $email, $body);
        }
    }
}
