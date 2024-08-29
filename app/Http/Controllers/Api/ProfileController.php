<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends Controller
{
    function index(Request $request)
    {
        $user = $request->user();

        $user = new UserResource($user);

        return $this->sendResponse($user);
    }

    public function updateToken(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'token' => 'required|string',
            ]);


            if ($validator->fails()) {
                return $this->sendError("Validation error", $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $request->user()->update(['push_token' => $request->token]);

            return $this->sendResponse(null, "Token updated successfully", 201);
        } catch (\Exception $e) {
            logger($e);

            return $this->sendError("Service Unavailable", [], 500);
        }
    }
}
