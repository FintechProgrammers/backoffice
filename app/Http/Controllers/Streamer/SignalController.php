<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    function index()
    {
        return view('streamer.signals.index');
    }
}
