<?php

use App\Models\Settings;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

if (!function_exists('generateReference')) { /* Check_for "generateReference" */
    function generateReference()
    {
        $reference = (string) Str::uuid();
        $reference = str_replace('-', '', $reference);

        return $reference;
    }
} /* End_check for "generateReference" */

if (!function_exists('isApiRequest')) { /* Check_for "isApiRequest" */
    function isApiRequest()
    {
        if (request()->wantsJson() || str_starts_with(request()->path(), 'api')) {
            return true;
        }

        return false;
    }
} /* End_check for "isApiRequest" */


if (!function_exists('durationUnit')) {
    function durationUnit()
    {
        return [
            'days',
            'weeks',
            'months',
            'years',
            'lifetime'
        ];
    }
}

if (!function_exists('convertDaysToUnit')) {
    function convertDaysToUnit(int $days, $unit)
    {
        $result = 0;

        switch ($unit) {
            case 'weeks':
                $result = floor($days / 7);
                break;
            case 'months':
                $result = round(abs(Carbon::now()->addDays($days)->diffInDays(Carbon::now()) / 30));
                break;
            case 'years':
                $futureDate = Carbon::now()->addDays($days);
                $result = round(abs(Carbon::now()->diffInYears($futureDate)));
                break;
            default:
                $result = $days;
        }

        return $result;
    }
}


if (!function_exists('serviceDownMessage')) {
    function serviceDownMessage()
    {
        return 'We apologize, but at this time we are unable to complete your request.';
    }
}

if (!function_exists('uploadFile')) { /* send to log" */
    function uploadFile($file, $folder, $driver = "")
    {
        // using config
        if (config('app.env') === 'local') {
            // The environment is local
            $file_name = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path("{$folder}"), $file_name);

            $fileUrl = url("{$folder}/" . $file_name);
        } else {
            if ($driver === "do_spaces") {
                $extension = $file->getClientOriginalExtension(); // Get the file extension (e.g., 'jpg', 'png', 'pdf')
                // Generate a unique filename using a timestamp and a random string
                $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

                $filePath = "{$folder}/" . $uniqueFileName;

                $path = Storage::disk('do_spaces')->put($filePath, $file, 'public');
                $fileUrl = Storage::disk('do_spaces')->url($path);
            } else {
                $file_name = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path("{$folder}"), $file_name);

                $fileUrl = url("{$folder}/" . $file_name);
            }
        }

        return $fileUrl;
    }
}

if (!function_exists('deleteFile')) {
    function deleteFile($fileUrl)
    {
        if (!empty($fileUrl)) {
            // Extract file path from URL
            $filePath = parse_url($fileUrl, PHP_URL_PATH);

            if (config('app.env') === 'local') {
                $path = public_path($filePath);
                if (file_exists($path)) {
                    unlink($path);
                }
            } else {
                if (Storage::disk('do_spaces')->exists($filePath)) {
                    Storage::disk('do_spaces')->delete($filePath);
                }
            }
        }
    }
}


if (!function_exists('validdateFile')) {
    function validdateFile($image, $ext, $allowedExtension)
    {
        if (($image->getSize() / 1000000) > 2) {
            throw ValidationException::withMessages(['attachments' => "Maximum 5 images can be uploaded"]);
        }
        if (!in_array($ext, $allowedExtension)) {
            throw ValidationException::withMessages(['attachments' => "Only png, jpg, jpeg, pdf images are allowed"]);
        }
    }
}


if (!function_exists('generateRandomPassword')) {
    function generateRandomPassword($length = 8)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        $characterCount = strlen($characters);

        for ($i = 0; $i < $length; ++$i) {
            $index = mt_rand(0, $characterCount - 1);
            $password .= $characters[$index];
        }

        return $password;
    }
}

if (!function_exists('isApiRequest')) { /* Check_for "isApiRequest" */
    function isApiRequest()
    {
        if (request()->wantsJson() || str_starts_with(request()->path(), 'api')) {
            return true;
        }

        return false;
    }
} /* End_check for "isApiRequest" */


/**
 * Send mail with the specified driver
 *
 * @param string  $driver
 * @param string  $email
 * @param array  $data
 *
 * @return bool  true|false
 */
if (!function_exists('sendMailByDriver')) { /* Check_for "sendMailByDriver" */
    function sendMailByDriver($driver, $email, $data)
    {
        // Mailgun drivers
        $mailgunDrivers = ['mailgun'];

        // Criteria to lookout for in the selected mail driver
        $criteria = in_array($driver, $mailgunDrivers) ? config('mail.mailers.' . $driver . '.domain') : config('mail.mailers.' . $driver . '.username');

        // Verify if the driver exist in the env and mail configuration file
        if (!is_null($criteria)) {
            // Try and send the mail via the selected dirver
            try {
                Mail::mailer($driver)->to($email)->send($data);

                return true;
            } catch (\Exception $e) {
                sendToLog($driver . ' Failure => ' . $e->getMessage());
                // Log the driver mail error
                logger($driver == 'smtp' ? 'Mailtrap' : 'Mailgun' . ' Failure => ', [
                    'message' => $e->getMessage(),
                ]);

                return false;
            }
        }

        logger(ucfirst($driver) . ' driver configuration is empty.');
        return false;
    }
}


