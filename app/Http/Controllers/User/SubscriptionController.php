<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    function index()
    {
        $data['subscriptions'] = Sale::where('user_id',Auth::user()->id)->get();

        return view('user.subscription.index',$data);
    }
}
