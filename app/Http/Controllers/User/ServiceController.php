<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\StripeService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    function index()
    {
        $data['services'] = Service::where('is_published', true)->latest()->get();

        return view('user.package.index', $data);
    }

    function show(Service $service)
    {
        $data['package'] = $service;
        return view('user.package.details', $data);
    }

    function purchase(Request $request,Service $service, StripeService $stripeService)
    {
        try {
            $user = $request->user();

            $data = [
                'currency' => 'usd',
                'amount' => $service->price * 100,
                'product_data' => [
                    'name' => $service->name
                ],
                'customer_email' => $user->email,
                'metadata' => ['user_id' => $user->id, 'service_id'=>$service->id],
                'success_url' => route('stripe.service.payment.Success').'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.cancel')
            ];

            $response = $stripeService->processCheckout($data);

            return $this->sendResponse(['route' => $response->url], "Success");
        } catch (\Exception $e) {
            sendToLog($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }
}
