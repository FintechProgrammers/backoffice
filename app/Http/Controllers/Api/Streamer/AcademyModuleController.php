<?php

namespace App\Http\Controllers\Api\Streamer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Academy\CategorizedModule;
use App\Http\Resources\Academy\ModulesResource;
use App\Http\Resources\Academy\ModuleWithVideosResource;
use App\Models\Academy;
use App\Models\AcademyModule;
use App\Models\AcademyVideo;
use Illuminate\Http\Request;

class AcademyModuleController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $data = AcademyModule::where('streamer_id', $user->id)->orderBy('id', 'desc')->get();

        $resource = ModulesResource::collection($data);

        return response()->json(['success' => true, 'data' => $resource]);
    }

    public function categoryModule(Request $request, $category)
    {
        $user = $request->user();

        $data = Academy::where('uuid', $category)
            ->with(['academyModules' => function ($query) use ($user) {
                $query->where('streamer_id', $user->id);
            }])->whereHas('academyModules', function ($query) use ($user) {
                $query->where('steamer_id', $user->id);
            })->get();

        $resource = CategorizedModule::collection($data);

        return response()->json(['success' => true, 'data' => $resource]);
    }

    public function details(AcademyModule $module)
    {
        return view('admin.academy.module-details', ['module' => $module]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:200', 'regex:/[^\s]+/'],
            'academy_uuid' => ['required', 'exists:academies,uuid'],
            'description' => ['nullable', 'max:10000', 'required_if:description,!=,null|regex:/[^\s]+/'],
        ]);

        $academy = Academy::where('uuid', $request->academy_uuid)->first();

        $user = $request->user();

        $module = AcademyModule::create([
            'steamer_id' =>  $user->id,
            'name' => $request->name,
            'academy_id' => $academy->id,
            'description' => $request->description,
        ]);

        $module = new ModulesResource($module);

        return response()->json(['success' => true, 'message' => 'New Module created successfully.', 'module' => $module]);
    }

    public function show(AcademyModule $module)
    {
        $resource = new ModuleWithVideosResource($module);

        return response()->json(['success' => true, 'data' => $resource]);
    }

    function sortVideos(Request $request)
    {
        $module = AcademyModule::whereUuid($request->module)->first();

        $videos = AcademyVideo::where('academy_module_id', $module->id)->get();

        foreach ($videos as $video) {
            $video->timestamps = false;
            $id = $video->uuid;

            foreach ($request->videos as $frontVideo) {
                if ($frontVideo['id'] == $id) {
                    $video->update(['order' => $frontVideo['order']]);
                }
            }
        }

        return response('Update Successful.', 200);
    }

    public function update(Request $request, AcademyModule $module)
    {
        $request->validate([
            'name' => ['nullable', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:10000'],
        ]);

        $module->update([
            'name' => $request->name ?? $module->name,
            'description' => $request->description ?? $module->description,
        ]);

        return response()->json(['success' => true, 'message' => 'Module updated successfully.']);
    }

    public function delete(AcademyModule $module)
    {
        $module->delete();

        return response()->json(['success' => true, 'message' => 'Module deleted successfully.']);
    }
}