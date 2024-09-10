<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonateController extends Controller
{
    public function index(User $user)
    {

        // Impersonate the selected user
        Auth::login($user); // Authenticates the selected user

        return redirect()->route('dashboard');

        // return response()->json([
        //     'success' => true,
        //     'redirectUrl' => route('dashboard')
        // ]);
    }

    public function stopImpersonation()
    {
        if (session()->has('impersonate')) {
            Auth::loginUsingId(session('impersonate'));
            session()->forget('impersonate');

            return redirect()->route('admin.dashboard')->with('success', 'You have stopped impersonating the user.');
        }

        return redirect()->back()->with('error', 'You are not impersonating any user.');
    }
}