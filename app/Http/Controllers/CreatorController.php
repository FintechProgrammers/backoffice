<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CreatorController extends Controller
{
    function index()
    {
        return view('streamer.index');
    }
}
