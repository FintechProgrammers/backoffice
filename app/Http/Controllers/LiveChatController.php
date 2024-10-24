<?php

namespace App\Http\Controllers;

use App\Http\Resources\LiveChatResource;
use App\Models\LiveChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LiveChatController extends Controller
{
    function messages($educator)
    {
        try {
            $educator = User::where('uuid', $educator)->firstOrFail();

            $messages = LiveChat::where('streamer_id', $educator->id)->get();

            $messages = LiveChatResource::collection($messages);

            return $this->sendResponse($messages);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }

    function sendMessage(Request $request, $educator)
    {
        try {

            $educatorDetails = User::where('uuid', $educator)->firstOrFail();

            if (!$educatorDetails) {
                return $this->sendError("Invalid educator");
            }

            $validator = Validator::make($request->all(), [
                'message' => 'required|string',
                'media' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            // Handle validation errors
            if ($validator->fails()) {
                return $this->sendError("Validations failed.", ['errors' => $validator->errors()], 422);
            }

            // if ($request->hasFile('media')) {
            //     // $imageUrl = $this->uploadFile($request->file('photo'), "strategy");
            //     $image = $request->file('media');
            //     $image_name = time() . '.' . $image->getClientOriginalExtension();
            //     $image->move(public_path('media'), $image_name);

            //     $message = url('media/' . $image_name);
            //     $type = "media";
            // } else {
            //     $message = $request->message;
            //     $type = "text";
            // }

            $user = $request->user();

            $chat = LiveChat::create([
                'streamer_id' => $educatorDetails->id,
                'sender_id' => $user->id,
                'message' => $request->message
            ]);

            $chat = new LiveChatResource($chat);

            // event(new ChatNotification($educatorDetails->uuid, $chat));

            return $this->sendResponse($chat, "Message sent successfully.", 201);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }
}
