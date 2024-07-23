<?php

namespace App\Http\Controllers;

use App\Models\MasspayUser;
use App\Models\Transaction;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MassPaymentController extends Controller
{
    protected $massPay;

    function __construct()
    {
        $this->massPay = new \App\Services\MassPayService();
    }

    function makeWithdrawal($validated)
    {
        try {

            $amount = $validated->amount;

            // check main wallet has balance that can handle the transaction
            $walletBalanceResponse = $this->massPay->getBalance();

            if (!$walletBalanceResponse['success']) {
                return $this->failedResponse();
            }

            $walletBalanceResponse = $walletBalanceResponse['data'];

            if ($amount > $walletBalanceResponse['balance']) {
                sendToLog("MassPay don't have enough funds");

                return $this->failedResponse();
            }

            $sourceToken = $walletBalanceResponse['token'];

            // check if user exist
            $massPayUser = MasspayUser::where('user_id', $validated->user->id)->first();

            if (!$massPayUser) {
                $userData = [
                    'internal_user_id' => $validated->user->uuid,
                    'country_code' => $validated->user->userProfile->country->iso3,
                    'first_name' => $validated->user->first_name,
                    'last_name' => $validated->user->last_name,
                    'email' => $validated->user->email
                ];

                // create user account
                $userResponse = $this->massPay->createUser($userData);

                if (!$userResponse['success']) {
                    return $this->failedResponse();
                }

                $userResponse = $userResponse['data'];

                $massPayUser = MasspayUser::create([
                    'user_id' => $validated->user->id,
                    'user_token' => $userResponse['user_token'],
                    'internal_user_id' => $userResponse['internal_user_id'],
                    'activation_url' => $userResponse['activation_url'],
                    'response' => json_encode($userResponse)
                ]);
            }

            if (empty($massPayUser->wallet_token)) {
                // get user wallet
                $walletResponse = $this->massPay->getUserWallet($massPayUser->user_token);

                if (!$walletResponse['success']) {
                    return $this->failedResponse();
                }

                $walletResponse = $walletResponse['data'];

                $massPayUser->update([
                    'wallet_token' => $walletResponse['token']
                ]);

                $massPayUser->refresh();
            }

            $payload = $validated->transaction_payload;

            $data = [
                'client_transfer_id' => $payload['internal_reference'],
                'source_token' => $sourceToken,
                'destination_token' => $massPayUser->wallet_token,
                'source_amount' => $amount,
                'destination_amount' => $amount
            ];

            $response = $this->massPay->initiateTransaction($data);

            if (!$response['success']) {
                return $this->failedResponse();
            }

            $response = $response['data'];
            $payload['external_reference'] = $response['payout_token'];

            if (in_array($response['status'], ['PENDING', 'PROCESSING', 'SCHEDULED'])) {
                $payload['status'] = 'pending';
            } else if (in_array($response['status'], ['CANCELLED', 'ERROR'])) {
                $payload['status'] = 'failed';
            } else if ($response['status'] === 'COMPLETED') {
                $payload['status'] = 'completed';
            }

            (bool) $success = false;

            \Illuminate\Support\Facades\DB::transaction(function () use ($payload, $validated, &$success) {

                Transaction::create($payload);

                $wallet = $validated->wallet;

                if (in_array($payload['status'], ['completed', 'pending'])) {
                    $wallet->update([
                        'amount' => $wallet->amount - $validated->amount
                    ]);
                }

                // Update success if all DB transactions were executed successfully
                $success = !$success;
            });

            if ($success) {
                return $this->sendResponse("Your withdrawal has been received successfully.");
            } else {
                return $this->sendError(serviceDownMessage(), [], Response::HTTP_SERVICE_UNAVAILABLE);
            }
        } catch (\Exception $e) {
            sendToLog($e->getMessage());
            return $this->failedResponse();
        }
    }

    function failedResponse()
    {
        return throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => serviceDownMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR));
    }
}
