<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionsController extends Controller
{
    function index()
    {
        return view('admin.subscriptions.index');
    }

    function filter(Request $request)
    {
        $data['subscriptions'] = UserSubscription::latest()->paginate(50);

        return view('admin.subscriptions._table', $data);
    }
}
