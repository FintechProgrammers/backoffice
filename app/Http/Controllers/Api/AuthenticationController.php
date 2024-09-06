<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Jobs\UpdateUser;
use App\Models\User;
use App\Services\Authentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError("Validation error", $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            DB::beginTransaction();

            // check if user exist on the current database
            if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {

                $user = User::find(Auth::user()->id);

                $token = $user->createToken('auth-token');

                // $token->accessToken

                $responseData['user'] = new UserResource($user);
                $responseData['auth_token'] = $token->plainTextToken;

                DB::commit();

                dispatch(new UpdateUser($user));

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
