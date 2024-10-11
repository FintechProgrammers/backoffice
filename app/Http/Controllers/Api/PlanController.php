<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    function index()
    {
        $products = Service::where('is_published', true)->get();

        $products = ServiceResource::collection($products);

        return $this->sendResponse($products);
    }

    function show(Service $service)
    {
        $product = new ServiceResource($service->first());

        return $this->sendResponse($product);
    }
}
