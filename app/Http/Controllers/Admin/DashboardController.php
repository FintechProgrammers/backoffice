<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        $data['stats'] = $this->statistics();

        return view('admin.dashboard.index', $data);
    }

    function statistics()
    {

        $totalAmbassadors = User::where('is_ambassador',true)->count();

        $totalcustomers = User::where('is_ambassador',false)->count();

        $totalUsers = User::count();

        $productsCount = Service::count();

        $tickets = Ticket::count();

        $ranks = Rank::count();

        $totalSales = 0;

        $revenueCount = 0;

        $expenses = 0;

        $withdrawalRequest = 0;

        return  [
            (object) [
                'title' => 'Sales',
                'value' =>  "$".$totalSales,
                'color' => 'text-bg-success',
                'icon' => 'bx bx-chart',
                'link' => null,
            ],
            (object) [
                'title' => 'Revenue',
                'value' =>  "$".$revenueCount,
                'color' => 'text-bg-info',
                'icon' => 'las la-money-bill-alt',
                'link' => null,
            ],
            (object) [
                'title' => 'Expenses',
                'value' =>  "$".$expenses,
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
                'value' => 0,
                'color' => 'text-bg-success',
                'icon' => 'bi bi-people-fill',
                'link' => null,
            ],
            (object) [
                'title' => 'Inactive Users',
                'value' => 0,
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
