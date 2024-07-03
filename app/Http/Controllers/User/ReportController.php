<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BonusHistory;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    function bonuses()
    {
        $data['bonuses'] = BonusHistory::where('user_id',auth()->user()->id)->latest()->get();

        return view('user.report.bonuses',$data);
    }

    function packages()
    {
        $data['packages'] = Sale::where('user_id', auth()->user()->id)->get();

        return view('user.report.packages',$data);
    }

    function ranks()
    {
        return view('user.report.ranks');
    }
}
