<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Models\Provider;
use App\Models\Transaction;
use App\Models\UserActivities;
use App\Models\UserOtp;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalToken;
use App\Traits\RecursiveActions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class WithdrawalController extends Controller
{
    use RecursiveActions;

    public const ERROR_RESPONSE = 'We apologize, but at this time we are unable to complete your request.';
    public const TRANSACTION_PENDING = 'Transaction has been received for processing.';
    public const INSUFFICIENT_WALLET_BALANCE = 'Insufficient wallet balance.';

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
        try {
            $user = auth()->user();

            $mail = [
                'name' => ucfirst($user->name),
                'email' => $user->email,
                'content' => 'We have received your request to withdraw funds from your account.
                 To proceed with this request, please use the following One-Time Password (OTP) to verify your identity:',
                'code' => $this->generateUserOtp($user->id, 'withdrawal_request'),
            ];

            // Mail::to($user->email)->send(new \App\Mail\OtpMail($mail));

            $user->notify(new WithdrawalToken($mail));

            return view('user.withdrawal._token-input');
        } catch (\Exception $e) {
            sendToLog($e);
            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }

    function store(WithdrawalRequest $request)
    {
        $validated = (object)$request->validated();

        $user = $request->user();

        $validated->user = $user;

        $code = UserOtp::where('token', $validated->token)
            ->where('purpose', 'withdrawal_request')
            ->where('user_id', $user->id)
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if (!$code) {
            return $this->sendError('Invalid token', Response::HTTP_UNAUTHORIZED);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        if ($validated->amount > $wallet->amount) {
            return $this->sendError("Insufficient funds to place withdrawal", []);
        }

        // get provider information
        $provider = Provider::whereUuid($validated->provider_id)->first();

        if (!$provider) {
            return $this->sendError(serviceDownMessage(), [], 500);
        }

        $openingBalance = $wallet->amount;

        $closingBalance = $wallet->amount - $validated->amount;

        $Withdrawal = [
            'internal_reference' => generateReference(),
            'user_id'  => $user->id,
            'associated_user_id' => $user->id,
            'amount' =>  $validated->amount,
            'cycle_id' => currentCycle(),
            'provider_id' => $provider->id,
            'type' => 'withdrawal',
            'narration' => 'External Withdrawal',
            'action' => 'debit',
            'status' => 'pending',
            'opening_balance' => $openingBalance,
            'closing_balance' => $closingBalance
        ];

        $amount = '$' . $validated->amount;

        UserActivities::create([
            'user_id' => $user->id,
            'log'  => "Places withdrawal request of {$amount}"
        ]);

        // $code->delete();

        $validated->provider = $provider;
        $validated->wallet = $wallet;
        $validated->transaction_payload = $Withdrawal;

        if ($provider->short_name == 'nowpayment') {
            $nowpyamnet = new \App\Http\Controllers\NowpaymentController();

            $validated->currency = 'usdttrc20';

            return ($nowpyamnet->payout($validated, $Withdrawal)) ? $this->sendResponse([], self::TRANSACTION_PENDING) : $this->sendError(serviceDownMessage(), [], 500);
        } elseif ($provider->short_name === 'masspay') {
            $masspay = new \App\Http\Controllers\MassPaymentController();

            return $masspay->makeWithdrawal($validated);
        }

        return $this->sendError(serviceDownMessage(), [], 500);
    }
}
