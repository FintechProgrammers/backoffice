<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    function success()
    {
        return view('payment-success');
    }

    function cancel()
    {
        return view('payment-cancel');
    }
}
