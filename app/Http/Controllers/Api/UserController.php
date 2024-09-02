<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    function index(Request $request)
    {
        $user = User::whereUuid($request->user_id)->first();

        $user = new UserResource($user);

        return $this->sendResponse($user, "User Date");
    }
}
