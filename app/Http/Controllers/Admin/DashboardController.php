<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AmbassedorPayments;
use App\Models\CommissionTransaction;
use App\Models\Rank;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index()
    {
        $data['stats'] = $this->statistics();
        $topSellingServices = DB::table('sales')
            ->select('service_id', DB::raw('COUNT(*) AS total_sales_count'), DB::raw('SUM(amount) AS total_amount_sold'))
            ->groupBy('service_id')
            ->orderBy('total_amount_sold', 'desc')
            ->limit(10) // Adjust limit as needed
            ->get()
            ->map(function ($sale) {
                $sale->service = Service::withTrashed()->find($sale->service_id);
                return $sale;
            });

        // $topSellingServices = Service::whereIn('id', $topSellingServiceIds)->get();

        $data['topSellingServices'] = $topSellingServices;

        return view('admin.dashboard.index', $data);
    }

    function statistics()
    {

        $user = Admin::find(Auth::guard('admin')->user()->id);

        $totalAmbassadors = User::where('is_ambassador', true)->count();

        $totalcustomers = User::where('is_ambassador', false)->count();

        $totalUsers = User::count();

        $productsCount = Service::count();

        $tickets = Ticket::count();

        $openedTickets = Ticket::where('status', 'open')->count();

        $closedTickets = Ticket::where('status', 'closed')->count();

        $ranks = Rank::count();

        $totalSales = Sale::sum('amount');

        $withdrawalsSum = Transaction::where('type', 'withdrawal')->sum('amount');

        $expenses =  CommissionTransaction::where('is_converted', false)->sum('amount');

        $withdrawalRequest = Transaction::where('type', 'withdrawal')->where('status', 'pending')->count();

        $activeUsers = User::has('subscription')
            ->whereHas('subscription', function ($query) {
                $query->where('is_active', true);
            })
            ->count();

        $inactiveUsers = User::doesntHave('subscription')->count();
        $usersWithInactiveSubscriptions = User::has('subscription')
            ->whereHas('subscription', function ($query) {
                $query->where('is_active', false);
            })
            ->count();

        $inactiveUsers = $inactiveUsers + $usersWithInactiveSubscriptions;

        return  [
            (object) [
                'title' => 'Revenue',
                'value' =>  "$" . number_format($totalSales, 2, '.', ','),
                'color' => 'text-bg-success',
                'icon' => 'bx bx-chart',
                'link' => null,
                'show' => $user->can('manage finance report'),
            ],
            (object) [
                'title' => 'Expenses',
                'value' =>  "$" . number_format($expenses, 2, '.', ','),
                'color' => 'text-bg-danger',
                'icon' => 'las la-money-bill-wave-alt',
                'link' => null,
                'show' => $user->can('manage finance report'),
            ],
            (object) [
                'title' => 'Profit',
                'value' =>  "$" . number_format($totalSales - $expenses, 2, '.', ','),
                'color' => 'text-bg-info',
                'icon' => 'las la-money-bill-alt',
                'link' => null,
                'show' => $user->can('manage finance report'),
            ],

            (object) [
                'title' => 'Withdrawals',
                'value' => $withdrawalRequest,
                'color' => 'text-bg-danger',
                'icon' => 'las la-hand-holding-usd',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Ambassadors',
                'value' => $totalAmbassadors,
                'color' => 'text-bg-warning',
                'icon' => 'bi bi-people-fill',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Customers',
                'value' => $totalcustomers,
                'color' => 'text-bg-info',
                'icon' => 'las la-users',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Total Users',
                'value' => $totalUsers,
                'color' => 'text-bg-info',
                'icon' => 'bi bi-people-fill',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Active Users',
                'value' => $activeUsers,
                'color' => 'text-bg-success',
                'icon' => 'bi bi-people-fill',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Inactive Users',
                'value' => $inactiveUsers,
                'color' => 'text-bg-warning',
                'icon' => 'bi bi-people-fill',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Products',
                'value' => $productsCount,
                'color' => 'text-bg-success',
                'icon' => 'las la-sitemap',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Ranks',
                'value' => $ranks,
                'color' => 'text-bg-info',
                'icon' => 'las la-medal',
                'link' => null,
                'show' => true,
            ],
            (object) [
                'title' => 'Support Tickets',
                'value' => $tickets,
                'color' => 'text-bg-dark',
                'icon' => 'las la-headset',
                'link' => null,
                'show' => $user->can('manage ticket'),
            ],
            (object) [
                'title' => 'Opened Tickets',
                'value' => $openedTickets,
                'color' => 'text-bg-warning',
                'icon' => 'las la-headset',
                'link' => null,
                'show' => $user->can('manage ticket'),
            ],
            (object) [
                'title' => 'Closed Tickets',
                'value' => $closedTickets,
                'color' => 'text-bg-danger',
                'icon' => 'las la-headset',
                'link' => null,
                'show' => $user->can('manage ticket'),
            ],
        ];
    }
}
