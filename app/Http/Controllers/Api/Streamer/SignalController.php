<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Events\SignalNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\AllSignalResource;
use App\Http\Resources\SignalResource;
use App\Models\Category;
use App\Models\Signal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SignalController extends Controller
{
    function index(Request $request)
    {
        $user = $request->user();

        $signals = Signal::where('streamer_id', $user->id)->get();

        $signals = SignalResource::collection($signals);

        return $this->sendResponse($signals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'category' => 'required|string|exists:categories,uuid',
                'type' => 'required|in:trade,news',
                'asset_type' => 'required_if:type,trade',
                'order_type' => 'required_if:type,trade',
                'entry_price' => 'required_if:type,trade|nullable|numeric',
                'stop_loss' => 'required_if:type,trade|nullable|numeric',
                'target_price' => 'required_if:type,trade|nullable|numeric',
                'percentage' => 'nullable|numeric',
                'comment' => 'nullable|string',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'description' => 'required_if:type,news',
                'document' => 'nullable|mimes:pdf,xlsx,xls'
            ]);

            // Handle validation errors
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $user = $request->user();

            $category = Category::whereUuid($request->category)->first();

            $chartUrl = null;
            $fileUrl = null;

            if ($request->hasFile('photo')) {
                $chartUrl = uploadFile($request->file('photo'), "signals", "do_spaces");
            }

            if ($request->hasFile('document')) {
                $fileUrl = uploadFile($request->file('document'), "documents", "do_spaces");
            }

            $signal = Signal::create([
                'streamer_id' => $user->id,
                'asset_type' => $request->asset_type,
                'order_type' => $request->order_type ?? 'buy',
                'entry_price' => $request->entry_price ?? 0,
                'stop_loss' => $request->stop_loss ?? 0,
                'target_price' => $request->target_price ?? 0,
                'category_id' => $category->id,
                'percentage' => $request->percentage ?? 0,
                'comment' => !empty($request->comment) ? $request->comment : $request->description,
                'chart_photo' =>  $chartUrl,
                'file_url' => $fileUrl
            ]);

            // broadcast events
            $signal = new SignalResource($signal);

            $fcmTokens = followersPushTokens($user->id);

            if (!empty($fcmTokens)) {

                $name = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

                $data = [
                    'push_tokens' =>  $fcmTokens,
                    'title' => "Signal Created.",
                    'message' => "{$name} created new signal.",
                    'data' => [
                        'signal' => json_encode($signal),
                    ]
                ];

                dispatch(new \App\Jobs\PushNotificationJob($data));
            }

            return $this->sendResponse($signal, "Signal created successfully.", "201");
        } catch (\Throwable $th) {
            sendToLog($th);

            return $this->sendError("Ops Somthing went wrong. try again later.");
        }
    }

    public function updateMarketStatus(Request $request)
    {
        try {

            $signal = Signal::where('uuid', $request->signal)->first();

            if (!$signal) {
                return response()->json(['success' => false, 'message' => 'Signal not found.']);
            }

            $signal->update(['market_status' => $request->market_status]);

            $signal = new AllSignalResource($signal);

            $user = $request->user();

            $fcmTokens =  followersPushTokens($user->id);

            if (!empty($fcmTokens)) {

                $name = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

                $data = [
                    'push_tokens' =>  $fcmTokens,
                    'title' => "Signal Status updated.",
                    'message' => "{$name} updated signal status.",
                    'data' => [
                        'signal' => json_encode($signal),
                    ]
                ];

                dispatch(new \App\Jobs\PushNotificationJob($data));
            }

            event(new SignalNotification($user->uuid, $signal, "updated"));

            return response()->json(['success' => true, 'message' => 'Market status updated successfully.']);
        } catch (\Exception $e) {
            logger($e);

            return response()->json(['success' => false, 'message' => 'Cant perform this request at the moment.']);
        }
    }

    public function updateStatus(Request $request)
    {

        try {
            $signal = Signal::where('uuid', $request->signal)->first();

            if (!$signal) {
                return response()->json(['success' => false, 'message' => 'Signal not found.']);
            }

            $signal->update(['status' => $request->status]);

            $signal = new SignalResource($signal);

            $user = $request->user();

            $fcmTokens =  followersPushTokens($user->id);

            if (!empty($fcmTokens)) {

                $name = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

                $data = [
                    'push_tokens' =>  $fcmTokens,
                    'title' => "Signal Status updated",
                    'message' => "{$name} updated signal status.",
                    'data' => [
                        'signal' => json_encode($signal),
                    ]
                ];

                dispatch(new \App\Jobs\PushNotificationJob($data));
            }

            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } catch (\Exception $e) {
            logger($e->getMessage());

            return response()->json(['success' => false, 'message' => 'Cant perform this request at the moment.']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|exists:categories,uuid',
            'type' => 'required|in:trade,news',
            'asset_type' => 'required_if:type,trade',
            'order_type' => 'required_if:type,trade',
            'entry_price' => 'required_if:type,trade|nullable|numeric',
            'stop_loss' => 'required_if:type,trade|nullable|numeric',
            'target_price' => 'required_if:type,trade|nullable|numeric',
            'percentage' => 'nullable|numeric',
            'comment' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'description' => 'required_if:type,news'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            $category = Category::whereUuid($request->category)->first();

            $signal = Signal::where('uuid', $id)->first();

            if (!$signal) {
                return response()->json(['success' => false, 'errors' => "Signal not found."]);
            }

            // $imageUrl = $signal->photo;
            $chartUrl = $signal->chart_photo;

            if ($request->hasFile('photo')) {
                $chartUrl = uploadFile($request->file('photo'), "signals", "do_spaces");
            }

            $signal->update([
                'asset_type' => $request->asset_type,
                'order_type' => $request->order_type ?? 'buy',
                'entry_price' => $request->entry_price ?? 0,
                'stop_loss' => $request->stop_loss ?? 0,
                'target_price' => $request->target_price ?? 0,
                'category_id' => $category->id,
                'percentage' => $request->percentage ?? 0,
                'comment' => !empty($request->comment) ? $request->comment : $request->description,
                'chart_photo' =>  $chartUrl,
                'is_updated' => true
            ]);

            // broadcast events
            $signal = new SignalResource($signal);

            $user = $request->user();

            $fcmTokens =  followersPushTokens($user->id);

            if (!empty($fcmTokens)) {

                $name = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

                $data = [
                    'push_tokens' =>  $fcmTokens,
                    'title' => "Signal Updated",
                    'message' => "{$name} updated signal.",
                    'data' => [
                        'signal' => json_encode($signal),
                    ]
                ];

                dispatch(new \App\Jobs\PushNotificationJob($data));
            }


            return $this->sendResponse([], "Singla updated successfully.");
        } catch (\Throwable $th) {
            sendToLog($th);

            Log::error('Error From updating signal', [$th]);
            return response()->json(['success' => false, 'message' => 'Ops Somthing went wrong. try again later.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $signal = Signal::where('uuid', $id)->first();

        if (!$signal) {
            return response()->json(['success' => false, 'message' => 'Signal not found.']);
        }

        $signal->delete();

        return response()->json(['success' => true, 'message' => ' Signal was successfully deleted.']);
    }
}
