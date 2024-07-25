<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLevels;
use App\Models\LevelRequirement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommisionPlanController extends Controller
{
    function index()
    {
        return view('admin.commission.index');
    }

    function filter(Request $request)
    {
        $data['commissions'] = CommissionLevels::get();

        return view('admin.commission._table', $data);
    }

    function create()
    {
        return view('admin.commission.create');
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'commission_percentage' => 'required|integer',
            'commission_type' => 'required',
            'level'   => 'nullable|numeric|required_if:commission_type,indirect',
            'direct_bv'  => 'nullable|numeric|required_if:has_requirement,on',
            'sponsored_bv' => 'nullable|numeric|required_if:has_requirement,on',
            'sponsored_count' => 'nullable|numeric|required_if:has_requirement,on',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $commission = CommissionLevels::create([
                'name' => $request->name,
                'level'  => $request->commission_type == 'direct' ? 0 : $request->level,
                'commission_percentage' => $request->commission_percentage,
                'is_direct'  => $request->commission_type == 'direct' ? true : false,
            ]);

            if ($request->has_requirement === 'on') {
                LevelRequirement::create([
                    'commission_level_id' => $commission->id,
                    'direct_bv'   => $request->direct_bv,
                    'sponsored_bv' => $request->sponsored_bv,
                    'sponsored_count' => $request->sponsored_count,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Commission Plan Created Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function show(CommissionLevels  $commission)
    {
        $data['commission'] = $commission;
        $data['requirement'] = LevelRequirement::where('commission_level_id', $commission->id)->first();

        return view('admin.commission.edit', $data);
    }

    function edit(CommissionLevels  $commission)
    {
        $data['commission'] = $commission;
        $data['requirement'] = LevelRequirement::where('commission_level_id', $commission->id)->first();

        return view('admin.commission.edit', $data);
    }


    function update(Request $request, CommissionLevels  $commission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'commission_percentage' => 'required|integer',
            'commission_type' => 'required',
            'level'   => 'nullable|numeric|required_if:commission_type,indirect',
            'direct_bv'  => 'nullable|numeric|required_if:has_requirement,on',
            'sponsored_bv' => 'nullable|numeric|required_if:has_requirement,on',
            'sponsored_count' => 'nullable|numeric|required_if:has_requirement,on',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $commission->update([
                'name' => $request->name,
                'level'  => $request->level,
                'commission_percentage' => $request->commission_percentage,
                'is_direct'  => $request->commission_type == 'direct' ? true : false,
            ]);

            if ($request->has_requirement === 'on') {
                LevelRequirement::where('commission_level_id', $commission->id)->update([
                    'direct_bv'   => $request->direct_bv,
                    'sponsored_bv' => $request->sponsored_bv,
                    'sponsored_count' => $request->sponsored_count,
                ]);
            }

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Commision Plan Updated Successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function destroy(CommissionLevels  $commission)
    {
        $commission->delete();

        return response()->json(['success' => true, 'message' => 'Commision Plan Deleted Successfully']);
    }
}