if (!function_exists('sendToLog')) { /* send to log" */
    function sendToLog($error)
    {
        logger($error);
        // Log the exception if it's in a local environment
        // if (env('APP_ENV') === 'local') {
        //     logger($error);
        // } else {
        //     try {
        //         // $logFilesPath = storage_path('logs');
        //         // // // get all log files
        //         // $logFiles = File::glob($logFilesPath . '/*.log');
        //         // // // get latest log
        //         // $latestLogFile = array_pop($logFiles);

        //         // $logFileContent = File::get($latestLogFile);

        //         $payload = [
        //             'text' => $error
        //         ];

        //         $client = new \GuzzleHttp\Client();
        //         $client->post('https://hooks.slack.com/services/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', [
        //             'headers' => [
        //                 'Content-Type' => 'application/json',
        //             ],
        //             'json' => $payload,
        //         ]);
        //     } catch (\Throwable $th) {
        //         throw $th;
        //     }
        // }
    }
}


if (!function_exists('limitWords')) {
    function limitWords($string, $limit = 100)
    {
        $words = explode(' ', $string);
        if (count($words) > $limit) {
            return implode(' ', array_slice($words, 0, $limit)) . '...';
        }
        return $string;
    }
}

if (!function_exists('systemSettings')) {
    function systemSettings()
    {
        return Settings::first();
    }
}


if (!function_exists('myReferrer')) {
    function myReferrer($id)
    {
        $referrals = directReferrals($id);

        $total = count($referrals);


        if ($total < 2) {
            return $id;
        }

        $firstChildTotalReferrals = 0;
        $secondChildTotalReferrals = 0;
        $firstchildId = 0;

        foreach (directReferrals($id) as $key => $child) {

            if ($key === 0) {
                $childRef = directReferrals($child->id);
                $totalChild = count($childRef);

                if ($totalChild < 2) {
                    return ($child->id);
                }
                $firstChildTotalReferrals = userDownlines($child->id);
                $firstchildId = $child->id;
                continue;
            }

            if ($key === 1) {
                $childRef = directReferrals($child->id);
                $totalChild = count($childRef);

                if ($totalChild < 2) {
                    return $child->id;
                }

                $secondChildTotalReferrals = userDownlines($child->id);
            }

            //Let's determine whose grandchild inherit the referral since all child have referred two

            if ($firstChildTotalReferrals <= $secondChildTotalReferrals) {

                return myReferrer($firstchildId);
            } else {
                return  myReferrer($child->id);
            }
        }
    }
}

if (!function_exists('userDownlines')) {
    function userDownlines($id)
    {
        $referrals = directReferrals($id);

        $total = count($referrals);

        foreach ($referrals as $child) {

            if ($child->id === $id) {
                continue; // To avoiding irresponsive recursion, continue if first user refered himself
            }

            $total += userDownlines($child->id);
        }

        return $total;
    }
}

if (!function_exists('directReferrals')) {
    function directReferrals($id)
    {
        $data = \App\Models\User::where('parent_id', $id)->where('is_ambassador', true)->get();

        return $data;
    }
}

if (!function_exists('currentCycle')) {
    function currentCycle()
    {
        $cycle = \App\Models\Cycle::where('is_active', true)->first();

        return $cycle->id;
    }
}

if (!function_exists('debitWallet')) {
    function debitWallet($amount)
    {
        $wallet = \App\Models\Wallet::where('user_id', auth()->user()->id)->first();

        $wallet->update([
            'amount' => $wallet->amount - $amount,
        ]);
    }
}

if (!function_exists('refundWallet')) {
    function refundWallet($amount)
    {
        $wallet = \App\Models\Wallet::where('user_id', auth()->user()->id)->first();

        $wallet->update([
            'balance' => $wallet->balance + $amount,
        ]);
    }
}

if (!function_exists('getWeekStartAndEnd')) {
    function getWeekStartAndEnd()
    {
        $now = new DateTimeImmutable();
        $dayOfWeek = $now->format('w'); // 0 (Sunday) to 6 (Saturday)

        // Adjust to Monday as the beginning of the week
        $weekStart = $now->sub(new DateInterval('P' . ($dayOfWeek ? $dayOfWeek - 1 : 6) . 'D'));
        $weekStart->setTime(0, 0); // Set time to 00:00

        $weekEnd = $weekStart->add(new DateInterval('P6D'));
        $weekEnd->setTime(23, 59, 59); // Set time to 23:59:59

        return [
            'week_start' => $weekStart->format('Y-m-d H:i:s'),
            'week_end' => $weekEnd->format('Y-m-d H:i:s'),
        ];
    }
}


if (!function_exists('getDaysOfWeek')) {
    function getDaysOfWeek()
    {
        return array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
    }
}


if (!function_exists('errorResponse')) {
    /**
     * Throw an error response
     * @param string  $message
     */
    function errorResponse($message, $code = Response::HTTP_UNPROCESSABLE_ENTITY)
    {
        // Trim and transform the message to sentence case
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $message,
        ], $code));
    }
}

if (!function_exists('topGlobalSellers')) {
    // Get top sellers across all users in the system
    function topGlobalSellers($limit = 20)
    {
        // Get all users
        $users = User::all();

        // Calculate total sales for each user and exclude those with zero sales
        $topSellers = $users->map(function ($user) {
            $totalSales = $user->getTotalSales();
            if ($totalSales > 0) {
                $user->total_sales = $totalSales;
                return $user;
            }
            return null;
        })->filter() // Remove null values (users with zero sales)
            ->sortByDesc('total_sales')
            ->take($limit);

        return $topSellers;
    }
}
