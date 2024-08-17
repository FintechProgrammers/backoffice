<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionExpired;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate user subscriptions that have expired';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired subscriptions...');

        try {

            // Get current date
            $currentDate = Carbon::now();

            $expiredSubscriptions = UserSubscription::where('end_date', '<', $currentDate)
                ->where('is_active', true)
                ->get();

            foreach ($expiredSubscriptions as $subscription) {

                $plan = $subscription->service;
                $user = $subscription->user;

                // cgeck if plan has auto renewal enabled
                if ($plan->auto_renewal) {
                    //check if user has payment method set
                    $paymentMethod = PaymentMethod::where('user_id', $user->id)->where('is_default', true)->first();

                    if ($paymentMethod) {
                        $provider = $paymentMethod->provider;

                        // create invoice
                        $invoice = Invoice::create([
                            'user_id' => $user->id,
                            'service_id' => !empty($package->id) ? $plan->id : null,
                            'order_id'   => generateReference(),
                            'is_paid' => false
                        ]);

                        $validated['invoice'] = $invoice;
                        $validated['user'] = $user;
                        $validated['package'] = $plan;

                        if ($provider->short_name == 'strip') {
                            $stripController = new \App\Http\Controllers\StripeController();

                            $route = $stripController->payment($validated);
                        }
                    }
                } else {
                    // Deactivate the subscription
                    $subscription->is_active = false;
                    $subscription->save();

                    if ($subscription->ambassadorship) {
                        $user->is_ambassador = false;
                        $user->save();
                    }

                    // Notify the user about the expired subscription
                    $user->notify(new SubscriptionExpired($subscription->end_date));
                }
            }
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }
}
