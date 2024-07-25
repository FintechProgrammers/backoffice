<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Sale;
use App\Models\Service;
use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    function index()
    {

        return view('user.team.index');
    }

    function filter(Request $request)
    {
        $user = auth()->user();

        $data['customers'] = User::where('parent_id', $user->id)->paginate(9);

        return view('user.team._content', $data);
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
        $data['subscription'] = UserSubscription::where('user_id', $user->id)->latest()->first();

        return view('user.team.user-info', $data);
    }

    function createCustomer()
    {
        $data['countries'] = Country::get();
        $data['packages'] = Service::where('is_published', true)->where('ambassadorship', false)->get();

        return view('user.team.create-user', $data);
    }
}