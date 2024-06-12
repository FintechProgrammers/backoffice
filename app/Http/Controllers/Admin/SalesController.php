<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    function index()
    {
        return view('admin.sales.index');
    }

    function filter(Request $request)
    {
        $data['sales'] = Sale::latest()->paginate(50);

        return view('admin.sales._table', $data);
    }
}
