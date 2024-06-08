<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Models\UserActivities;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WithdrawalController extends Controller
{
    function index()
    {
        return view('user.withdrawal.index');
    }

    function filter(Request $request)
    {
        $data['withdrawals'] = Withdrawal::where('user_id', Auth::user()->id)->get();

        return view('user.withdrawal._table', $data);
    }

    function create()
    {
        return view('user.withdrawal.create');
    }

    function store(WithdrawalRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $request->user();

            $bonusWallet = Auth::user()->bonuWallet;

            $convertedBV = $bonusWallet->amount * systemSettings()->bv_equivalent;

            if ($request->amount > $convertedBV) {
                return $this->sendError("Insufficient funds to place withdrawal", []);
            }

            $details = [
                'account_number' => $request->account_number,
                'account_name'   => $request->account_name,
            ];

            Withdrawal::create([
                'reference' => generateReference(),
                'user_id'  => $user->id,
                'amount' =>  $request->amount,
                'details' => json_encode($details),
                'status' => 'pending'
            ]);

            // Calculate the remaining BV after withdrawal
            $remainingBV = $convertedBV - $request->amount;

            // Convert the remaining BV back to the bonus wallet equivalent if needed
            $remainingBonusWalletAmount = $remainingBV / systemSettings()->bv_equivalent;

            $bonusWallet->update([
                'amount' => $remainingBonusWalletAmount
            ]);

            $amount = '$' . $request->amount;

            UserActivities::create([
                'user_id' => $user->id,
                'log'  => "Places withdrawal request of {$amount}"
            ]);

            DB::commit();

            return $this->sendResponse([], "Withdrawal request was successful");
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e->getMessage());

            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }
}
