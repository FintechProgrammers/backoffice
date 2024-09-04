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
        DB::beginTransaction();

        try {
            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();

            // Determine the closing and payment dates for the settlement period
            if ($now->day == 14) {
                $closingDate = $startOfMonth->copy()->addDays(7);
                $paymentDate = $startOfMonth->copy()->addDays(14);
                $settlementWeek = 'Week 2';
            } elseif ($now->day == 21) {
                $closingDate = $startOfMonth->copy()->addDays(14);
                $paymentDate = $startOfMonth->copy()->addDays(21);
                $settlementWeek = 'Week 3';
            } elseif ($now->day == 28) {
                $closingDate = $startOfMonth->copy()->addDays(21);
                $paymentDate = $startOfMonth->copy()->addDays(28);
                $settlementWeek = 'Week 4';
            } elseif ($now->day == 7) {
                if ($now->month != $endOfMonth->month) {
                    $closingDate = $endOfMonth;
                    $paymentDate = $startOfMonth->addMonth()->startOfMonth()->addDays(6);
                    $settlementWeek = 'End of Month';
                } else {
                    $closingDate = $startOfMonth->copy()->addDays(7);
                    $paymentDate = $startOfMonth->copy()->addDays(14);
                    $settlementWeek = 'Week 1';
                }
            } else {
                $this->info('No commissions to release today.');
                return;
            }

            // Get commissions that are due for release
            $commissions = CommissionTransaction::where('is_converted', false)
                ->whereHas('sale', function ($query) use ($paymentDate) {
                    $query->where('is_refunded', false)
                        ->where('created_at', '<=', $paymentDate);
                })
                ->get()
                ->groupBy('user_id');

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
                        'settlement_week' => $settlementWeek
                    ]);

                    // Mark all user commissions as released
                    $userCommissions->each->update(['is_converted' => true, 'transaction_id' => $transaction->id]);

                    $user->notify(new CommissionReleased($totalAmount));

                    $this->info("Released {$totalAmount} to user {$user->id}'s wallet.");
                } else {
                    $this->warn("User with ID {$userId} not found.");
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
