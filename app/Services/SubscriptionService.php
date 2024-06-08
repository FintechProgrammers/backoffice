<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use Carbon\Carbon;

class SubscriptionService
{
    function createSubscription($service, $user)
    {
        $subscription = UserSubscription::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'reference'  => generateReference(),
            'start_date' =>  Carbon::now(),
            'end_date' =>  Carbon::now()->addDays($service->duration),
            'is_active' => true
        ]);

        UserActivities::create([
            'user_id' => $user->id,
            'log'  => "Subscribed to {$service->name} service."
        ]);

        $this->createSaleRecord($subscription, $service->price);
    }

    function updateSubscription($service, $userSubscription)
    {
        $userSubscription->update([
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($service->duration),
            'is_active' => true
        ]);

        UserActivities::create([
            'user_id' => $userSubscription->user_id,
            'log'  => "Renewed subscription for {$service->name} service."
        ]);

        $this->createSaleRecord($userSubscription, $service->price);
    }

    function createSaleRecord($subscription, $amount)
    {
        Sale::create([
            'user_id' => $subscription->user->id,
            'service_id' => $subscription->service->id,
            'parent_id' => !empty($subscription->user->parent_id) ? $subscription->user->parent_id : null,
            'amount' => $amount
        ]);
    }
}
