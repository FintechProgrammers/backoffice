<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    function index()
    {
        $data['sales'] = Sale::where('parent_id', Auth::user()->id)->get();

        return view('user.sales.index', $data);
    }
}
