<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AmbassedorPayments;
use App\Models\CommissionTransaction;
use App\Models\Country;
use App\Models\Rank;
use App\Models\Sale;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    function index()
    {

        return view('admin.dashboard.index');
    }

    function filter(Request $request)
    {
        $dateFrom = ($request->filled('startDate')) ? Carbon::parse($request->startDate)->startOfDay() : null;

        $dateTo = ($request->filled('endDate')) ? Carbon::parse($request->endDate)->endOfDay() : null;

        $data['stats'] = $this->statistics($dateFrom, $dateTo);

        $topSellingServices = DB::table('sales')
            ->select('service_id', DB::raw('COUNT(*) AS total_sales_count'), DB::raw('SUM(amount) AS total_amount_sold'))
            ->when(!empty($dateFrom) && !empty($dateTo), function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            })
            ->groupBy('service_id')
            ->orderBy('total_amount_sold', 'desc')
            ->limit(10) // Adjust limit as needed
            ->get()
            ->map(function ($sale) {
                $sale->service = Service::withTrashed()->find($sale->service_id);
                return $sale;
            });

        $data['topSellingServices'] = $topSellingServices;

        $currentYear = $request->filled('year') ? $request->year : date('Y');

        // Retrieve total transaction amounts grouped by month
        $salesOvertime = Sale::where('is_refunded', false)->whereRaw('YEAR(created_at) = ?', [$currentYear])->select(
            DB::raw('MONTH(created_at) as month'), // Extract month from created_at
            DB::raw('SUM(amount) as total_amount') // Divide the sum of amount by 100
        )
            ->groupBy(DB::raw('MONTH(created_at)')) // Group by month
            ->orderByRaw('MONTH(created_at)') // Sort by month
            ->get();

        // Transform the sorted result to match the format needed for the chart
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
        $data['salesOvertime'] = [
            'labels' => $salesOvertime->map(function ($item) use ($monthsMap) {
                return $monthsMap[$item->month];
            })->toArray(),
            'total_amounts' => $salesOvertime->pluck('total_amount')->toArray(),
        ];

        // Fetch total sales per service
        $data['salesPerService'] = Sale::select(
            DB::raw('DATE(created_at) as date'),
            'service_id',
            DB::raw('SUM(amount) as total_sales')
        )
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->groupBy('service_id', DB::raw('DATE(created_at)'))
            ->get()
            ->groupBy('service_id')
            ->map(function ($sales, $serviceId) {
                $service = Service::find($serviceId);
                return [
                    'service_name' => $service ? $service->name : 'Unknown Service',
                    'daily_sales' => $sales->pluck('total_sales', 'date')
                ];
            });

        // Get user counts by country, only including users with a valid country code
        $userCounts = User::whereHas('userProfile', function ($query) {
            $query->whereNotNull('country_code'); // Exclude users with no country
        })
            ->join('user_infos', 'users.id', '=', 'user_infos.user_id') // Assuming 'user_infos' is the table name for UserInfo
            ->select('user_infos.country_code', DB::raw('count(*) as user_count'))
            ->groupBy('user_infos.country_code')
            ->get();

        // Prepare data for the map
        $markers = [];
        foreach ($userCounts as $userCount) {
            // Get country by country code
            $country = Country::where('iso2', $userCount->country_code)->first(); // Assuming Country has a 'code' attribute

            // Check if country is found; handle cases where it might not be
            if ($country) {
                $markers[] = [
                    'name' => $country->name, // Assuming Country has a 'name' attribute
                    'coords' => [$country->latitude, $country->longitude], // Assuming you have latitude and longitude
                    'style' => [
                        'fill' => '#28d193' // You can customize colors based on user_count or other logic
                    ]
                ];
            }
        }

        $data['markers'] = $markers;

        return response()->json($data);
    }

    function statistics($dateFrom = null, $dateTo = null)
    {

        $user = Admin::find(Auth::guard('admin')->user()->id);

        $totalAmbassadors = User::where('is_ambassador', true)->count();

        $totalcustomers = User::where('is_ambassador', false)->count();

        $totalUsers = User::when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))->count();

        $productsCount = Service::count();

        $tickets = Ticket::count();

        $openedTickets = Ticket::where('status', 'open')->count();

        $closedTickets = Ticket::where('status', 'closed')->count();

        $ranks = Rank::count();

        $totalSales = Sale::when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('amount');

        $withdrawalsSum = Transaction::where('type', 'withdrawal')->sum('amount');

        $expenses =  CommissionTransaction::when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))->sum('amount');

        $withdrawalRequest = Transaction::where('type', 'withdrawal')->where('status', 'pending')
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))->count();

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

        $weekCommissionPayment = paymentsOfTheWeek();

        $weekCommissionPayment = $weekCommissionPayment->sum('amount');

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
                'title' => 'Payments this week',
                'value' =>  "$" . number_format($weekCommissionPayment, 2, '.', ','),
                'color' => 'text-bg-danger',
                'icon' => 'las la-money-bill-wave-alt',
                'link' => null,
                'show' => $user->can('manage finance report'),
            ],
            (object) [
                'title' => 'Withdrawals',
                'value' => "$" . number_format($withdrawalsSum, 2, '.', ','),
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
