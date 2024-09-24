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

            $directSale = $user->directSales();
            $commissions = $user->commissionTransactions();
            $directCommission = $user->directCommissionTransactions;
            $indirectCommission = $user->indirectCommissionTransactions;

            $wallet = Wallet::where('user_id', $user->id)->select('balance')->first();

            $weeklyDirectCommission = $directCommission->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('amount')->sum('amount');

            $now = Carbon::now();


            $weekOfMonth = $now->weekOfMonth;
            $startOfWeek = $now->copy()->startOfWeek();
            $endOfWeek = $now->copy()->endOfWeek();

            if ($weekOfMonth == 4) {
                $weeklyIndirectCommission = $indirectCommission->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                    ->select('amount')
                    ->sum('amount');
            } else {
                $weeklyIndirectCommission = 0;
            }

            $currentWeekCommissions = $weeklyDirectCommission + $weeklyIndirectCommission;

            $data['currentRank'] = $user->rank;
            $data['nextRank'] = $user->nextRank();
            $data['progress'] = $user->getRankProgress();

            $data['walletBalance'] = $wallet->balance;

            $data['currentWeekDirectVolume'] =  $directSale->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('bv_amount')->sum('bv_amount');

            $data['currentWeekDirectCommissions'] = $weeklyDirectCommission;

            $data['currentMonthVolume'] = $user->total_bv_this_month;

            $data['currentMonthCommissions'] = $commissions->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('amount')->sum('amount');

            $data['currentWeekCommissions'] = $currentWeekCommissions;

            $data['teamVolume'] = $user->team_volume;

            $data['teamCommissions'] = $user->getTeamCommissions();

            $data['teamAmbassadors'] = $user->getAmbassadorDescendants()->count();
            $data['teamCustomers'] = $user->getCustomerDescendants()->count();

            $data['lifeTimeEarnings'] = $user->total_earnings;

            $data['activeUsers'] = $user->getActiveSubscriptionDescendants()->count();
            $data['totalUsers'] =   $data['teamAmbassadors'] + $data['teamCustomers'];
            $data['upcomingRenewals'] = $user->subscription()->expiringInOneWeek()->count();
            $data['topTeamSellers'] = $user->getTopSellers(20);

            $data['topGlobalSellers'] = topGlobalSellers(20);
            $data['enrollments'] = $user->getLastEnrolledUsers();
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
