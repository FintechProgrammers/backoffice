<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class NexioService
{
    function createUser($data)
    {
        $payload = [
            "recipientRef" => $data['recipient_id'],
            "email" => $data['email'],
            "firstName" => $data['first_name'],
            "lastName" => $data['last_name'],
            "country" => $data['country_code'],
        ];

        return self::handle("/payout/v3/recipient", "POST", $payload);
    }

    function payouts($data)
    {
        $payload = [
            'payouts' => [[
                "amount" => $data['amount'],
                "currency" => "USD",
                "recipient" => [
                    "recipientRef" => $data['recipient_ref'],
                ],
                "description" => $data['narration'],
                "payoutRef" => $data['reference']
            ]]
        ];

        return self::handle("/payout/v3/pay", "POST", $payload);
    }

    function getPayout($payoutId)
    {

        return self::handle("/payout/v3/{$payoutId}", "GET");
    }

    function createWebhook($data)
    {
        $payload = [
            "payoutAccountId" => config('constants.nexio.payout_account_id'),
            "webhookUrls" => $data
        ];

        return self::handle("/webhook/v3/webhookUrls", "POST", $payload);
    }


    private function handle($uri = '/', $method = 'POST', $params = [], $token = null)
    {
        try {
            $client = new Client();

            $username = config('constants.nexio.username');
            $password = config('constants.nexio.password');

            $token = base64_encode("{$username}:{$password}");

            $headers = [
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . $token,
                'Content-Type' => 'application/json',
            ];

            // Create the request with or without params based on their presence
            if (!empty($params)) {
                $body = json_encode($params);
                $request = new Request($method, config('constants.nexio.base_url') . $uri, $headers, $body);
            } else {
                $request = new Request($method, config('constants.nexio.base_url') . $uri, $headers);
            }

            $res = $client->send($request);

            return [
                'success' => true,
                'data' => json_decode($res->getBody()->getContents(), true)
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log the exception message
            sendToLog($e->getMessage());

            // Get the response body from the exception
            if ($e->hasResponse()) {
                $responseBody = $e->getResponse()->getBody()->getContents();
                $responseArray = json_decode($responseBody, true);

                // Check if the JSON decoding was successful
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $responseArray;
                }
            }

            return [
                'success' => false,
                'message' => 'An error occurred while processing the request.',
            ];
        } catch (\Exception $e) {
            // Log other exceptions
            sendToLog($e->getMessage());

            return [
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ];
        }
    }
}
