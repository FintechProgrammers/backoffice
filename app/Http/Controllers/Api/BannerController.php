<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    function index()
    {
        $banners = Banner::where('is_active', true)->get();

        $banners = BannerResource::collection($banners);

        return $this->sendResponse($banners);
    }
}
