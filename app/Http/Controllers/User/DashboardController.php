<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BonusHistory;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\UserSubscription;
use App\Models\Wallet;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        $user = auth()->user();

        $data['banners'] = Banner::latest()->get();
        $data['walletBalance'] = Wallet::where('user_id', $user->id)->select('amount')->sum('amount');
        $data['currenctCirlceDirectVolume'] = BonusHistory::where('user_id', $user->id)->where('cycle_id', currentCycle())->select('amount')->sum('amount');
        $data['currenctCirlceCommisions'] = BonusHistory::where('user_id', $user->id)->where('cycle_id', currentCycle())->select('amount')->sum('amount') * systemSettings()->bv_equivalent;
        $data['lifeTimeEarnings'] = BonusHistory::where('user_id', $user->id)->where('is_converted', true)->select('amount')->sum('amount') * systemSettings()->bv_equivalent;
        $data['subscriptionsCount'] = UserSubscription::where('user_id', $user->id)->count();
        $data['ticketsCount'] = Ticket::where('user_id', $user->id)->count();

        return view('user.dashboard.index', $data);
    }

    function weekClock()
    {
        $data = getWeekStartAndEnd();

        return $this->sendResponse($data);
    }
}
