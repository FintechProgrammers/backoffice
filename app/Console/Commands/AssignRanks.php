<?php

namespace App\Console\Commands;

use App\Models\Rank;
use App\Models\RankHistory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AssignRanks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ranks:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign ranks to users based on their total sales for the month';

    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info('Starting to assign ranks...');

        // Get all users
        $users = User::where('is_ambassador', true)->get();

        // Get all users with active subscriptions
        // $users = User::whereHas('subscriptions', function ($query) {
        //     $query->where('end_date', '>', Carbon::now());
        // })->get();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        foreach ($users as $user) {

            // Check if the current month is different from the month of the last rank update
            if ($user->rank_updated_at && $user->rank_updated_at->month != $currentMonth) {
                // Clear the rank
                $user->rank_id = null;
                $user->rank_updated_at = null;
                $user->save();
            }

            // Get the user's total sales for the current month
            $totalSales = $user->sales()->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->sum('amount');

            // Get the highest rank that the user qualifies for
            $rank = Rank::where('creteria', '>=', $totalSales)
                ->orderBy('creteria', 'desc')
                ->first();

            if ($rank) {
                // Check if the user's rank has changed
                if ($user->rank_id != $rank->id) {
                    // Update user's rank
                    $user->rank_id = $rank->id;
                    $user->rank_updated_at = Carbon::now();
                    $user->save();

                    // Log the rank change
                    RankHistory::create([
                        'user_id' => $user->id,
                        'rank_id' => $rank->id,
                    ]);

                    $this->info("User {$user->id} has been assigned rank {$rank->name}.");
                }
            }
        }
    }
}
