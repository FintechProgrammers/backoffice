<?php

namespace App\Console\Commands;

use App\Models\CommissionTransaction;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\CommissionReleased;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseCommissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:release-commissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        DB::beginTransaction();

        try {

            $now = Carbon::now();

            $cash_back_window = !empty(systemSettings()->cash_back_window) ? systemSettings()->cash_back_window : 7;

            // Get commissions that are due for release and conditions are met
            $commissions = CommissionTransaction::where('is_converted', false)
                ->whereHas('sale', function ($query) use ($now, $cash_back_window) {
                    $query->where('is_refunded', false)
                        ->where('created_at', '>=', $now->subDays($cash_back_window));
                })
                ->get();

            foreach ($commissions as $commission) {
                $user = User::find($commission->user_id);

                if ($user) {
                    $wallet = $user->wallet;

                    $opening_balance = $wallet->balance;
                    $closing_balance = $wallet->balance + $commission->amount;

                    $wallet->update([
                        'balance' => $closing_balance
                    ]);

                    // Mark commission as released
                    $commission->update([
                        'is_converted' => true
                    ]);

                    Transaction::create([
                        'user_id' => $user->id,
                        'associated_user_id' => $commission->child_id,
                        'internal_reference' => generateReference(),
                        'amount' => $commission->amount,
                        'opening_balance' => $opening_balance,
                        'closing_balance' => $closing_balance,
                        'action' => 'credit',
                        'type' => 'commission',
                        'status' => 'completed',
                        'narration' => "Sales commission"
                    ]);

                    $user->notify(new CommissionReleased($commission->amount));

                    $this->info("Released {$commission->amount} to user {$user->id}'s wallet.");
                } else {
                    $this->warn("User with ID {$commission->user_id} not found.");
                }
            }


            DB::commit();
            $this->info('All due commissions have been released successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
