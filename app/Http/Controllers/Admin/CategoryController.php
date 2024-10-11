<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    function index()
    {
        return view('admin.category.index');
    }

    function create()
    {
        return view('admin.category.create');
    }

    function  filter()
    {
        $data['categories'] = Category::get();

        return view('admin.category._table', $data);
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'type' => 'string|required'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $photo = null;

            if ($request->hasFile('photo')) {
                $photo = uploadFile($request->file('photo'), "category", "do_spaces");
            }

            $category = Category::create([
                'name' => $request->name,
                'photo' =>  $photo,
                'type' => $request->type
            ]);

            return response()->json(['success' => true, 'message' => 'Catgory created successfully'], 201);
        } catch (\Throwable $th) {
            sendToLog($th);

            return response()->json(['success' => false, 'message' => 'Ops Somthing went wrong. try again later.'], 500);
        }
    }

    function show(Category $category)
    {

        if (!$category) {
            return response()->json(['success' => false, 'errors' => 'Category not found.']);
        }

        return view('admin.category.show', ['category' => $category]);
    }

    function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'type' => 'string|required'
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if (!$category) {
            return response()->json(['success' => false, 'errors' => 'Category not found.']);
        }

        try {
            $photo = $category->photo;

            if ($request->hasFile('photo')) {
                $photo = uploadFile($request->file('photo'), "category", "do_spaces");
            }

            $category->update([
                'name' => $request->name,
                'photo' =>  $photo,
                'type' => $request->type
            ]);

            return $this->sendResponse([], "Catgory updated successfully");
        } catch (\Throwable $th) {
            sendToLog($th);

            return response()->json(['success' => false, 'message' => 'Ops Somthing went wrong. try again later.'], 500);
        }
    }

    function removeImage(Category $category)
    {

        if (!$category) {
            return response()->json(['success' => false, 'errors' => 'Category not found.']);
        }

        // Parse the URL to get the file path
        $filePath = parse_url($category->photo, PHP_URL_PATH);

        // Convert the encoded URL characters back to their original form
        $filePath = urldecode($filePath);

        if (file_exists(public_path($filePath))) {
            if (unlink(public_path($filePath))) {
                $category->update([
                    'photo' => null
                ]);
                // File successfully deleted
                return response()->json(['success' => true, 'message' => 'File successfully deleted'], 201);
            } else {
                // File deletion failed
                return response()->json(['success' => false, 'message' => 'Failed to delete the file.'], 404);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'File not found.'], 404);
        }
    }
}
