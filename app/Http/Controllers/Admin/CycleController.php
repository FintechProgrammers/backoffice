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
        $data['cycle'] = Cycle::latest()->get();
    }

    function runCycle()
    {
        $cycle = Cycle::where('is_active', true)->first();

        // If no active cycle exists, create a new cycle as cycle 1
        if (!$cycle) {
            $cycleNumber = 1;
            $startDate = now();
            $endDate = $startDate->copy()->addMonth(); // Add a month to start date to get end date

            $cycle = Cycle::create([
                'name' => "Cycle {$cycleNumber}",
                'cycle_number' => $cycleNumber,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'is_active' => true
            ]);
        } else {
            // Check if the active cycle end date has passed
            $currentDate = now();
            if ($currentDate->gt($cycle->end_date)) {
                // Deactivate the current cycle
                $cycle->is_active = false;
                $cycle->save();

                // Create a new cycle incrementing the cycle number by 1
                $cycleNumber = $cycle->cycle_number + 1;
                $startDate = now();
                $endDate = $startDate->copy()->addMonth(); // Add a month to start date to get end date

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
