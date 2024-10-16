<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Events\ChatNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    function getMessages(Request $request)
    {
        $user = $request->user();

        $messages = Chat::where('chat_group_id', $user->chatGroup->id)->latest()->get();

        $messages = ChatResource::collection($messages);

        return $this->sendResponse($messages);
    }

    function sendMessage(Request $request)
    {
        try {

            $user = $request->user();

            $validator = Validator::make($request->all(), [
                'media' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
                'message' => 'nullable|string|required_if:media,null',
            ]);

            if ($request->message == 'null' && !$request->hasFile('media')) {
                return response()->json(['success' => false, 'error' => "Cannot send null message."], 400);
            }

            // Handle validation errors
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            if ($request->hasFile('media')) {
                // $imageUrl = $this->uploadFile($request->file('photo'), "strategy");
                $message = uploadFile($request->file('media'), "media", "do_spaces");
                $type = "media";
            } else {
                $message = $request->message;
                $type = "text";
            }

            $chat = Chat::create([
                'chat_group_id' => $user->chatGroup->id,
                'sender_id' => $user->id,
                'message' => $message,
                'type' => $type
            ]);

            $chat = new ChatResource($chat);

            event(new ChatNotification($user->uuid, $chat));

            $fcmTokens =  followersPushTokens($user->id);

            if (!empty($fcmTokens)) {
                $name = ucfirst($user->first_name) . ' ' . ucfirst($user->last_name);

                $data = [
                    'push_tokens' =>  $fcmTokens,
                    'title' => "Chat Notification",
                    'message' => "You have new message in {$name}'s channel",
                    'data' => [
                        'message' => json_encode($chat)
                    ]
                ];

                dispatch(new \App\Jobs\PushNotificationJob($data));
            }

            return $this->sendResponse($chat);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("An error has occurred.", [], 500);
        }
    }
}
