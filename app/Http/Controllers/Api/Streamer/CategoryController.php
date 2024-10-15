<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\StreamerCategoryResource;
use App\Models\Streamer;
use App\Models\StreamerCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    function index(Request $request)
    {
        $user = $request->user();

        $categories = StreamerCategory::where('streamer_id', $user->id)->get();

        $categories = StreamerCategoryResource::collection($categories);

        return $this->sendResponse($categories);
    }
}
