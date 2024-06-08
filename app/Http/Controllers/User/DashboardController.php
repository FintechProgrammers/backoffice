<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    function index()
    {
        $data['services'] = Service::where('is_published', true)->get();

        return view('user.dashboard.index',$data);
    }
}
