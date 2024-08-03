<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Service;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    function index()
    {
        $user = User::whereId(auth()->user()->id)->first();

        $data['subscriptions'] = $user->subscriptions;

        $data['packages'] = Service::where('is_published', true)->get();

        return view('user.subscription.index', $data);
    }
}
