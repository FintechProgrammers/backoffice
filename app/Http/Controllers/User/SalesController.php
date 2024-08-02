<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    function index()
    {

        return view('user.sales.index');
    }

    function filter(Request $request)
    {
        $user = User::whereId(auth()->user()->id)->first();

        $data['sales'] = $user->getDescendantSales()->paginate(12);

        return view('user.sales._table', $data);
    }
}