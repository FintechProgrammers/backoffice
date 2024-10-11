<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AssetController extends Controller
{
    function index()
    {
        return view('admin.assets.index');
    }

    function filter()
    {
        $data['assets'] = Asset::latest()->paginate(20);

        return view('admin.assets._table', $data);
    }

    function create()
    {
        $data['categories'] = Category::get();

        return view('admin.assets.create', $data);
    }

    function show(Asset $asset)
    {
        if (!$asset) {
            return response()->json(['success' => false, 'message' => 'Asset not found.']);
        }

        $data['categories'] = Category::get();
        $data['asset'] = $asset;

        return view('admin.assets.show', $data);
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'asset_name' => 'required|string',
                'category' => 'required|exists:categories,id',
                'symbol'   => 'required|string',
                'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            ]);

            // Handle validation errors
            if ($validator->fails()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            $photo = null;

            if ($request->hasFile('photo')) {
                // $imageUrl = $this->uploadFile($request->file('photo'), "strategy");
                $photo = uploadFile($request->file('photo'), "images/assets", "do_spaces");
            }

            $asset = Asset::create([
                'name' => $request->asset_name,
                'symbol' => $request->symbol,
                'category_id' => $request->category,
                'image' => $photo
            ]);

            return response()->json(['success' => true, 'message' => 'Asset created successfully.'], 201);
        } catch (\Throwable $th) {
            sendToLog($th);

            return response()->json(['success' => false, 'message' => 'Ops Somthing went wrong. try again later.'], 500);
        }
    }

    public function update(Request $request, Asset $asset)
    {
        $validator = Validator::make($request->all(), [
            'asset_name' => 'required|string',
            'category' => 'required|exists:categories,id',
            'symbol'   => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            if (!$asset) {
                return response()->json(['success' => false, 'message' => 'Asset not found.']);
            }

            $photo = $asset->image;

            if ($request->hasFile('photo')) {
                $photo = uploadFile($request->file('photo'), "images/assets", "do_spaces");
            }

            $asset->update([
                'name' => $request->asset_name,
                'symbol' => $request->symbol,
                'category_id' => $request->category,
                'image' => $photo
            ]);

            return response()->json(['success' => true, 'message' => 'Asset updated successfully.'], 201);
        } catch (\Throwable $th) {
            sendToLog($th);

            return response()->json(['success' => false, 'message' => 'Ops Somthing went wrong. try again later.'], 500);
        }
    }
}
