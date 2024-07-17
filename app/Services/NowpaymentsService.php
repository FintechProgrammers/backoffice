<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class NowpaymentsService
{

    function getMinimumAmount(array $data)
    {

        $currency_to = $data['currency_to'];
        $currency_from = $data['currency_from'];

        return self::handle("/v1/min-amount?currency_from={$currency_from}&currency_to={$currency_to}&fiat_equivalent=usd&is_fixed_rate=False&is_fee_paid_by_user=False", "GET");
    }

    function createInvoice(array $data)
    {
        $payload = [
            "price_amount" => $data['amount'],
            "price_currency" => "usd",
            "order_id" => $data['order_id'],
            "order_description" => $data['description'],
            "ipn_callback_url" => $data['ipn_callback_url'],
            "success_url" => route('payment.success'),
            "cancel_url" => route('payment.cancel')
        ];

        return  self::handle('/v1/invoice', "POST", $payload);
    }

    function createPayment(array $data)
    {
        $payload = [
            "price_amount" => $data['amount'],
            "price_currency" => "usd",
            "pay_currency" => "btc",
            "ipn_callback_url" => $data['ipn_callback_url'],
            "order_id" => $data['order_id'],
            "order_description" =>  $data['description']
        ];

        return  self::handle('/v1/payment', "POST", $payload);
    }

    function createPaymentByInvoice(array $data)
    {
        $payload = [
            "iid" => $data['invoice_id'],
            "pay_currency" => "btc",
            "purchase_id" => $data['purchase_id'],
            "order_description" => $data['order_description'],
            "customer_email" => $data['customer_email'],
            "payout_address" => $data['payout_address'],
            "payout_extra_id" => null,
            "payout_currency" =>  "usdttrc20"
        ];

        return self::handle('/v1/invoice-payment', "POST", $payload);
    }

    function balance()
    {
        return  self::handle('/v1/balance', "GET");
    }

    function validateAddress(array $data)
    {
        $payload = [
            "address" => $data['address'],
            "currency" => $data['currency'],
            "extra_id" => null
        ];

        return self::handle('/v1/payout/validate-address', "POST", $payload);
    }

    function payout(array $data)
    {

        $payload = [
            "payout_description" => "Payout request of  {$data['amount']} USD",
            "ipn_callback_url" => $data['ipn_callback_url'],
            "withdrawals"      =>  [
                [
                    'address'           => $data['address'],
                    'amount'            => $data['amount'],
                    'currency'          => $data['currency'],
                    "ipn_callback_url" => $data['ipn_callback_url']
                ],
            ]
        ];


        $token = self::authenticate();

        return self::handle('/v1/payout', "POST", $payload, $token);
    }

    function verifyPayout(array $data)
    {
        $payload = [
            "verification_code" => $data["verification_code"]
        ];

        $token = self::authenticate();

        return  self::handle('/v1/payout/5000000191/verify', "POST", $payload, $token);
    }

    function availablecurrencies()
    {
        return self::handle('/v1/full-currencies', "GET");
    }

    function merchanteCoin()
    {
        return self::handle('/v1/merchant/coins', "GET");
    }

    function authenticate()
    {
        try {
            $client = new Client();

            $params = [
                'email' => config('contstants.nowpayment.email'),
                'password' => config('contstants.nowpayment.password')
            ];

            $headers = [
                'Content-Type' => 'application/json',
            ];

            $request = new Request("POST", config('contstants.nowpayment.base_url') . '/v1/auth', $headers, json_encode($params));

            $res = $client->send($request);

            $res = json_decode($res->getBody()->getContents(), true);

            if (!empty($res)) {
                return $res['token'];
            } else {
                return '';
            }
        } catch (\Exception $e) {
            sendToLog($e);

            return $e->getMessage();
        }
    }

    private function handle($uri = '/', $method = 'POST', $params = [], $token = null)
    {
        try {
            $client = new Client();

            $headers = [
                'Content-Type' => 'application/json',
                'x-api-key' => config('contstants.nowpayment.api_key'),
            ];

            if (!empty($token)) {
                $headers['Authorization'] = "Bearer {$token}";
            }

            $request = new Request($method, config('contstants.nowpayment.base_url') . $uri, $headers, json_encode($params));

            $res = $client->send($request);

            return json_decode($res->getBody()->getContents(), true);
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
