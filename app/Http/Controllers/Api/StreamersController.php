<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StreamerDetailsResource;
use App\Http\Resources\StreamerResource;
use App\Models\Service;
use App\Models\ServiceStreamer;
use App\Models\Streamer;
use Illuminate\Http\Request;

class StreamersController extends Controller
{
    function index(Service $service)
    {
        $streamers = ServiceStreamer::where('service_id', $service->id)->get();

        $streamers = StreamerResource::collection($streamers);

        return $this->sendResponse($streamers, "List of streamers based on services");
    }

    function details(Streamer $streamer)
    {
        $streamerData = new StreamerDetailsResource($streamer);

        return $this->sendResponse($streamerData, "Streamer details");
    }
}
