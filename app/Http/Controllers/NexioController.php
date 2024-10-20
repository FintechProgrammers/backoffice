<?php

namespace App\Http\Controllers;

use App\Models\NexioUser;
use App\Models\Provider;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\WithdrawalNotification;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class NexioController extends Controller
{
    protected $nexio;

    function __construct()
    {
        $this->nexio = new \App\Services\NexioService();
    }

    function createUser($validated)
    {
        try {

            $payload = [
                'recipient_id'  => generateReference(),
                'email' => $validated->user->email,
                'first_name' => $validated->user->first_name,
                'last_name' => $validated->user->last_name,
                'country_code' => $validated->user->userProfile->country->iso2,
            ];

            $response = $this->nexio->createUser($payload);

            if (!$response['success']) {
                if ($response['error'] === 427 || $response['message'] == "Duplicate email for nexio") {
                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => "Kindly request support to setup your Nexio ID",
                    ], Response::HTTP_INTERNAL_SERVER_ERROR));
                } else {
                    return $this->failedResponse();
                }
            }

            $response = $response['data'];

            $nexioUser = NexioUser::create([
                'user_id' => $validated->user->id,
                'recipient_id' => $response['recipientId'],
                'provider_id' => $response['providerId'],
                'provider_recipient_ref' => $response['providerRecipientRef'],
                'payout_account_id' => $response['payoutAccountId'],
                'recipient_ref' => $response['recipientRef'],
                'response' => json_encode($response)
            ]);

            return $nexioUser;
        } catch (\Exception $e) {
            sendToLog($e->getMessage());
            return $this->failedResponse();
        }
    }

    function payout($validated)
    {
        try {

            $nexioUser = NexioUser::where('user_id', $validated->user->id)->first();

            if (!$nexioUser) {
                $nexioUser = $this->createUser($validated);
            }

            $payoutPayload = [
                'amount' => (float) $validated->amount,
                'narration' => 'External withdrawal',
                'reference' => $validated->transaction_payload['internal_reference'],
                'recipientId' => $nexioUser->recipient_id
            ];

            $response = $this->nexio->payouts($payoutPayload);

            if (!$response['success']) {
                return $this->failedResponse();
            }

            $response = $response['data'];

            $wallet = $validated->wallet;

            $transaction = $validated->transaction_payload;

            $transaction = Transaction::create($transaction);

            (bool) $success = false;

            \Illuminate\Support\Facades\DB::transaction(function () use ($transaction, $wallet, &$success) {

                $wallet->update([
                    'balance' => $transaction['closing_balance']
                ]);

                // Update success if all DB transactions were executed successfully
                $success = !$success;
            });

            if ($success) {
                return $this->sendResponse([], "Your withdrawal has been received successfully.");
            } else {
                return $this->sendError(serviceDownMessage(), [], Response::HTTP_SERVICE_UNAVAILABLE);
            }
        } catch (\Exception $e) {
            sendToLog($e->getMessage());
            return $this->failedResponse();
        }
    }

    function webhook()
    {
        try {
            $data = trim(file_get_contents('php://input'), "\xEF\xBB\xBF");

            $decoded = json_decode(mb_convert_encoding($data, 'UTF-8', 'UTF-8'), true, 512, JSON_THROW_ON_ERROR);

            $eventType = $decoded['eventType'];

            if ($eventType === "PAYOUT") {
                // get payout record
                $payout = $this->nexio->getPayout($decoded['payoutId']);

                if (!$payout['success']) {
                    sendToLog("Naxio Payout with id {$decoded['payoutId']} no found");
                } else {
                    $payout = $payout['data'];

                    $transaction = Transaction::where('internal_reference', $payout['payoutRef'])->where('status', 'pending')->first();

                    if (!$transaction) {
                        sendToLog("Transaction {$payout['payoutRef']} no fund");

                        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
                    }

                    $status = null;

                    if ($payout['payoutStatus'] === 10) {
                        $status = "pending";
                    } elseif ($payout['payoutStatus'] === 20) {
                        $status = "completed";
                    } elseif ($payout['payoutStatus'] === 90) {
                        $status = "failed";
                    } elseif ($payout['payoutStatus'] === 30) {
                        $status = "failed";
                    } else {
                        $status = "pending";
                    }

                    $user = $transaction->user;

                    $data = [
                        'name' => $user->full_name,
                        'amount' => $transaction->amount,
                        'status' => $status, // 'success' or 'failed'
                    ];

                    $transaction->update([
                        'status' => $status,
                        'response' => json_encode($payout)
                    ]);

                    if ($status === "failed") {
                        $wallet = \App\Models\Wallet::where('user_id', $user->id)->first();

                        $newBalance = $wallet->balance + $transaction->amount;

                        $wallet->update([
                            'balance' => $newBalance,
                        ]);
                    }

                    $user->notify(new WithdrawalNotification($data));
                }
            }

            return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        } catch (\UnexpectedValueException $e) {
            logger($e->getMessage());
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            logger($e->getMessage());
            exit();
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
