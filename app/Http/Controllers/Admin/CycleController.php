<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cycle;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CycleController extends Controller
{

    function index()
    {
        $activeCycle = Cycle::where('is_active', true)->first();

        $inactiveCycles = Cycle::where('is_active', false)
            ->orderBy('created_at', 'desc') // Adjust 'created_at' if needed
            ->get();

        $cycles = collect([$activeCycle])->merge($inactiveCycles);

        $data['cycles'] = $cycles;

        return view('admin.circle.index', $data);
    }

    function runCycle()
    {
        $cycle = Cycle::where('is_active', true)->first();

        // If no active cycle exists, create a new cycle starting from the beginning of the current month
        if (!$cycle) {
            $cycleNumber = 1;
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth(); // End of the current month

            $cycle = Cycle::create([
                'name' => "Cycle {$cycleNumber}",
                'cycle_number' => $cycleNumber,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true
            ]);
        } else {
            // Check if the active cycle end date has passed or is today
            $currentDate = now();
            if ($currentDate->gte($cycle->end_date)) {
                // Deactivate the current cycle
                $cycle->is_active = false;
                $cycle->save();

                // Create a new cycle starting from the beginning of the next month
                $cycleNumber = $cycle->cycle_number + 1;
                $startDate = now()->startOfMonth()->addMonth();
                $endDate = $startDate->copy()->endOfMonth(); // End of the next month

                $cycle = Cycle::create([
                    'name' => "Cycle {$cycleNumber}",
                    'cycle_number' => $cycleNumber,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => true
                ]);
            }
        }

        // At this point, $cycle contains the current active cycle


    }
}
