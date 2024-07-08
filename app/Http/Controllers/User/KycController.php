<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserKyc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KycController extends Controller
{
    function index()
    {
        return view('user.kyc.index');
    }

    function loadContent()
    {
        $data['kyc'] = UserKyc::where('user_id', auth()->user()->id)->latest()->first();

        return view('user.kyc.content', $data);
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|max:2048',
            'back_page' => 'nullable|image|max:2048',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $user = $request->user();

            $backImage = null;

            $frontImage = uploadFile($request->file('photo'), "uploads/kyc", "do_spaces");

            if ($request->hasFile('back_page')) {
                $backImage = uploadFile($request->file('back_page'), "uploads/kyc", "do_spaces");
            }

            UserKyc::create([
                'user_id'       => $user->id,
                'front_link'    => $frontImage,
                'back_link'     => $backImage,
                'service'       => 'Documentation',
                'status'        => 'pending'
            ]);

            return response()->json(['success' => false, 'message' => 'Your document has been successfully upload for review.']);
        } catch (\Exception $e) {
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage(), 500]);
        }
    }
}
