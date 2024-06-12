<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    function index()
    {
        return view('admin.withdrawals.index');
    }

    function filter(Request $request)
    {
        $data['withdrawals'] = Withdrawal::latest()->paginate(50);

        return view('admin.withdrawals._table', $data);
    }

    function details(Withdrawal $withdrawal)
    {
        $data['withdrawal'] = $withdrawal;
        $data['details']    = json_decode($withdrawal->details);

        return view('admin.withdrawals._details', $data);
    }
}
