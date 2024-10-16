<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AllSignalResource;
use App\Models\ServiceStreamer;
use App\Models\Signal;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    function index(Request $request)
    {
        try {
            $user = $request->user();

            // Get all streamer_ids related to the user's subscriptions
            $educators = ServiceStreamer::whereIn('service_id', function ($query) use ($user) {
                $query->select('service_id')
                    ->from('user_subscriptions')
                    ->where('user_id', $user->id);
            })->distinct()->pluck('streamer_id');

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
