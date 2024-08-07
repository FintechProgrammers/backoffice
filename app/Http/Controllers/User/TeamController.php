<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Sale;
use App\Models\Service;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamController extends Controller
{
    function index()
    {
        return view('user.team.index');
    }

    function filter(Request $request)
    {
        $user = User::whereId(auth()->user()->id)->first();

        $data['customers'] = $user->getAmbassadorDescendants();

        return view('user.team._table', $data);
    }

    function customers()
    {
        return view('user.customers.index');
    }

    function customersFilter(Request $request)
    {
        $user = User::whereId(auth()->user()->id)->first();

        $data['customers'] = $user->getCustomerDescendants();

        return view('user.customers._table', $data);
    }

    function genealogy()
    {
        $user = auth()->user();

        $data['user'] = $user;

        return view('user.team.genealogy', $data);
    }

    function userInfo(User $user)
    {
        $data['user'] = $user;
        $data['totalSales'] = Sale::where('parent_id', $user->id)->sum('amount');
        $data['currentCycleSales'] = Sale::where('parent_id', $user->id)->where('cycle_id', currentCycle())->sum('amount');
        $data['subscriptions'] = UserSubscription::where('user_id', $user->id)->latest()->get();

        return view('user.team.user-info', $data);
    }

    function createCustomer()
    {
        $data['countries'] = Country::get();
        $data['packages'] = Service::where('is_published', true)->where('ambassadorship', false)->get();

        return view('user.team.create-user', $data);
    }
}
