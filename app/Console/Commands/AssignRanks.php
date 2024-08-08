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

        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $endOfMonth = $now->endOfMonth();

        // Get all users
        $users = User::where('is_ambassador', true)->get();

        // Get all users with active subscriptions
        // $users = User::whereHas('subscriptions', function ($query) {
        //     $query->where('end_date', '>', Carbon::now());
        // })->get();

        foreach ($users as $user) {
            $totalSales = $user->sales()->select('amount')->sum('amount');

            // Get the highest rank that the user qualifies for
            $rank = Rank::where('creteria', '<=', $totalSales)
                ->orderBy('creteria', 'desc')
                ->first();

            if ($rank) {
                // Check if the user's rank has changed
                if ($user->rank_id != $rank->id) {
                    // Update user's rank
                    $user->rank_id = $rank->id;
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
