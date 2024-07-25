<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use App\Models\Wallet;
use App\Models\Withdrawal;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class WalletController extends Controller
{
    function payment($validated)
    {
        try {
            DB::beginTransaction();

            $user = $validated['user'];

            $service = $validated['package'];

            $invoice = $validated['invoice'];

            // get commission wallet
            $wallet = Wallet::where('user_id', $validated['payer'])->first();

            if (!$wallet) {
                return $this->sendError(serviceDownMessage(), [], 400);
            }

            if ($service->price > $wallet->amount) {
                return $this->sendError("Insufficient funds", [], 400);
            }

            $wallet->update([
                'amount' => $wallet->amount - $service->price,
            ]);

            $invoice->update([
                'is_paid' => true
            ]);

            $openingBalance = $wallet->amount;

            $closingBalance = $wallet->amount - $validated->amount;

            $Withdrawal = [
                'internal_reference' => generateReference(),
                'user_id'  => $wallet->user_id,
                'associated_user_id' => $user->id,
                'amount' =>  $validated->amount,
                'cycle_id' => currentCycle(),
                'provider_id' => null,
                'type' => 'withdrawal',
                'narration' => 'Service payment',
                'action' => 'debit',
                'status' => 'completed',
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance
            ];

            Withdrawal::create($Withdrawal);

            $subscriptionService = new SubscriptionService();

            $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $service->id)->first();

            if ($userSubscription) {
                $subscriptionService->updateSubscription($service, $userSubscription);
            } else {
                $subscriptionService->createSubscription($service, $user);
            }

            DB::commit();

            return route('payment.success');
        } catch (\Exception $e) {
            DB::rollBack();

            sendToLog($e);

            return $this->sendError(serviceDownMessage(), [], 500);
        }
    }

    function failedProcess($message)
    {
        return throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message
        ], Response::HTTP_SERVICE_UNAVAILABLE));
    }
}
