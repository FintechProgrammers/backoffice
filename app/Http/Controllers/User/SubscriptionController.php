<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Service;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    function index()
    {
        $data['subscriptions'] = UserSubscription::where('user_id', Auth::user()->id)->get();
        $data['packages'] = Service::where('is_published', true)->get();

        return view('user.subscription.index', $data);
    }
}