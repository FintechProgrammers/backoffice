<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    function index()
    {

        return view('user.team.index');
    }

    function filter(Request $request)
    {
        $user = auth()->user();

        $data['customers'] = User::where('parent_id', $user->id)->paginate(9);

        return view('user.team._content', $data);
    }

    function genealogy()
    {
        $user = auth()->user();

        $data['user'] = $user;

        return view('user.team.genealogy', $data);
    }
}
