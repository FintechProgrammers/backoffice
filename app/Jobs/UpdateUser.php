<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\Authentication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new job instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->user;

        $authentication = new Authentication();

        $response = $authentication->getUser($user->username);
        $data = $response['data'];

        if (empty($data) || isset($data['user exist'])) {
            return;
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
            updateReferences($userId, $newUserId);
        });
    }
}
