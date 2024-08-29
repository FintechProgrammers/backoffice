<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubscriptionResource;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    function index(Request $request)
    {
        $user = $request->user();

        // get User subscriptions
        $subscriptions  = SubscriptionResource::collection($user->subscriptions);

        return $this->sendResponse($subscriptions);
    }
}
