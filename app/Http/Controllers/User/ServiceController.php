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

    function purchase(Service $service, StripeService $stripeService)
    {
        try {

            $data = [
                'currency' => 'usd',
                'amount' => $service->price * 100,
                'product_data' => [
                    'name' => $service->name
                ],
                'success_url' => route('stripe.success'),
                'cancel_url' => route('stripe.cancel')
            ];

            $response = $stripeService->processCheckout($data);

            logger($response);

            dd($response);

            return $this->sendResponse(['route' => $response->url], "Success");
        } catch (\Exception $e) {
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }
}
