<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Http\Controllers\Controller;
use App\Http\Resources\StreamerUserResource;
use App\Models\Streamer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Validation error", $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // check if user exist on the current database
            if (Auth::guard('streamer')->attempt($request->only('email', 'password'))) {

                $user = Streamer::find(Auth::guard('streamer')->user()->id);

                $token = $user->createToken('auth-token');

                $responseData['user'] = new StreamerUserResource($user);

                $responseData['auth_token'] = $token->plainTextToken;

                DB::commit();

                return $this->sendResponse($responseData, "Successful login.", Response::HTTP_OK);
            }

            return $this->sendError("Invalid login credentials", [], Response::HTTP_UNAUTHORIZED);
        } catch (\Throwable $e) {
            DB::rollBack();
            logger(["auth error" => $e]);
            return $this->sendError("Service Unavailable", [], Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }
}
