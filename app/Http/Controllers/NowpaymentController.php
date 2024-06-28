<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Service;
use App\Services\NowpaymentsService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;

class NowpaymentController extends Controller
{
    function iniatePayment($service = null)
    {
        // $route = route('nowpayment.layout', ['service' => $service->uuid]);

        // return $route;
        try {
            $nowpaymentService = new NowpaymentsService();

            $user = auth()->user();

            if (!empty($service)) {

                $payload = [
                    'amount'           => $service->price,
                    'order_id'          => generateReference(),
                    'description'       => "{$service->name} Purchase",
                    'ipn_callback_url'  => route('ipn.nowpayment.service')
                ];

                $response = $nowpaymentService->createInvoice($payload);

                if (empty($response)) {
                    sendToLog($response);

                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => serviceDownMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY));
                }

                Invoice::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'order_id'   => $response['order_id'],
                    'is_paid' => false
                ]);

                return $response['invoice_url'];
            } else {

                $payload = [
                    'amount'           => systemSettings()->ambassador_fee,
                    'order_id'         => generateReference(),
                    'description'       => "Ambassedor Account Setup",
                    'ipn_callback_url'  => route('ipn.nowpayment.abassador')
                ];

                $response = $nowpaymentService->createInvoice($payload);

                if (empty($response)) {
                    sendToLog($response);

                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => serviceDownMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY));
                }

                Invoice::create([
                    'user_id' => $user->id,
                    'order_id'   => $response['order_id'],
                    'is_paid' => false
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

    function ipn(Request $request)
    {
    }

    function abassadorIpn(Request $request)
    {
    }

    function serviceIpn(Request $request)
    {
    }
}
