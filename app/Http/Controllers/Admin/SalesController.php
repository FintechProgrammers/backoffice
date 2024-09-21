<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    function index()
    {
        return view('admin.sales.index');
    }

    function filter(Request $request)
    {
        $dateFrom = ($request->filled('startDate')) ? Carbon::parse($request->startDate)->startOfDay() : null;

        $dateTo = ($request->filled('endDate')) ? Carbon::parse($request->endDate)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;

        $searchSponsor = $request->filled('search_sponsor') ? $request->search_sponsor : null;

        $sales = Sale::when(!empty($search), function ($query) use ($search) {
            $query->whereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', "%$search%")
                    ->orWhere('last_name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('username', 'like', "%$search%");
            });
        })->when(!empty($searchSponsor), function ($query) use ($searchSponsor) {
            $query->whereHas('parent', function ($query) use ($searchSponsor) {
                $query->where('first_name', 'like', "%$searchSponsor%")
                    ->orWhere('last_name', 'like', "%$searchSponsor%")
                    ->orWhere('email', 'like', "%$searchSponsor%")
                    ->orWhere('username', 'like', "%$searchSponsor%");
            });
        })->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($dateFrom) && !empty($dateTo) && !empty($search), function ($query) use ($search, $dateFrom, $dateTo) {
                $query->whereHas('user', function ($query) use ($search) {
                    $query->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%");
                })->whereBetween('created_at', [$dateFrom, $dateTo]);
            });

        $data['sales'] = $sales->latest()->paginate(20);

        return view('admin.sales._table', $data);
    }
}
