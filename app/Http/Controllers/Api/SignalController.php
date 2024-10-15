<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllSignalResource;
use App\Models\Signal;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    function index()
    {
        try {
            // get list of eductors user is following

            $educators = [];

            $signals = Signal::whereIn('streamer_id', $educators)->latest()->paginate(30);

            $signals = AllSignalResource::collection($signals);

            return $this->sendResponse($signals);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }

    function show($id)
    {
        try {
            $signal = Signal::where('uuid', $id)->firstOrFail();

            $signal = new AllSignalResource($signal);

            return $this->sendResponse($signal);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }
}
