<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BusinessVolumeController extends Controller
{
    function index(Request $request)
    {
        $user = User::where('id', $request->user()->id)->first();


        if ($user->is_ambassador) {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();

            $directSale = $user->directSales();
            $commissions = $user->commissionTransactions();
            $directCommission = $user->directCommissionTransactions;
            // $indirectCommission = $user->indirectCommissionTransactions;

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

            $data['currentWeekDirectVolume'] =  $directSale->whereBetween('created_at', [$startOfWeek, $endOfWeek])->select('bv_amount')->sum('bv_amount');

            $data['currentWeekDirectCommissions'] = $weeklyDirectCommission;

            $data['currentMonthVolume'] = $user->total_bv_this_month;

            $data['currentMonthCommissions'] = $commissions->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->select('amount')->sum('amount');

            $data['currentWeekCommissions'] = $currentWeekCommissions;

            $data['teamVolume'] = $user->team_volume;

            $data['teamCommissions'] = $user->getTeamCommissions();
        } else {
            $data['currentWeekDirectVolume'] =  0;

            $data['currentWeekDirectCommissions'] = 0;

            $data['currentMonthVolume'] = 0;

            $data['currentMonthCommissions'] = 0;

            $data['currentWeekCommissions'] = 0;

            $data['teamVolume'] = 0;

            $data['teamCommissions'] = 0;
        }

        return $this->sendResponse($data);
    }
}
