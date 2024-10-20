<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\ProviderConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
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
                'min_amount' => $request->mininum_amount,
                'max_amount' => $request->maximum_amount,
                'charge' => $request->charge
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
                'is_crypto'     => $request->is_crypto == 'on' ? true : false,
                // 'is_default'    => $request->is_default == 'on' ? true : false,
                'can_payin'     => $request->can_payin == 'on' ? true : false,
                'can_payout'    => $request->can_payout == 'on' ? true : false,
                'min_amount' => $request->mininum_amount,
                'max_amount' => $request->maximum_amount,
                'charge' => $request->charge
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

    function default(Provider $provider)
    {
        Provider::where('is_crypto', $provider->is_crypto)->where('can_payin', $provider->can_payin)->update([
            'is_default' => false
        ]);

        $provider->update([
            'is_default' => true
        ]);

        return $this->sendResponse([], "Set as default successfully.");
    }

    function configForm(Provider $provider)
    {
        $data['provider'] = $provider;
        $data['config'] = ProviderConfig::where('provider_id', $provider->id)->first();

        return view('admin.provider._config-form', $data);
    }

    function configPost(Request $request, Provider $provider)
    {
        DB::beginTransaction();
        try {

            ProviderConfig::updateOrCreate(
                ['provider_id' => $provider->id], // Condition to find existing record
                [
                    'api_key' => $request->filled('api_key') ? Crypt::encryptString($request->input('api_key')) : null,
                    'secret' => $request->filled('secret') ? Crypt::encryptString($request->input('secret')) : null,
                    'password' => $request->filled('password') ? Crypt::encryptString($request->input('password')) : null,
                    'merchant_id' => $request->filled('merchant_id') ? Crypt::encryptString($request->input('merchant_id')) : null,
                    'account_id' => $request->filled('account_id') ? Crypt::encryptString($request->input('account_id')) : null,
                    'username' => $request->filled('username') ? Crypt::encryptString($request->input('username')) : null,
                    'webhook_secret' => $request->filled('webhook_secret') ? Crypt::encryptString($request->input('webhook_secret')) : null
                ]
            );

            DB::commit();

            return $this->sendResponse([], "{$provider->name} configured successfully");
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }
}
