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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
                $monthlyIndirectCommission = $user->getTeamCommissions();
            } else {
                $monthlyIndirectCommission = 0;
            }

            $currentWeekCommissions = $weeklyDirectCommission + $monthlyIndirectCommission;

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

    public function getSalesData(Request $request)
    {
        $user = $request->user();

        $currentYear = $request->filled('year') ? $request->year : date('Y');

        // Get sales grouped by month
        $saleOvertime = $user->sales()->whereRaw('YEAR(created_at) = ?', [$currentYear])
            ->select(
                DB::raw('MONTH(created_at) as month'), // Extract month from created_at
                DB::raw('SUM(amount) / 100 as total_amount') // Divide the sum of amount by 100
            )
            ->groupBy(DB::raw('MONTH(created_at)')) // Group by month
            ->orderByRaw('MONTH(created_at)') // Sort by month
            ->get();

        // Define an array to map month numbers to month names
        $monthsMap = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December',
        ];

        // Transform the sorted result to match the format needed for the chart
        $salesData = [
            'labels' => $saleOvertime->map(function ($item) use ($monthsMap) {
                return $monthsMap[$item->month];
            })->toArray(),
            'total_amounts' => $saleOvertime->pluck('total_amount')->toArray(),
        ];

        return response()->json($salesData);
    }
}
