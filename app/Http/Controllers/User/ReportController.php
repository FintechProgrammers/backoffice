<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BonusHistory;
use App\Models\CommissionTransaction;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Ramsey\Uuid\v1;

class ReportController extends Controller
{
    function bonuses()
    {
        $data['bonuses'] = BonusHistory::where('user_id', auth()->user()->id)->latest()->get();

        return view('user.report.bonuses', $data);
    }

    function commission()
    {
        return view('user.report.commission');
    }

    function commissionFilter(Request $request)
    {

        $query = $this->queryCommissions($request);

        $data['commissions'] = $query->latest()->paginate(20);

        return view('user.report._commission-table', $data);
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

        $query = CommissionTransaction::where('user_id', auth()->user()->id);

        $query =
            $query->when(!empty($search), function ($query) use ($search) {
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
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]));

        return $query;
    }

    function packages()
    {
        $data['packages'] = Sale::where('user_id', auth()->user()->id)->get();

        return view('user.report.packages', $data);
    }

    function ranks()
    {
        return view('user.report.ranks');
    }
}
