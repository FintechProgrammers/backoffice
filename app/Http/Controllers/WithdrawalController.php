<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Jobs\Mail\OtpMail;
use App\Models\Provider;
use App\Models\UserActivities;
use App\Models\UserOtp;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Traits\RecursiveActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalController extends Controller
{
    use RecursiveActions;

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
        $data['cryptoProvider'] = Provider::where('is_active', true)->where('is_crypto', true)->where('can_payout', true)->where('is_default', true)->first();
        $data['bankTransferProvider'] = Provider::where('is_active', true)->where('is_crypto', false)->where('can_payout', true)->where('is_default', true)->first();

        return view('user.withdrawal.create', $data);
    }

    function sendOTP(Request $request)
    {
        $user = auth()->user();

        $mail = [
            'name' => ucfirst($user->name),
            'email' => $user->email,
            'content' => '',
            'token' => $this->generateUserOtp($user->id, 'withdrawal_request'),
        ];

        dispatch(new OtpMail($mail));

        return view('user.withdrawal._token-input');
    }

    function store(WithdrawalRequest $request)
    {
        try {

            DB::beginTransaction();

            $user = $request->user();

            $code = UserOtp::where('token', $request->token)
                ->where('purpose', 'withdrawal_request')
                ->where('user_id', $user->id)
                ->where('created_at', '>', now()->subMinute())
                ->first();

            if (!$code) {
                return $this->sendError('Invalid token', Response::HTTP_UNAUTHORIZED);
            }

            $wallet = Wallet::where('user_id', $user->id)->first();

            $convertedBV = $wallet->amount * systemSettings()->bv_equivalent;

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

            $wallet->update([
                'amount' => $remainingBonusWalletAmount
            ]);

            $amount = '$' . $request->amount;

            UserActivities::create([
                'user_id' => $user->id,
                'log'  => "Places withdrawal request of {$amount}"
            ]);

            $code->delete();

            DB::commit();

            return $this->sendResponse([], "Withdrawal request was successful");
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e->getMessage());

            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }
}
