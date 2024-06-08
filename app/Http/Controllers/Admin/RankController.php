<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RankController extends Controller
{
    function index()
    {
        return view('admin.rank.index');
    }

    function filter(Request $request)
    {
        $data['ranks']  = Rank::paginate(50);

        return view('admin.rank._table', $data);
    }

    function create()
    {
        return view('admin.rank.create');
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'benchmark' => 'required|numeric',
            'image' => 'required|image|mimes:png,jpg,svg|max:4096'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $image = uploadFile($request->file('image'), "uploads/ranks", "do_spaces");

            Rank::create([
                'name' => $request->name,
                'creteria' => $request->benchmark,
                'file_url'  => $image
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Rank Created Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage(), 500]);
        }
    }

    function show(Rank $rank)
    {
        $data['rank'] = $rank;

        return view('admin.rank.show', $data);
    }

    function edit(Rank $rank)
    {
        $data['rank'] = $rank;

        return view('admin.rank.edit', $data);
    }

    function update(Request $request, Rank $rank)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'benchmark' => 'required|numeric',
            'image' => 'required|image|mimes:png,jpg,svg|max:4096'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $image = $rank->file_url;

            if ($request->hasFile('image')) {
                // Check if there's an existing file and delete it
                if ($rank->file_url) {
                    deleteFile($rank->file_url);
                }

                $image = uploadFile($request->file('image'), "uploads/ranks", "do_spaces");
            }

            $rank->update([
                'name' => $request->name,
                'creteria' => $request->benchmark,
                'file_url'  => $image
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Rank Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage(), 500]);
        }
    }

    function destroy(Rank $rank)
    {
        $rank->delete();

        return response()->json(['success' => true, 'message' => 'Subject deleted successfully.']);
    }
}
