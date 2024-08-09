<?php

namespace App\Http\Controllers;

use App\Http\Requests\WithdrawalRequest;
use App\Models\Provider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserOtp;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Notifications\WithdrawalToken;
use App\Traits\RecursiveActions;
use Carbon\Carbon;
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

        $dateFrom = ($request->filled('date_from')) ? Carbon::parse($request->date_from)->startOfDay() : null;

        $dateTo = ($request->filled('date_to')) ? Carbon::parse($request->date_to)->endOfDay() : null;

        $search = $request->filled('search') ? $request->search : null;
        $status = $request->filled('status')  ? $request->status : null;

        $type = $request->filled('type') ? $request->type : null;

        $query = Transaction::query();

        $query->where('user_id', Auth::user()->id)->when(!empty($search), fn($query) => $query->where('internal_reference', 'LIKE', "%{$search}%"))
            ->when(!empty($status), fn($query) => $query->where('status', $status))
            ->when(!empty($type), fn($query) => $query->where('type', $type))
            ->when(!empty($dateFrom) && !empty($dateTo), fn($query) => $query->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($status) && !empty($dateFrom) && !empty($dateTo), fn($query) => $query->where('status', $status)->whereBetween('created_at', [$dateFrom, $dateTo]))
            ->when(!empty($type) && !empty($dateFrom) && !empty($dateTo), fn($query) =>  $query->where('type', $type)->whereBetween('created_at', [$dateFrom, $dateTo]));

        $data['withdrawals'] = $query->paginate(50);

        return view('user.withdrawal._table', $data);
    }

    function create()
    {
        $data['paymentMethods'] = Provider::where('is_active', true)->where('can_payout', true)->get();

        return view('user.withdrawal.create', $data);
    }

    function sendOTP(Request $request)
    {
        try {
            $user = User::where('id', auth()->user()->id)->first();

            $mail = [
                'name' => ucfirst($user->full_name),
                'code' => $this->generateUserOtp($user->id, 'withdrawal_request'),
            ];

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

        if ($validated->amount > $wallet->balance) {
            return $this->sendError("Insufficient funds to place withdrawal", []);
        }

        // get provider information
        $provider = Provider::whereUuid($validated->provider_id)->first();

        if (!$provider) {
            return $this->sendError(serviceDownMessage(), [], 500);
        }

        $openingBalance = $wallet->balance;

        $closingBalance = $wallet->balance - $validated->amount;

        $Withdrawal = [
            'internal_reference' => generateReference(),
            'user_id'  => $user->id,
            'associated_user_id' => $user->id,
            'amount' =>  $validated->amount,
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

        $code->delete();

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
        } elseif ($provider->short_name === 'masspaynexio') {
            $nexio = new \App\Http\Controllers\NexioController();

            return $nexio->payout($validated);
        }

        return $this->sendError(serviceDownMessage(), [], 500);
    }
}
