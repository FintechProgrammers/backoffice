<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\Service;
use Illuminate\Http\Request;

class ProviderController extends Controller
{
    function payinProvider($serviceId = null)
    {

        if (!empty($serviceId)) {
            $service = Service::whereUuid($serviceId)->where('is_published', true)->first();

            if (!$service) {
                return $this->sendError("Package not found.", [], 404);
            }

            $data['service'] = $service;
        } else {
            $data['service'] = null;
        }

        return view('user.provider.payins', $data);
    }

    function getDefaultCardProvider(Request $request)
    {
        if ($request->filled('service_id')) {
            $service = Service::whereUuid($request->service_id)->where('is_published', true)->first();

            if (!$service) {
                return $this->sendError("Package not found.", [], 404);
            }
        } else {
            $service = null;
        }

        $provider = Provider::where('is_active', true)->where('is_crypto', false)->where('can_payin', true)->where('is_default', true)->first();

        if (!$provider) {
            return $this->sendError("Unable to complete your request at the momment", [], 400);
        }

        if ($provider->short_name == 'strip') {
            $stripController = new \App\Http\Controllers\StripeController();

            $route = $stripController->payment($service);
        } else {
            return $this->sendError("Unable to complete your request at the momment", [], 400);
        }

        return $this->sendResponse(['route' => $route]);
    }

    function getDefaultCryptoProvider(Request $request)
    {
        if ($request->filled('service_id')) {
            $service = Service::whereUuid($request->service_id)->where('is_published', true)->first();

            if (!$service) {
                return $this->sendError("Package not found.", [], 404);
            }
        } else {
            $service = null;
        }

        $provider = Provider::where('is_active', true)->where('is_crypto', true)->where('can_payin', true)->where('is_default', true)->first();

        if (!$provider) {
            return $this->sendError("Unable to complete your request at the momment", [], 400);
        }

        if ($provider->short_name == 'nowpayment') {
            $nowpyamnet = new \App\Http\Controllers\NowpaymentController();

            $route = $nowpyamnet->payment($service);
        } else {
            return $this->sendError("Unable to complete your request at the momment", [], 400);
        }

        return $this->sendResponse(['route' => $route]);
    }

    function payoutProvider()
    {
    }

    function provider()
    {
        $providers = Provider::where('is_active', true)->where('can_payin', true)->get();

        return view('user.provider.index', ['providers' => $providers]);
    }
}
