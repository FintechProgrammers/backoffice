<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Google2faController extends Controller
{

    public function enable2fa(Request $request)
    {
        // Retrieve the user based on the provided $id (assuming $id is the user's UUID).
        $admin = Admin::where('uuid', $request->id)->firstOrFail();

        //Check if user already has 2FA enabled
        if (! empty($admin->google2fa_secret)) {
            return redirect()->route('admin.login')->with('error', 'You already have 2FA enabled.');
        }

        // Verify the hash to prevent tampering with the link.
        if (sha1($admin->email) !== $request->hash) {
            abort(403, 'Invalid link');
        }

        // Convert the provided expires timestamp to a Carbon instance for comparison.
        $expires = Carbon::createFromTimestamp($request->expires);

        // Check if the link has expired
        if (Carbon::now()->greaterThan($expires)) {
            abort(403, 'Link has expired');
        }

        $google2fa = app('pragmarx.google2fa');

        $data['secret'] = $google2fa->generateSecretKey(32);

        $data['QR_Image'] = $google2fa->getQRCodeInline(
            config('app.name'),
            $admin->email,
            $data['secret']
        );

        $data['admin'] = $admin;

        return view('admin.auth.security.enable-2fa', $data);
    }

    public function enable2faPost(Request $request, Admin $admin)
    {
        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->messages()->first());
        }

        $google2fa = app('pragmarx.google2fa');

        if (! $google2fa->verify($request->one_time_password, $request->google2fa_secret)) {
            return back()->with('error', 'Invalid One time password.');
        }

        // store the secret in the user profile
        $admin->update([
            'google2fa_secret' => $request->google2fa_secret,
        ]);

        return redirect()->route('admin.login')->with('success', '2fa Activated Successfully.');
    }

    public function verify2fa()
    {
        return view('admin.auth.security.verify-2fa');
    }

    public function verify2faPost(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'one_time_password' => 'required',
        ]);

        if ($validator->fails()) {
            return back()->with('error', $validator->messages()->first());
        }

        $admin = Auth::guard('admin')->user();

        $google2fa = app('pragmarx.google2fa');

        if (empty($admin->google2fa_secret)) {
            return redirect()->route('admin.security.enable-2fa')->with('error', 'Enable your 2fa.');
        }

        $valid = $google2fa->verifyKey($admin->google2fa_secret, $request->one_time_password);

        if (! $valid) {
            return redirect()->back()->with('error', 'Invalid One time password.');
        }

        session(['2fa_checked' => true]);

        return redirect()->route('admin.dashboard.index')->with('success', 'Login successfully.');
    }
}
