<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Notifications\CommissionReleased;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class DispatchCommission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $groupedCommissions, public $settlementWeek)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::beginTransaction();

        try {
            foreach ($this->groupedCommissions as $userId => $userCommissions) {
                $user = User::find($userId);

                if ($user) {
                    $totalAmount = $userCommissions->sum('amount');
                    $wallet = $user->wallet;

                    $opening_balance = $wallet->balance;
                    $closing_balance = $wallet->balance + $totalAmount;

                    $wallet->update([
                        'balance' => $closing_balance
                    ]);

                    // Create a new transaction for the user
                    $transaction = Transaction::create([
                        'user_id' => $user->id,
                        'associated_user_id' => null, // optional if summarizing multiple child transactions
                        'internal_reference' => generateReference(),
                        'amount' => $totalAmount,
                        'opening_balance' => $opening_balance,
                        'closing_balance' => $closing_balance,
                        'action' => 'credit',
                        'type' => 'commission',
                        'status' => 'completed',
                        'narration' => "Total sales commission",
                        'settlement_week' => $this->settlementWeek
                    ]);

                    // Mark all user commissions as released
                    $userCommissions->each->update(['is_converted' => true, 'transaction_id' => $transaction->id]);

                    // Notify the user
                    $user->notify(new CommissionReleased($totalAmount));
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e);
        }
    }
}
