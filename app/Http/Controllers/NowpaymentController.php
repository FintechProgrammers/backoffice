<?php

namespace App\Http\Controllers;

use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use App\Models\Withdrawal;
use App\Services\NowpaymentsService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

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

    function payment($validated = null)
    {
        try {
            // $user = auth()->user();

            if (!empty($validated)) {

                $service = $validated['package'];

                $payload = [
                    'amount'           => $service->price,
                    'order_id'          => $validated['invoice']['order_id'],
                    'description'       => "{$service->name} Purchase",
                    'ipn_callback_url'  => config('contstants.nowpayment.ipn_base_url') . '/ipn/nowpayment/service/payment'
                ];

                $response = $this->nowpaymentService->createInvoice($payload);

                if (empty($response)) {
                    sendToLog($response);

                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => serviceDownMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY));
                }

                return $response['invoice_url'];
            } else {

                $user = auth()->user();

                $payload = [
                    'amount'           => systemSettings()->ambassador_fee,
                    'order_id'         => generateReference(),
                    'description'       => "Ambassador Account Setup",
                    'ipn_callback_url'  => config('contstants.nowpayment.ipn_base_url') . '/ipn/nowpayment/ambassador/payment'
                ];

                $response = $this->nowpaymentService->createInvoice($payload);

                if (empty($response)) {
                    sendToLog($response);

                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => serviceDownMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY));
                }

                Invoice::create([
                    'user_id'       => $user->id,
                    'order_id'      => $response['order_id'],
                    'is_paid'       => false
                ]);

                return $response['invoice_url'];
            }
        } catch (\Exception $e) {
            sendToLog($e);

            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    function validateAddress($validated,  $currency = 'usdttrc20')
    {
        try {

            $validateAddress = [
                'address'     => $validated['wallet_address'],
                'currency'    => $currency
            ];

            $verifyAddress = $this->nowpaymentService->validateAddress($validateAddress);

            if (empty($verifyAddress) || $verifyAddress['code'] != 'BAD_CREATE_WITHDRAWAL_REQUEST') {
                // sendToLog($verifyAddress);
                return throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => serviceDownMessage(),
                ], Response::HTTP_INTERNAL_SERVER_ERROR));
            }

            if ($verifyAddress['code'] == 'BAD_CREATE_WITHDRAWAL_REQUEST') {
                return throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => $verifyAddress['message'],
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
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

    function payout($validated, $provider, $currency = 'usdttrc20')
    {
        try {

            $payoutPayload = [
                'address'  => $validated['wallet_address'],
                'amount' => $validated['amount'],
                'currency' => $validated['currency'],
                'ipn_callback_url' => config('contstants.nowpayment.ipn_base_url') . '/ipn/nowpayment/payout'
            ];

            $details = [
                'wallet_address' => $validated['wallet_address'],
                'coin'           => 'USDT',
                'network'       =>  $currency
            ];

            // create transaction record
            $withdrawalRecord = Withdrawal::create([
                'cycle_id' => currentCycle(),
                'provider_id' => $provider->id,
                'reference' => generateReference(),
                'user_id'  => $validated['user']['id'],
                'amount' =>  $validated['amount'],
                'details' => json_encode($details),
                'status' => 'pending'
            ]);

            // Set `success` to false before DB transaction
            (bool) $success = false;

            \Illuminate\Support\Facades\DB::transaction(function () use ($payoutPayload, $withdrawalRecord, &$success) {

                $response = $this->nowpaymentService->payout($payoutPayload);

                if (empty($response) || isset($response['status'])) {
                    sendToLog($response);
                    return $this->sendError("Unable to complete your request at the moment", [], 400);
                }

                debitWallet($payoutPayload['amount']);

                // check payment status
                $withdrawal = $response['withdrawals']['0'];

                if (in_array($withdrawal['status'], ['WAITING', 'SENDING', 'PROCESSING'])) {
                    $status = "pending";
                } elseif ($withdrawal['status'] === 'FINISHED') {
                    $status = "completed";
                } else {
                    refundWallet($payoutPayload['amount']);
                    $status = "declined";
                }

                $withdrawalRecord->update([
                    'external_reference' => $withdrawal['id'],
                    'status'             => $status,
                ]);

                // Update success if all DB transactions were executed successfully
                $success = !$success;
            });

            return $success;
        } catch (\Exception $e) {
            sendToLog($e->getMessage());
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

        if (empty($decoded)) {
            sendToLog('Nowpayment sent an empty transaction webhook response payload.');

            return response('Payload is empty.', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        }

        // Compute the HMAC using the SHA-512 hash algorithm
        $signature = hash_hmac('SHA512', $data, config('contstants.nowpayment.ipn_key'));

        $headerKey = $request->headers->get('x-nowpayments-sig');

        // Verify signature
        if ($headerKey !== $signature) {
            sendToLog('Nowpayment payout webhook unauthorized access.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }

    function ambassadorIpn(Request $request)
    {
        $data = trim(file_get_contents('php://input'), "\xEF\xBB\xBF");
        // Decode the contents from the webhook response
        $decoded = json_decode(mb_convert_encoding($data, 'UTF-8', 'UTF-8'), true, 512, JSON_THROW_ON_ERROR);

        if (empty($decoded)) {
            sendToLog('Nowpayment sent an empty transaction webhook response payload.');

            return response('Payload is empty.', Response::HTTP_OK)->header('Content-Type', 'text/plain');
        }

        // Compute the HMAC using the SHA-512 hash algorithm
        $signature = hash_hmac('SHA512', $data, config('contstants.nowpayment.ipn_key'));

        $headerKey = $request->headers->get('x-nowpayments-sig');

        // Verify signature
        if ($headerKey !== $signature) {
            sendToLog('Nowpayment payout webhook unauthorized access.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        // GEt the invoice
        $invoice = Invoice::where('order_id', $decoded['order_id'])->whereNull('service_id')->where('is_paid', false)->first();

        if (empty($invoice)) {
            sendToLog('Nowpayment invoice not found.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        $invoice->update([
            'is_paid' => true
        ]);

        $user = $invoice->user;

        if (!$user->is_ambassador) {

            AmbassedorPayments::create([
                'reference' => generateReference(),
                'user_id' => $user->id,
                'amount'  => $invoice->amount
            ]);

            $user->update([
                'is_ambassador' => true
            ]);

            Bonus::create([
                'user_id' => $user->id,
                'amount' => 0
            ]);

            UserActivities::create([
                'user_id' => $user->id,
                'log'  => 'Payment for Ambassador Account.'
            ]);
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
        $signature = hash_hmac('SHA512', $data, config('contstants.nowpayment.ipn_key'));

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

        $user = $invoice->user;

        $package = Service::find($invoice->service_id);

        if (empty($package)) {
            sendToLog('Package not found.');
            sendToLog('Nowpayment payout response: ' . json_encode($decoded));

            return response()->json([], Response::HTTP_UNAUTHORIZED);
        }

        $subscriptionService = new SubscriptionService();

        $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $package->id)->first();

        if ($userSubscription) {
            if (!$userSubscription->is_active) {
                $subscriptionService->updateSubscription($package, $userSubscription);
            }
        } else {
            $subscriptionService->createSubscription($package, $user);
        }

        $invoice->update([
            'is_paid' => true
        ]);

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }
}
