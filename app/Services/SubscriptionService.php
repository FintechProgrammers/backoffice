<?php

namespace App\Services;

use App\Mail\SubscriptionMail;
use App\Models\Bonus;
use App\Models\Sale;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SubscriptionService
{

    function startService($service, $user)
    {
        $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $service->id)->first();

        if ($userSubscription) {
            $this->updateSubscription($service, $userSubscription);
        } else {
            $this->createSubscription($service, $user);
        }
    }

    function createSubscription($service, $user)
    {
        $subscription = UserSubscription::create(
            [
                'user_id' => $user->id,
                'service_id' => $service->id,
                'reference'  => generateReference(),
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addDays($service->duration),
                'is_active' => true
            ]
        );

        $this->ambassadorAccount($user, $service);

        UserActivities::create([
            'user_id' => $user->id,
            'log'  => "Subscribed to {$service->name} service."
        ]);

        $this->createSaleRecord($subscription, $service->price);

        $message = "You have successfully subscribed to the {$service->name}.";

        $this->sendMail($user, $message, $service);
    }

    function updateSubscription($service, $userSubscription)
    {
        $userSubscription->update([
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($service->duration),
            'is_active' => true
        ]);

        $this->ambassadorAccount($userSubscription->user, $service);

        UserActivities::create([
            'user_id' => $userSubscription->user_id,
            'log'  => "Renewed subscription for {$service->name} service."
        ]);

        $this->createSaleRecord($userSubscription, $service->price);

        $message = "You have successfully renewed the {$service->name}.";

        $this->sendMail($userSubscription->user, $message, $service);
    }

    function createSaleRecord($subscription, $amount)
    {
        Sale::create([
            'user_id' => $subscription->user->id,
            'service_id' => $subscription->service->id,
            'cycle_id' => currentCycle(),
            'parent_id' => !empty($subscription->user->parent_id) ? $subscription->user->parent_id : null,
            'amount' => $amount,
            'bv_amount' => $subscription->service->bv_amount
        ]);
    }

    function ambassadorAccount($user, $service)
    {
        if ($service->ambassadorship) {
            $user->update([
                'is_ambassador' => true
            ]);

            Bonus::create([
                'user_id' => $user->id,
                'amount' => 0
            ]);
        }
    }

    function sendMail($user, $message, $service)
    {
        $mail = [
            'name'  => $user->name,
            'content' => $message,
            'service' => $service
        ];

        Mail::to($user->email)->send(new SubscriptionMail($mail));
    }
}
