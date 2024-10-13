<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use App\Notifications\Admin2faNotification;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Verify2fa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $admin = Admin::find(Auth::guard('admin')->user()->id);

        // Check if user already has 2fa enaled else notify user to setup 2fa.
        if (empty($admin->google2fa_secret)) {
            $admin->notify(new Admin2faNotification($admin));

            Auth::guard('admin')->logout();

            return back()->with('warning', 'Instructions for setting up 2FA have been emailed to you. Check your inbox, then follow the instructions to set up 2FA.');
        }

        // 2FA is already checked
        if (! session('2fa_checked')) {
            // Authentication successful for admin guard
            return redirect()->route('admin.security.verify2fa');
        }

        return $next($request);
    }
}
