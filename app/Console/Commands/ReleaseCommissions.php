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
    protected $description = 'Release commissions according to settlement rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $commissions = paymentsOfTheWeek();

            if (!empty($commissions)) {
                foreach ($commissions as $userId => $userCommissions) {

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
                            'settlement_week' => ""
                        ]);

                        // Mark all user commissions as released
                        $userCommissions->each->update(['is_converted' => true, 'transaction_id' => $transaction->id]);

                        $user->notify(new CommissionReleased($totalAmount));

                        $this->info("Released {$totalAmount} to user {$user->id}'s wallet.");
                    } else {
                        $this->warn("User with ID {$userId} not found.");
                    }
                }
            }

            DB::commit();
            $this->info('All due commissions have been released successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            sendToLog($e);
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }
}
