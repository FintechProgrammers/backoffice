<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AmbassedorPayments;
use App\Models\Rank;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index()
    {
        $data['stats'] = $this->statistics();
        $topSellingServiceIds = DB::table('sales')
            ->select('service_id', DB::raw('COUNT(*) AS total_sales'))
            ->groupBy('service_id')
            ->orderBy('total_sales', 'desc')
            ->limit(10) // Adjust limit as needed
            ->pluck('service_id');

        $topSellingServices = Service::whereIn('id', $topSellingServiceIds)->get();

        $data['topSellingServices'] = $topSellingServices;

        return view('admin.dashboard.index', $data);
    }

    function statistics()
    {

        $totalAmbassadors = User::where('is_ambassador', true)->count();

        $totalcustomers = User::where('is_ambassador', false)->count();

        $totalUsers = User::count();

        $productsCount = Service::count();

        $tickets = Ticket::count();

        $ranks = Rank::count();

        $totalSales = Sale::sum('amount');

        $totalAmbassadorSales = AmbassedorPayments::sum('amount');

        $revenueCount = $totalSales + $totalAmbassadorSales;

        $withdrawalsSum = Withdrawal::where('status', 'completed')->sum('amount');

        $expenses =  $withdrawalsSum;

        $withdrawalRequest = Withdrawal::where('status', 'pending')->count();

        $activeUsers = User::has('subscriptions', '>=', 1)
            ->whereHas('subscriptions', function ($query) {
                $query->where('is_active', true);
            })
            ->count();

        $inactiveUsers = User::doesntHave('subscriptions')->count();
        $usersWithInactiveSubscriptions = User::has('subscriptions')
            ->whereHas('subscriptions', function ($query) {
                $query->where('is_active', false);
            })
            ->count();

        $inactiveUsers = $inactiveUsers + $usersWithInactiveSubscriptions;

        return  [
            (object) [
                'title' => 'Sales',
                'value' =>  "$" . number_format($totalSales, 2),
                'color' => 'text-bg-success',
                'icon' => 'bx bx-chart',
                'link' => null,
            ],
            (object) [
                'title' => 'Revenue',
                'value' =>  "$" . number_format($revenueCount, 2),
                'color' => 'text-bg-info',
                'icon' => 'las la-money-bill-alt',
                'link' => null,
            ],
            (object) [
                'title' => 'Expenses',
                'value' =>  "$" . number_format($expenses, 2),
                'color' => 'text-bg-danger',
                'icon' => 'las la-money-bill-wave-alt',
                'link' => null,
            ],
            (object) [
                'title' => 'Withdrawal Request',
                'value' => $withdrawalRequest,
                'color' => 'text-bg-danger',
                'icon' => 'las la-hand-holding-usd',
                'link' => null,
            ],
            (object) [
                'title' => 'Ambassadors',
                'value' => $totalAmbassadors,
                'color' => 'text-bg-warning',
                'icon' => 'bi bi-people-fill',
                'link' => null,
            ],
            (object) [
                'title' => 'Customers',
                'value' => $totalcustomers,
                'color' => 'text-bg-info',
                'icon' => 'las la-users',
                'link' => null,
            ],
            (object) [
                'title' => 'Total Users',
                'value' => $totalUsers,
                'color' => 'text-bg-info',
                'icon' => 'bi bi-people-fill',
                'link' => null,
            ],
            (object) [
                'title' => 'Active Users',
                'value' => $activeUsers,
                'color' => 'text-bg-success',
                'icon' => 'bi bi-people-fill',
                'link' => null,
            ],
            (object) [
                'title' => 'Inactive Users',
                'value' => $inactiveUsers,
                'color' => 'text-bg-warning',
                'icon' => 'bi bi-people-fill',
                'link' => null,
            ],
            (object) [
                'title' => 'Products',
                'value' => $productsCount,
                'color' => 'text-bg-success',
                'icon' => 'las la-sitemap',
                'link' => null,
            ],
            (object) [
                'title' => 'Ranks',
                'value' => $ranks,
                'color' => 'text-bg-info',
                'icon' => 'las la-medal',
                'link' => null,
            ],
            (object) [
                'title' => 'Support Tickets',
                'value' => $tickets,
                'color' => 'text-bg-dark',
                'icon' => 'las la-headset',
                'link' => null,
            ],
        ];
    }
}
