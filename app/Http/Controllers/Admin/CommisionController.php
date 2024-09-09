<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionTransaction;
use App\Models\Sale;
use App\Models\User;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommisionController extends Controller
{
    function index(User $user)
    {
        $data['user'] = $user;
        $data['sales'] = Sale::get();

        return view('admin.users.commission-form', $data);
    }

    function store(Request $request)
    {
        $request->validate([
            'sale' => 'required|exists:sales,uuid'
        ]);

        try {

            DB::beginTransaction();

            $sale = Sale::whereUuid($request->sale)->first();

            // check if this sale already has a comission
            $commission = CommissionTransaction::where('sale_id', $sale->id)->first();

            if ($commission) {
                return back()->with('error', 'This sale has already been commissioned.');
            }

            $commissionService = new CommissionService();

            $commissionService->distributeCommissions($sale);

            DB::commit();

            return back()->with('success', 'Commission released successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e);

            return back()->with('error', serviceDownMessage());
        }
    }
}
