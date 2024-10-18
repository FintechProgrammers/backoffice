<?php

namespace App\Http\Controllers;

use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserInfo;
use App\Models\UserSubscription;
use App\Notifications\NewAccountCreated;
use App\Notifications\WithdrawalNotification;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;

class NowpaymentController extends Controller
{

    protected $nowpaymentService;

    function __construct()
    {
        $this->nowpaymentService = new \App\Services\NowpaymentsService();
    }

    function create($serviceId = null)
    {
        if (!empty($serviceId)) {
            $service = Service::whereUuid($serviceId)->first();
            $data['service'] = $service;
        } else {
            $data['service'] = null;
        }

        $data['orderId'] = generateReference();

        return view('nowpayments.create', $data);
    }

    function payment($validated)
    {
        try {

            $service = $validated['package'];

            $payload = [
                'amount'           => $service->price,
                'order_id'          => $validated['invoice']['order_id'],
                'description'       => "{$service->name} Purchase",
                'ipn_callback_url'  => config('constants.nowpayment.ipn_base_url') . '/ipn/nowpayment/service/payment'
            ];

            $response = $this->nowpaymentService->createInvoice($payload);

            if (!$response['success']) {
                sendToLog($response);

                return throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => serviceDownMessage(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            $response = $response['data'];

            return $response['invoice_url'];
        } catch (\Exception $e) {
            sendToLog($e);

            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    function validateAddress($validated)
    {
        try {

            $validateAddress = [
                'address'     => $validated->wallet_address,
                'currency'    => $validated->currency
            ];

            $verifyAddress = $this->nowpaymentService->validateAddress($validateAddress);

            if (!$verifyAddress['success'] && $verifyAddress['code'] == 'BAD_ADDRESS_VALIDATION_REQUEST') {
                throw new HttpResponseException($this->sendError($validateAddress['message'], [], Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            if (!$validateAddress['success']) {
                throw new HttpResponseException($this->sendError(serviceDownMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR));
            }

            return true;
        } catch (\Exception $e) {
            sendToLog($e->getMessage());
            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    function payout($validated)
    {
        try {

            $payload = $validated->transaction_payload;

            $payoutPayload = [
                'address'  => $validated->wallet_address,
                'amount' => $validated->amount,
                'currency' => $validated->currency,
                'ipn_callback_url' => config('constants.nowpayment.ipn_base_url') . '/ipn/nowpayment/payout'
            ];

            $addressValidation = $this->nowpaymentService->validateAddress($payoutPayload);

            if (!$addressValidation) {
                return $this->sendError("Invalid payment address");
            }

            $response = $this->nowpaymentService->payout($payoutPayload);

            if (!$response['success']) {
                sendToLog($response);

                $payload['status'] = 'declined';
                $payload['external_reference'] = '';
            } else {

                $response = $response['data'];

                // check payment status
                $withdrawal = $response['withdrawals']['0'];

                $payload['external_reference'] = !empty($withdrawal['id']) ? $withdrawal['id'] : '';
                // $payload['response'] = json_encode($response);

                if (in_array($withdrawal->status, ['WAITING', 'SENDING', 'PROCESSING'])) {
                    $payload['status'] = "pending";
                } elseif ($withdrawal->status === 'FINISHED') {
                    $payload['status'] = "completed";
                } else {
                    $payload['status'] = "failed";
                }
            }

            $payload['payload'] = json_encode([
                'wallet_address' => $validated->wallet_address,
                'coin'           => 'USDT',
                'network'       =>  $validated->currency
            ]);

            $wallet = $validated->wallet;

            // Set `success` to false before DB transaction
            (bool) $success = false;

            \Illuminate\Support\Facades\DB::transaction(function () use ($payload, $validated, $wallet, &$success) {

                Transaction::create($payload);

                $wallet->update([
                    'balance' => $payload['closing_balance']
                ]);

                if (strtolower($payload['status']) === 'failed') {
                    refundWallet($validated->amount);
                }

                // Update success if all DB transactions were executed successfully
                $success = !$success;
            });

            return $success;
        } catch (\Exception $e) {
            sendToLog($e);
            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    function payoutIpn(Request $request)
    {
        $data = trim(file_get_contents('php://input'), "\xEF\xBB\xBF");
        // Decode the contents from the webhook response
        $decoded = json_decode(mb_convert_encoding($data, 'UTF-8', 'UTF-8'), true, 512, JSON_THROW_ON_ERROR);

        if (empty($decoded)) {
            sendToLog('Nowpayment sent an empty transaction webhook response payload.');

            return response('Payload is empty.', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        }

        // Compute the HMAC using the SHA-512 hash algorithm
        $signature = hash_hmac('SHA512', $data, config('constants.nowpayment.ipn_key'));

        $headerKey = $request->headers->get('x-nowpayments-sig');

        // Verify signature
        if ($headerKey !== $signature) {
            sendToLog('Nowpayment payout webhook unauthorized access.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        $transaction = Transaction::where('external_reference', $decoded['id'])->where('status', 'pending')->first();

        if (!$transaction) {
            sendToLog('Nowpayment transaction not fund: ' . json_encode($decoded));

            return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        }

        if ($decoded['status'] === 'FINISHED') {
            $status = "completed";
        } else if (in_array($decoded['status'], ['FAILED', 'REJECTED'])) {
            $status = "failed";
        } else {
            $status = "pending";
        }

        $transaction->update([
            'status' => $status,
            'response' => json_encode($decoded)
        ]);

        $user = $transaction->user;

        $data = [
            'name' => $user->full_name,
            'amount' => $transaction->amount,
            'status' => $status, // 'success' or 'failed'
        ];

        $transaction->update([
            'status' => $status,
            'response' => json_encode($decoded)
        ]);

        if ($status === "failed") {
            $wallet = \App\Models\Wallet::where('user_id', $user->id)->first();

            $newBalance = $wallet->balance + $transaction->amount;

            $wallet->update([
                'balance' => $newBalance,
            ]);
        }

        if (in_array($status, ['completed', 'failed'])) {
            $user->notify(new WithdrawalNotification($data));
        }

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }

    function serviceIpn(Request $request)
    {
        $data = trim(file_get_contents('php://input'), "\xEF\xBB\xBF");
        // Decode the contents from the webhook response
        $decoded = json_decode(mb_convert_encoding($data, 'UTF-8', 'UTF-8'), true, 512, JSON_THROW_ON_ERROR);

        if (empty($decoded)) {
            sendToLog('Nowpayment sent an empty transaction webhook response payload.');

            return response('Payload is empty.', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        }

        // Compute the HMAC using the SHA-512 hash algorithm
        $signature = hash_hmac('SHA512', $data, config('constants.nowpayment.ipn_key'));

        $headerKey = $request->headers->get('x-nowpayments-sig');

        // Verify signature
        if ($headerKey !== $signature) {
            sendToLog('Nowpayment payout webhook unauthorized access.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        // GEt the invoice
        $invoice = Invoice::where('order_id', $decoded['order_id'])->where('is_paid', false)->first();

        if (empty($invoice)) {
            sendToLog('Nowpayment invoice not found.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        if ($decoded['payment_status'] == "finished") {

            if (empty($invoice->user)) {
                $userPayload = json_decode($invoice->user_payload);

                $password = \Illuminate\Support\Str::random(5);

                $user = User::create([
                    'first_name' => $userPayload->first_name,
                    'last_name' => $userPayload->last_name,
                    'email' => $userPayload->email,
                    'username' => $userPayload->username,
                    'parent_id'  => $userPayload->parent_id,
                    'password' => Hash::make($password)
                ]);

                UserInfo::where('user_id', $user->id)->update([
                    'country_code' => $userPayload->country,
                ]);

                $mailData = [
                    'username' => $user->username,
                    'name' => $user->first_name,
                    'password' => $password
                ];

                $user->notify(new NewAccountCreated($mailData));
            } else {
                $user = $invoice->user;
            }

            $package = Service::find($invoice->service_id);

            if (empty($package)) {
                sendToLog('Package not found.');
                sendToLog('Nowpayment payout response: ' . json_encode($decoded));

                return response()->json([], Response::HTTP_UNAUTHORIZED);
            }

            $subscriptionService = new SubscriptionService();

            $subscriptionService->startService($package, $user);

            $invoice->update([
                'user_id' => $user->id,
                'is_paid' => true
            ]);
        }

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }
}
