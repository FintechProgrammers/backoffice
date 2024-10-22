<?php

namespace App\Http\Controllers;

use App\Http\Resources\ScheduleResources;
use App\Http\Resources\StreamerResource;
use App\Http\Resources\VideoResource;
use App\Models\LiveAttendants;
use App\Models\Schedule;
use App\Models\ServiceStreamer;
use App\Models\Streamer;
use App\Models\Video;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends Controller
{
    public function index()
    {
        $schdules = Schedule::orderBy('id', 'desc')->get();
        $resources = ScheduleResources::collection($schdules);

        return $this->sendResponse($resources, "Schedules retrieved successfully.", Response::HTTP_OK);
    }

    public function educatorSchedules($educator)
    {
        $educator = Streamer::where('uuid', $educator)->firstOrFail();

        $schdules = Schedule::where('streamer_id', $educator->id)->get();
        $resources = ScheduleResources::collection($schdules);

        return $this->sendResponse($resources, "", Response::HTTP_OK);
    }

    public function show(Request $request, $schedule)
    {
        $schedule = Schedule::where('uuid', $schedule)->firstOrFail();
        $videos = Video::where('schedule_id', $schedule->id);

        // if ($request->has('type')) {
        //     $videos = $videos->where('type', $request->type);
        // }

        $resources = [
            "schedule" => new ScheduleResources($schedule),
            "videos" => VideoResource::collection($videos->get())
        ];

        return $this->sendResponse($resources, "Schedules retrieved successfully.", Response::HTTP_OK);
    }

    function setViewers(Request $request, $id)
    {
        $user = $request->user();

        $schdule = Schedule::whereUuid($id)->first();

        if (!$schdule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found.'], 404);
        }

        $user = LiveAttendants::where('user_id', $user->id)->where('schedule_id', $schdule->id)->where('date', date("Y-m-d"))->first();

        if (!$user) {

            // $schdule->viewers = $schdule->viewers + 1;

            // $schdule->save();

            LiveAttendants::create([
                'user_id' => $user->id,
                'schedule_id' => $schdule->id,
                'date' => date("Y-m-d")
            ]);

            // event(new JoinedStream($schdule));
        }

        $count = LiveAttendants::where('schedule_id', $schdule->id)->where('date', date("Y-m-d"))->count();

        $schdule->update([
            'viewers' => $count
        ]);

        // $sch = $schdule->refresh();

        // if ($sch->viewers == 0) {
        //     $sch->update(['viewers' => 1]);
        // }

        $schdule = $schdule->refresh();

        return response()->json(['message' => 'updated successfully', 'viewers' => $schdule->viewers]);
    }

    function leaveStream(Request $request, $id)
    {
        try {
            $user = $request->user();

            $schedule = Schedule::whereUuid($id)->first();

            if (!$schedule) {
                return response()->json(['success' => false, 'message' => 'Schedule not found.'], 404);
            }

            $schedule->update([
                'viewers' =>  $schedule->viewers > 0 ? $schedule->viewers - 1 : 0
            ]);

            LiveAttendants::where([
                'user_id' => $user->id,
                'schedule_id' => $schedule->id,
                'date' => date("Y-m-d")
            ])->delete();

            // event(new JoinedStream($schedule));

            return response()->json(['message' => 'updated successfully', 'viewers' => $schedule->viewers]);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }

    function getViewers($schedule)
    {
        $schdule = Schedule::whereUuid($schedule)->firstOrFail();

        return $this->sendResponse($schdule->viewers);
    }

    function educatorsOnLive(Request $request)
    {
        try {

            $user = $request->user();

            // Get all streamer_ids related to the user's subscriptions
            $educators = ServiceStreamer::whereIn('service_id', function ($query) use ($user) {
                $query->select('service_id')
                    ->from('user_subscriptions')
                    ->where('user_id', $user->id);
            })->distinct()->pluck('streamer_id');

            $educators = streamer::whereIn('id', $educators)->where('is_live', true)->get();

            $educators = StreamerResource::collection($educators);

            return $this->sendResponse($educators);
        } catch (\Exception $e) {
            sendToLog($e);

            return $this->sendError("Unable to complete your request at the moment.", [], 500);
        }
    }
}
