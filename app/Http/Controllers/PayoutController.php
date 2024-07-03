<?php

namespace App\Http\Controllers;

use App\Http\Requests\CryptoPayoutRequest;
use App\Models\Provider;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class PayoutController extends Controller
{
    public const ERROR_RESPONSE = 'We apologize, but at this time we are unable to complete your funds transfer request.';

    public const TRANSACTION_PENDING = 'Transaction has been received for processing.';

    public const INSUFFICIENT_WALLET_BALANACE = 'Insuficient wallet balance.';

    function crypto(CryptoPayoutRequest $request, Provider $provider)
    {

        $validated = $request->validated();
        $user = $request->user();

        $validated['user'] = $user;

        // get user wallet
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            sendToLog("Wallet for {$user->id} not found");
            return $this->sendError(self::ERROR_RESPONSE, [], 400);
        }

        // check amount wallet amount to requested amount
        if ($validated['amount'] > $wallet->amount) {
            return $this->sendError(self::INSUFFICIENT_WALLET_BALANACE, [], 400);
        }

        if ($provider->short_name == 'nowpayment') {
            $nonwpayments = new \App\Http\Controllers\NowpaymentController();

            $nonwpayments->validateAddress($validated);

            if ($nonwpayments->payout($validated, $provider)) {
                return $this->sendResponse([], self::TRANSACTION_PENDING, 201);
            }
        }

        return $this->sendError(self::ERROR_RESPONSE, [], 500);
    }

    function bankTransfer(Provider $provider)
    {
    }
}
