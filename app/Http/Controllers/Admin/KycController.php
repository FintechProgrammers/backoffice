<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserKyc;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KycController extends Controller
{
    function index()
    {
        return view('admin.kyc.index');
    }

    function filter(Request $request)
    {

        $dateFrom = ($request->filled('date_from')) ? Carbon::parse($request->date_from)->startOfDay() : null;

        $dateTo = ($request->filled('date_to')) ? Carbon::parse($request->date_to)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;
        $status = $request->filled('status')  ? $request->status : null;
        $accountType = $request->filled('account_type') ? $request->account_type : null;

        $query = User::whereHas('kyc')->withTrashed();

        $query = $query
            ->when(!empty($search), fn($query) => $query->where('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")->orWhere('username', 'LIKE', "%{$search}%"))
            ->when(!empty($status), fn($query) => $query->where('status', $status))
            ->when(!empty($accountType), fn($query) => $accountType == 'ambassador' ? $query->where('is_ambassador', true) : $query->where('is_ambassador', false))
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(
                !empty($status) && !empty($dateFrom) && !empty($dateTo),
                fn($query) => $query->where('status', $status)->whereBetween('created_at', [$dateFrom, $dateTo])
            )
            ->when(
                !empty($accountType) && !empty($dateFrom) && !empty($dateTo),
                fn($query) => $accountType == 'ambassador' ? $query->where('is_ambassador', true)->whereBetween('created_at', [$dateFrom, $dateTo]) : $query->where('is_ambassador', false)->whereBetween('created_at', [$dateFrom, $dateTo])
            );

        $data['users'] = $query->paginate(50);

        return view('admin.kyc._table', $data);
    }

    function show(User $user)
    {
        $data['documents'] = UserKyc::where('user_id', $user->id)->get();
        $data['user'] = $user;

        return view('admin.kyc.show', $data);
    }

    function approve(UserKyc $kyc)
    {
        $kyc->update([
            'status' => 'verified',
        ]);

        User::where('id', $kyc->user_id)->update([
            'kyc_is_verified'  => true,
        ]);

        return $this->sendResponse([], "Document Approved Successfully.");
    }

    function decline(UserKyc $kyc)
    {
        $kyc->update([
            'status' => 'declined',
        ]);

        User::where('id', $kyc->user_id)->update([
            'kyc_is_verified'  => false,
        ]);

        return $this->sendResponse([], "Document Declined Successfully.");
    }
}
