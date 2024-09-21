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

            $now = Carbon::now();
            $startOfMonth = $now->copy()->startOfMonth();
            $endOfMonth = $now->copy()->endOfMonth();
            $endOfPreviousMonth = $now->copy()->subMonth()->endOfMonth();  // Last day of the previous month
            $currentDay = $now->day;

            $commissions = \App\Models\CommissionTransaction::where('is_converted', false);

            if ($currentDay == 14) {
                // Get commissions for 1st to 7th of the month
                $commissionStartDate = $startOfMonth->copy();
                $commissionEndDate = $startOfMonth->copy()->addDays(7);
                $settlementWeek = 'Week 1';

                // Fetch commissions from 1st to 7th
                $commissions = $commissions->where('level', 0)->whereHas('sale', function ($query) use ($commissionStartDate, $commissionEndDate) {
                    $query->where('is_refunded', false)
                        ->whereBetween('created_at', [$commissionStartDate, $commissionEndDate]);
                })->get()
                    ->groupBy('user_id');
            } elseif ($currentDay == 21) {
                // Get commissions for 8th to 14th of the month
                $commissionStartDate = $startOfMonth->copy()->addDays(7);
                $commissionEndDate = $startOfMonth->copy()->addDays(14);
                $closingDate = $startOfMonth->copy()->addDays(14);
                $paymentDate = $startOfMonth->copy()->addDays(21);
                $settlementWeek = 'Week 2';

                // Fetch commissions from 8th to 14th
                $commissions = $commissions->where('level', 0)->whereHas('sale', function ($query) use ($commissionStartDate, $commissionEndDate) {
                    $query->where('is_refunded', false)
                        ->whereBetween('created_at', [$commissionStartDate, $commissionEndDate]);
                })->get()->groupBy('user_id');
            } elseif ($currentDay == 28) {
                // Get commissions for 15th to 21st of the month
                $commissionStartDate = $startOfMonth->copy()->addDays(14);
                $commissionEndDate = $startOfMonth->copy()->addDays(21);
                $settlementWeek = 'Week 3';

                // Fetch commissions from 15th to 21st
                $commissions = $commissions->where('level', 0)->whereHas('sale', function ($query) use ($commissionStartDate, $commissionEndDate) {
                    $query->where('is_refunded', false)
                        ->whereBetween('created_at', [$commissionStartDate, $commissionEndDate]);
                })->get()->groupBy('user_id');
            } elseif (
                $currentDay == 7
            ) {
                // Get commissions for 22nd to the last day of the previous month
                $commissionStartDate = $endOfPreviousMonth->copy()->subDays($endOfPreviousMonth->day - 22);
                $commissionEndDate = $endOfPreviousMonth;
                $settlementWeek = 'End of Month';

                $commissions = $commissions->whereHas('sale', function ($query) use ($commissionStartDate, $commissionEndDate) {
                    $query->where('is_refunded', false)
                        ->whereBetween('created_at', [$commissionStartDate, $commissionEndDate]);
                })->get()->groupBy('user_id');
            } else {

                return;
            }

            if (!empty($commissions)) {
                foreach ($commissions as $userId => $userCommissions) {

                    $user = User::find($userId);

                    if ($user) {
                        $totalAmount = $userCommissions->sum('amount');

                        if ($totalAmount > 0) {
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
                        }
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
