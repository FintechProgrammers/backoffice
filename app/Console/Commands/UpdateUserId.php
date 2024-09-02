<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\Authentication;
use Illuminate\Console\Command;

class UpdateUserId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:id';

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
        // get all users usernames
        $usersnames = User::all()->pluck('username')->toArray();

        $authentication = new Authentication();

        $response = $authentication->getUser("");

        $data = $response['data'];
    }
}