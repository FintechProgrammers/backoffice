<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Academy\AcademyResource;
use App\Models\Academy;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcademyController extends Controller
{
    public function all(Request $request)
    {
        $user = $request->user();

        $data = Academy::where('streamer_id', $user->id)->orderBy('id', 'desc')->get();

        $resource = AcademyResource::collection($data);

        return $this->sendResponse($resource);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:200', 'regex:/[^\s]+/'],
            'thumbnail' => ['required', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'description' => ['nullable', 'max:10000', 'required_if:description,!=,null|regex:/[^\s]+/'],
        ]);

        $thumbnail = uploadFile($request->file('thumbnail'), "academy/thumbnails", "do_spaces");

        $user = $request->user();

        $academy = Academy::create([
            'streamer_id' =>  $user->id,
            'name' => $request->name,
            'thumbnail' => $thumbnail,
            'description' => $request->description,
        ]);

        $academy = new AcademyResource($academy);

        return $this->sendResponse($academy, "New Academy created successfully", Response::HTTP_CREATED);
    }

    public function show(Academy $academy)
    {
        $resource = new AcademyResource($academy);

        return $this->sendResponse($resource, "", Response::HTTP_CREATED);
    }

    public function update(Request $request, Academy $academy)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:200'],
            'thumbnail' => ['nullable', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'description' => ['nullable', 'string', 'max:10000']
        ]);

        $thumbnail = $academy->thumbnail;

        if ($request->hasFile('thumbnail')) {
            $thumbnail = uploadFile($request->file('thumbnail'), "academy/thumbnails", "do_spaces");
        }

        $academy->update([
            'name' => $request->name ?? $academy->name,
            'description' => $request->description ?? $academy->description,
            'thumbnail' => $thumbnail,
        ]);

        return $this->sendResponse(null, "Academy updated successfully", Response::HTTP_CREATED);
    }

    public function delete(Academy $academy)
    {
        $academy->delete();

        return response()->json(['success' => true, 'message' => 'Academy deleted successfully.']);
    }
}
