<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    function index()
    {
        return view('admin.products.index');
    }

    function filter()
    {
        $data['products'] = Product::paginate(50);

        return view('admin.products._table', $data);
    }

    function create()
    {
        return view('admin.products.create');
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            Product::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Product created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function edit(Product $product)
    {
        $data['product'] = $product;

        return view('admin.products.edit', $data);
    }

    function show(Product $product)
    {
        $data['product'] = $product;

        return view('admin.products.show', $data);
    }

    function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Product updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function publish(Product $product)
    {
        $product->update([
            'is_published' => true,
        ]);

        return response(['success' => true, 'message' => 'Publish successfully']);
    }

    function draft(Product $product)
    {
        $product->update([
            'is_published' => false,
        ]);

        return response(['success' => true, 'message' => 'Product set as draft successfully.']);
    }

    function destroy(Product $product)
    {
        $product->delete();

        return response()->json(['success' => false, 'message' => 'Product deleted successfully.']);
    }
}
