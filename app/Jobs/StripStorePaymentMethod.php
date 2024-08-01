<?php

namespace App\Jobs;

use App\Models\Provider;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StripStorePaymentMethod implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $paymentMethod;

    /**
     * Create a new job instance.
     */
    public function __construct($data, $paymentMethod)
    {
        $this->data = $data;
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $stripeService = new \App\Services\StripeService();

            $dataLoad = ['customer_id' => $this->data->customer_id, 'payment_method' => $this->paymentMethod];

            $response = $stripeService->retrievePaymentMethod($dataLoad);

            $provider = Provider::where('short_name', 'strip')->first();

            if ($provider) {
                $payment = \App\Models\PaymentMethod::updateOrCreate(
                    [
                        'user_id' => $this->data->user_id,
                        'provider_id' => $provider->id,
                        'pm_id' => $response->id
                    ],
                    [
                        'card_brand' => $response->card->brand,
                        'last4' =>  $response->card->last4,
                        'details' => json_encode($response)
                    ]
                );
            }
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }
}