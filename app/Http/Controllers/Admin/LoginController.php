<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Notifications\Admin2faNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    function index()
    {
        return view('admin.auth.login');
    }

    function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid credentials'], 401);
        }

        $admin = Admin::where('email', $request->email)->first();


        // Check if user already has 2fa enaled else notify user to setup 2fa.
        if (empty($admin->google2fa_secret)) {
            $admin->notify(new Admin2faNotification($admin));

            Auth::guard('admin')->logout();

            return response()->json(['success' => true, 'message' => 'Instructions for setting up 2FA have been emailed to you. Check your inbox, then follow the instructions to set up 2FA.']);
        }

        if ($request->session()->has('2fa_checked')) {
            $request->session()->forget('2fa_checked');
        }

        return response()->json(['success' => true, 'route' => route('admin.dashboard.index'), 'message' => 'Login successful']);
    }
}
