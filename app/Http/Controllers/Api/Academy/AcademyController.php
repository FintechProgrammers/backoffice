<?php

namespace App\Http\Controllers\Api\Academy;

use App\Http\Controllers\Controller;
use App\Http\Resources\Academy\AcademyResource;
use App\Models\Academy;
use App\Models\ServiceStreamer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AcademyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Get all streamer_ids related to the user's subscriptions
        $educators = ServiceStreamer::whereIn('service_id', function ($query) use ($user) {
            $query->select('service_id')
                ->from('user_subscriptions')
                ->where('user_id', $user->id);
        })->distinct()->pluck('streamer_id');

        $data = Academy::whereIn('admin_id', $educators)->orderBy('id', 'desc')->get();

        $resource = AcademyResource::collection($data);

        return $this->sendResponse($resource, "Academy list retrieved successfully.", Response::HTTP_OK);
    }
}
