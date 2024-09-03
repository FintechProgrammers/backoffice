<?php

namespace App\Jobs;

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Authentication;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MigrateUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;

    /**
     * Create a new job instance.
     *
     * @param \Illuminate\Support\Collection $users
     */
    public function __construct($users)
    {
        $this->users = $users;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $authentication = new Authentication();
        $processedCount = 0; // Initialize the processed count
        $idsNotFound = [];

        foreach ($this->users as $user) {
            $response = $authentication->getUser($user->username);
            $data = $response['data'];

            if (empty($data) || isset($data['user exist'])) {
                array_push($idsNotFound, $user->username);
                continue;
            }

            $userId = $user->id;
            $newUserId = $data['uid'];

            DB::transaction(function () use ($user, $userId, $newUserId) {
                // Update parent references
                User::where('parent_id', $userId)->update(['parent_id' => $newUserId]);

                // Update the current user record
                $user->update([
                    'id' => $newUserId,
                    'migrated' => true
                ]);

                // Batch update user_id references across multiple models
                $this->updateReferences($userId, $newUserId);
            });

            $processedCount++; // Increment the processed count
        }

        $idsNotFoundList = json_encode($idsNotFound);

        // Log the number of users processed
        Log::info("MigrateUserJob processed {$processedCount} users.");
        Log::info("username not found {$idsNotFoundList}");
    }

    /**
     * Update all references for the given user ID.
     *
     * @param int $oldUserId
     * @param int $newUserId
     */
    protected function updateReferences($oldUserId, $newUserId)
    {
        $updates = ['user_id' => $newUserId];
        $parentUpdates = ['parent_id' => $newUserId];

        \App\Models\UserInfo::where('user_id', $oldUserId)->update($updates);
        \App\Models\Wallet::where('user_id', $oldUserId)->update($updates);
        \App\Models\Sale::where('user_id', $oldUserId)->update($updates);
        \App\Models\Sale::where('parent_id', $oldUserId)->update($parentUpdates);
        \App\Models\CommissionTransaction::where('user_id', $oldUserId)->update($updates);
        \App\Models\CommissionTransaction::where('child_id', $oldUserId)->update(['child_id' => $newUserId]);
        \App\Models\UserActivities::where('user_id', $oldUserId)->update($updates);
        \App\Models\UserKyc::where('user_id', $oldUserId)->update($updates);
        \App\Models\Invoice::where('user_id', $oldUserId)->update($updates);
        \App\Models\StripeUser::where('user_id', $oldUserId)->update($updates);
        \App\Models\PaymentMethod::where('user_id', $oldUserId)->update($updates);
    }
}
