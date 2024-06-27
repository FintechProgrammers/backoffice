<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    function index()
    {
        $user = auth()->user();

        $data['user'] = $user;

        return view('user.team.index', $data);
    }

}
