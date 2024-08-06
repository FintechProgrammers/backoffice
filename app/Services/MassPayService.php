<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class MassPayService
{
    function getBalance()
    {
        return self::handle("/v1.0.0/payout/account/balance", "GET");
    }

    function initiateTransaction(array $data)
    {
        $payload = [
            "source_currency_code" => "USD",
            "notify_user" => false,
            "auto_commit" => false,
            "client_transfer_id" => $data['client_transfer_id'],
            "destination_currency_code" => "USD",
            "source_token" => $data['source_token'],
            "destination_token" => $data['destination_token'],
            "source_amount" => $data['amount'],
            'destination_amount' => $data['amount'],
        ];

        $user_token = $data['user_token'];

        return self::handle("/v1.0.0/payout/{$user_token}", "POST", $payload);
    }

    function getUserTransaction(array $data)
    {
        $user_token = $data['user_token'];

        return self::handle("/v1.0.0/payout/{$user_token}?include_payer_logos=false", "GET");
    }

    function getTransactionStatus(array $data)
    {
        $user_token = $data['user_token'];
        $payout_token = $data['payout_token'];

        return self::handle("/v1.0.0/payout/{$user_token}/{$payout_token}?force_status_update=false", "GET");
    }

    function commitTransaction(array $data)
    {
        $user_token = $data['user_token'];
        $payout_token = $data['payout_token'];

        return self::handle("/v1.0.0/payout/{$user_token}/{$payout_token}", "PUT");
    }

    function createUser(array $data)
    {
        $payload = [
            "notify_user" => false,
            "internal_user_id" => $data['internal_user_id'],
            "country" => $data['country_code'],
            "first_name" => $data['first_name'],
            "last_name" => $data['last_name'],
            "email" => $data['email'],
        ];

        return self::handle("/v1.0.0/payout/user", "POST", $payload);
    }

    function getUserWallet($userToken)
    {
        return $this->handle("/v1.0.0/payout/wallet/{$userToken}", "GET", []);
    }

    private function handle($uri = '/', $method = 'POST', $params = [], $token = null)
    {
        try {
            $client = new Client();

            $apiKey = config('constants.masspay.api_key');

            $headers = [
                'Accept' => 'application/json',
                'Authorization' => "Bearer {$apiKey}",
            ];

            if (!empty($token)) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $request = new Request($method, config('constants.masspay.base_url') . $uri, $headers, json_encode($params));

            $res = $client->send($request);

            $data = [
                'success' => true,
                'data' => json_decode($res->getBody()->getContents(), true)
            ];

            return $data;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Log the exception message

            sendToLog($e);

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
            sendToLog($e);

            return [
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ];
        }
    }
}
