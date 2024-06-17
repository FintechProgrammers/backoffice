<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    function bonuses()
    {
        return view('user.report.bonuses');
    }

    function packages()
    {
        return view('user.report.packages');
    }

    function ranks()
    {
        return view('user.report.ranks');
    }
}
