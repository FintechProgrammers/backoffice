<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    function index()
    {
        return view('admin.settings.index');
    }

    function store(Request $request)
    {

        // setup payment methods
        $data = [
            'withdrawal_is_active'      => $request->withdrawal_is_active === "on" ? true : false,
            'minimum_withdrawal_amount' => $request->has('minimum_withdrawal_amount') ? $request->minimum_withdrawal_amount : 0,
            'maximum_withdrawal_amount' => $request->has('maximum_withdrawal_amount') ? $request->maximum_withdrawal_amount : 0,
            'withdrawal_fee'            => $request->has('withdrawal_fee') ? $request->withdrawal_fee : 0,
            'bv_equivalent'             => $request->has('bv_equivalent') ? $request->bv_equivalent : 0,
            'ambassador_fee'            => $request->has('ambassador_fee') ? $request->ambassador_fee : 0
        ];

        $settings = Settings::first();

        if ($settings) {
            $settings->update($data);
        } else {
            Settings::create($data);
        }

        return $this->sendResponse([], "Settings updated successfully.");
    }
}
