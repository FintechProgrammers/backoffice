<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DispatchCommission;
use App\Models\CommissionTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\CommissionReleased;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionsController extends Controller
{
    function index()
    {
        return view('admin.commissions.index');
    }

    function commissionFilter(Request $request)
    {

        $query = $this->queryCommissions($request);

        $data['commissions'] = $query->latest()->paginate(20);

        return view('admin.commissions._table', $data);
    }

    function calculateTotalCommission(Request $request)
    {
        $query = $this->queryCommissions($request);

        $total = $query->sum('amount');

        $data['total'] = number_format($total, 2, '.', ',');

        return response()->json($data);
    }

    function queryCommissions(Request $request)
    {
        $dateFrom = ($request->filled('startDate')) ? Carbon::parse($request->startDate)->startOfDay() : null;

        $dateTo = ($request->filled('endDate')) ? Carbon::parse($request->endDate)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;
        $status = $request->filled('status')  ? $request->status : null;

        $level = $request->filled('level') ? $request->level : null;

        $query = CommissionTransaction::when(!empty($search), function ($query) use ($search) {
            $query->whereHas('associate', function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%");
            });
        })
            ->when(!empty($status), function ($query) use ($status) {
                if ($status === '1') {
                    $query->where('is_converted', true);
                } else {
                    $query->where('is_converted', false);
                }
            })
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($level), function ($query) use ($level) {
                if ($level == 'direct') {
                    $query->where('level', 0);
                } else {
                    $query->where('level', '!=', '0');
                }
            })
            ->when(!empty($level) && !empty($dateFrom) && !empty($dateTo), function ($query) use ($level, $dateFrom, $dateTo) {
                if ($level == 'direct') {
                    $query = $query->where('level', 0);
                } else {
                    $query = $query->where('level', '!=', '0');
                }

                $query->whereBetween('created_at', [$dateFrom, $dateTo]);
            });

        return $query;
    }

    function payview()
    {
        $data['users'] = User::where('is_ambassador', true)->get();
        $data['weeks'] = [
            ['name' => 'Week 1 (1st to 7th)', 'value' => 1],
            ['name' => 'Week 2 (8th to 14th)', 'value' => 2],
            ['name' => 'Week 3 (15th to 21st)', 'value' => 3],
            ['name' => 'Week 4 (22nd to end of month)', 'value' => 4]
        ];

        return view('admin.commissions.payview', $data);
    }

    function store(Request $request)
    {
        // Validate the input
        $request->validate([
            'user' => ['required'],
            'week' => ['required'],
        ]);

        // Define the current date and selected week
        $now = Carbon::now();
        $currentDay = $now->day;
        $selectedWeek = $request->input('week');

        // Define week ranges
        $weekRanges = [
            1 => ['start' => 1, 'end' => 7],   // Week 1: 1st to 7th
            2 => ['start' => 8, 'end' => 14],  // Week 2: 8th to 14th
            3 => ['start' => 15, 'end' => 21], // Week 3: 15th to 21st
            4 => ['start' => 22, 'end' => $now->endOfMonth()->day] // Week 4: 22nd to end of month
        ];

        // Map each week to the earliest day of the month it can be paid on
        $weekPaymentDays = [
            1 => 14,  // Week 1 can be paid starting on the 14th
            2 => 21,  // Week 2 can be paid starting on the 21st
            3 => 28,  // Week 3 can be paid starting on the 28th
            4 => 7    // Week 4 can be paid starting on the 7th of the next month
        ];

        // Get the minimum payment day for the selected week
        $minimumPaymentDay = $weekPaymentDays[$selectedWeek];

        // Validate if today's date is >= the required payment day
        if ($currentDay < $minimumPaymentDay) {
            return back()->with('error', "You can only pay for Week {$selectedWeek} starting on the {$minimumPaymentDay}th day of the current month.");
        }

        // Get the date range for the selected week
        $weekStart = $weekRanges[$selectedWeek]['start'];
        $weekEnd = $weekRanges[$selectedWeek]['end'];

        // Calculate the date range for commissions
        $commissionStartDate = $now->copy()->startOfMonth()->addDays($weekStart - 1);
        $commissionEndDate = $now->copy()->startOfMonth()->addDays($weekEnd - 1)->endOfDay();

        // Fetch commissions based on the selected user or all users
        $userId = $request->input('user');

        $query = CommissionTransaction::where('is_converted', false)->whereHas('sale', function ($query) use ($commissionStartDate, $commissionEndDate) {
            $query->where('is_refunded', false)
                ->whereBetween('created_at', [$commissionStartDate, $commissionEndDate]);
        });

        if ($userId !== 'all') {
            $query = $query->where('user_id', $userId);
        }

        if ($selectedWeek === 4) {
            $query = $query->where('level', '!=', '0');
        } else {
            $query = $query->where('level', '0');
        }

        $commissions = $query->get();

        // Check if any commissions exist
        if ($commissions->isEmpty()) {
            return back()->with('error', 'No commissions found for the selected week and user.');
        }


        // If processing for a single user, sum the total amount
        if ($userId !== 'all') {

            try {
                DB::beginTransaction();

                $totalAmount = $commissions->sum('amount');
                $user = User::find($userId);

                if ($user) {
                    $wallet = $user->wallet;
                    $opening_balance = $wallet->balance;
                    $closing_balance = $wallet->balance + $totalAmount;

                    $wallet->update([
                        'balance' => $closing_balance
                    ]);

                    // Create a new transaction for the user
                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'associated_user_id' => null, // optional if summarizing multiple child transactions
                        'internal_reference' => generateReference(),
                        'amount' => $totalAmount,
                        'opening_balance' => $opening_balance,
                        'closing_balance' => $closing_balance,
                        'action' => 'credit',
                        'type' => 'commission',
                        'status' => 'completed',
                        'narration' => "Total sales commission",
                        'settlement_week' => $selectedWeek
                    ]);

                    // Mark all user commissions as released
                    $commissions->each->update(['is_converted' => true, 'transaction_id' => $transaction->id]);

                    // Notify the user
                    $user->notify(new CommissionReleased($totalAmount));
                }

                DB::commit();
            } catch (\Exception $e) {
                sendToLog($e);

                return back()->with('error', 'Unable to complete  the processs');
            }
        } else {
            // If processing for all users, group commissions by user
            $groupedCommissions = $commissions->groupBy('user_id');

            dispatch(new DispatchCommission($groupedCommissions, "week {$selectedWeek}"));
        }

        // Redirect back with success message
        return back()->with('success', 'Commissions processed successfully.');
    }
}
