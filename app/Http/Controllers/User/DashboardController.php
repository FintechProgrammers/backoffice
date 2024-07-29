<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BonusHistory;
use App\Models\CommissionTransaction;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        $user = User::where('id', auth()->user()->id)->first();

        if ($user->is_ambassador) {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $directSale = Sale::where('parent_id', $user->id);
            $directCommission = CommissionTransaction::where('user_id', $user->id);

            $data['walletBalance'] = Wallet::where('user_id', $user->id)->select('amount')->sum('amount');

            $data['currentWeekDirectVolume'] =  $directSale->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('bv_amount')->sum('bv_amount');

            $data['currentWeekDirectCommissions'] = $user->directCommissionTransactions->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('amount')->sum('amount');

            $data['currentMonthDirectVolume'] = $directSale->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('bv_amount')->sum('bv_amount');

            $data['currentMonthCommissions'] = $directCommission->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('amount')->sum('amount');

            $data['currentWeekCommissions'] = $directCommission->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('amount')->sum('amount');

            $data['teamVolume'] = calculateTeamSales($user->id);

            $data['teamCommissions'] = calculateTeamCommissions($user->id);

            $data['teamAmbassadors'] = getAmbassadorDownlinesList($user->id)->count();
            $data['teamCustomers'] = getCustomerDownlinesList($user->id)->count();

            $data['lifeTimeEarnings'] = $directCommission->where('is_converted', true)->select('amount')->sum('amount');

            $data['activeUsers'] = getDownlinesWithActiveSubscriptionsList($user->id)->count();
            $data['totalUsers'] =   $data['teamAmbassadors'] + $data['teamCustomers'];
            $data['upcomingRenewals'] = $user->subscriptions()->expiringInOneWeek()->count();
            $data['topTeamSellers'] = getTopTeamSellers($user->id);
            $data['topGlobalSellers'] = Sale::with('user')->get()->groupBy('user_id')->map(function ($sales) {
                return [
                    'user' => $sales->first()->user,
                    'total_amount' => $sales->sum('amount'),
                ];
            })->sortByDesc('total_amount')->take(20);
        }


        $data['subscriptionsCount'] = UserSubscription::where('user_id', $user->id)->count();

        $data['ticketsCount'] = Ticket::where('user_id', $user->id)->count();

        $data['banners'] = Banner::latest()->get();


        return view('user.dashboard.index', $data);
    }


    function weekClock()
    {
        $data = getWeekStartAndEnd();

        return $this->sendResponse($data);
    }
}
