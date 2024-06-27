<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProviderController extends Controller
{
    function index()
    {
        return view('admin.provider.index');
    }

    function filter(Request $request)
    {
        $data['providers'] = Provider::get();

        return view('admin.provider._table', $data);
    }

    function create()
    {
        return view('admin.provider.create');
    }

    function edit(Provider $provider)
    {
        $data['provider'] = $provider;

        return view('admin.provider.edit', $data);
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'required|image',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imageUrl = uploadFile($request->file('image'), "uploads/packages", "do_spaces");
            }

            Provider::create([
                'name'          => $request->name,
                'short_name'    => $request->short_name,
                'image_url'     => $imageUrl,
                'is_crypto'     => $request->is_crypto == 'on' ? true : false,
                'is_default'    => $request->is_default == 'on' ? true : false,
                'can_payin'     => $request->can_payin == 'on' ? true : false,
                'can_payout'    => $request->can_payout == 'on' ? true : false,
                'is_active'     => $request->is_active == 'on' ? true : false,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Provider created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function update(Request $request, Provider $provider)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'image' => 'nullable|image',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $imageUrl = uploadFile($request->file('image'), "uploads/packages", "do_spaces");
            } else {
                $imageUrl = $provider->image_url;
            }

            $provider->update([
                'name'          => $request->name,
                'image_url'     => $imageUrl,
                'is_crypto'     => $request->is_crypt == 'on' ? true : false,
                // 'is_default'    => $request->is_default == 'on' ? true : false,
                'can_payin'     => $request->can_payin == 'on' ? true : false,
                'can_payout'    => $request->can_payout == 'on' ? true : false,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Provider updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function enable(Provider $provider)
    {
        $provider->update([
            'is_active' => 1,
        ]);

        return $this->sendResponse([], "Enabled successfully.");
    }

    function disable(Provider $provider)
    {
        $provider->update([
            'is_active' => 0,
        ]);

        return $this->sendResponse([], "Disabled successfully.");
    }
}
