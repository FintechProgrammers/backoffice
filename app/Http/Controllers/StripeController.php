<?php

namespace App\Http\Controllers;

use App\Jobs\StripStorePaymentMethod;
use App\Mail\SubscriptionMail;
use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\StripeUser;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserInfo;
use App\Models\UserSubscription;
use App\Notifications\NewAccountCreated;
use App\Services\StripeService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;

class StripeController extends Controller
{
    protected $stripeService;

    function __construct()
    {
        $this->stripeService = new \App\Services\StripeService();
    }

    function payment($validated)
    {
        try {

            $service = $validated['package'];

            $user = $validated['user'];

            $invoice = $validated['invoice'];

            $provider = $validated['provider'];

            $data = [
                'amount'        => $service->price * 100,
                'product_data'  => [
                    'name'      => $service->name,
                    'images'     => [$service->product_image_url],
                    // 'description' => $service->description,
                ],
                'customer_email' => $validated['email'],
                // 'customer_name' => $user->name,
                // 'customer_id'    => $stripUser->customer_id,
                'metadata'       => ['email' => $validated['email'], 'service_id' => $service->uuid, 'invoice' => $invoice->uuid],
                'success_url'    => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'     => route('payment.cancel')
            ];

            $response = $this->stripeService->processCheckout($data);

            if (empty($response)) {
                sendToLog($response);

                return throw new HttpResponseException(response()->json([
                    'success' => false,
                    'message' => serviceDownMessage(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY));
            }

            return $response->url;

            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        } catch (\Exception $e) {
            sendToLog($e);

            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    function createPaymentMethod($request)
    {
        try {
            $user = $request->user();

            // check if user already have an account with stripe
            // check if user exist on stripe
            $stripUser = StripeUser::where('user_id', $user->id)->first();

            if (!$stripUser) {
                $userData = [
                    'email'  => $user->email,
                    'name'  => $user->full_name,
                ];

                $createUser = $this->stripeService->createCustomer($userData);

                if (empty($createUser)) {
                    sendToLog($createUser);

                    return throw new HttpResponseException(response()->json([
                        'success' => false,
                        'message' => serviceDownMessage(),
                    ], Response::HTTP_UNPROCESSABLE_ENTITY));
                }

                $stripUser = StripeUser::create([
                    'user_id'       => $user->id,
                    'customer_id'   => $createUser->id
                ]);
            }

            $payload = [
                'customer_id' => $stripUser->customer_id,
                'success_url' => route('payment-method.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('profile.edit'),
            ];

            $response = $this->stripeService->createSession($payload);

            return $this->sendResponse(['route' => $response->url]);
        } catch (\Exception $e) {
            sendToLog($e);

            return throw new HttpResponseException(response()->json([
                'success' => false,
                'message' => serviceDownMessage(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY));
        }
    }

    function webhook(Request $request, StripeService $stripeService)
    {

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $data = [
                'payload' => $payload,
                'sig_header' => $sig_header
            ];

            $event = $stripeService->getWebhookEvent($data);
        } catch (\UnexpectedValueException $e) {
            logger($e);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            logger($e);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->sessionCompleted($event->data->object);
                break;
            case 'charge.succeeded':
                $this->chargeCompleted($event->data->object);
                break;
            case 'charge.updated':
                $this->paymentIntentCreated($event->data->object);
                break;
            case 'payment_intent.succeeded':
                $this->paymentIntentCompleted($event->data->object);
                break;
            case 'charge.refund.updated':
                break;
            case 'refund.created':
                $refund = $event->data->object;
                break;
            case 'reporting.report_run.failed':
                $reportRun = $event->data->object;
            case 'reporting.report_run.succeeded':
                $reportRun = $event->data->object;
            case 'setup_intent.canceled':
                $setupIntent = $event->data->object;
            case 'setup_intent.created':
                $setupIntent = $event->data->object;
            case 'setup_intent.requires_action':
                $setupIntent = $event->data->object;
            case 'setup_intent.setup_failed':
                $setupIntent = $event->data->object;
            case 'setup_intent.succeeded':
                $this->setupIntendSucceeded($event->data->object);
                break;
        }

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }

    function chargeCompleted($data) {}

    function setupIntendSucceeded($data)
    {
        try {
            $this->savePaymentMethod($data);
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }

    function sessionCompleted($data)
    {
        try {
            // Retrieve the Checkout Session from the API with line_items expanded
            $session = $this->stripeService->retrieveSession($data->id);

            if ($session->payment_status === 'paid') {
                $this->processInternalInvoice($session->metadata, $session->id);
                // get the invoice
            }
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }

    function paymentIntentCreated($data)
    {
        try {
            $this->savePaymentMethod($data);
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }

    function paymentIntentCompleted($data)
    {
        try {
            $this->processInternalInvoice($data->metadata, $data->id);
        } catch (\Exception $e) {
            sendToLog($e);
        }
    }

    function savePaymentMethod($data)
    {
        $stripeUser = StripeUser::where('customer_id', $data->customer)->first();

        if (!$stripeUser) {
            sendToLog("stripe user {$data->customer} not found");
            return false;
        }

        // dispatch(new StripStorePaymentMethod($stripeUser, $data->payment_method));

        $dataLoad = ['customer_id' => $stripeUser->customer_id, 'payment_method' => $data->payment_method];

        $response = $this->stripeService->retrievePaymentMethod($dataLoad);

        $provider = \App\Models\Provider::where('short_name', 'strip')->first();

        if ($provider) {
            $payment = \App\Models\PaymentMethod::updateOrCreate(
                [
                    'user_id' => $stripeUser->user_id,
                    'provider_id' => $provider->id,
                    'pm_id' => $response->id
                ],
                [
                    'card_brand' => $response->card->brand,
                    'last4' =>  $response->card->last4,
                    'details' => json_encode($response)
                ]
            );
        }
    }


    function processInternalInvoice($metadata, $externalReference)
    {
        $invoice = Invoice::whereUuid($metadata->invoice)->first();

        if (!$invoice) {
            sendToLog("invoice {$metadata->invoice} not found");
            return false;
        }

        if (!$invoice->is_paid) {

            // check if invoice has a user
            if (!empty($invoice->user_id)) {
                $user = User::where('email', $metadata->email)->first();
            } else {
                $userPayload = json_decode($invoice->user_payload);

                $password = \Illuminate\Support\Str::random(5);

                $user = User::create([
                    'first_name' => $userPayload->first_name,
                    'last_name' => $userPayload->last_name,
                    'email' => $userPayload->email,
                    'username' => $userPayload->username,
                    'parent_id'  => $userPayload->parent_id,
                    'password' => Hash::make($password)
                ]);

                UserInfo::where('user_id', $user->id)->update([
                    'country_code' => $userPayload->country,
                ]);

                $mailData = [
                    'username' => $user->username,
                    'name' => $user->first_name,
                    'password' => $password
                ];

                $user->notify(new NewAccountCreated($mailData));
            }

            // get the service
            if (!$user) {
                sendToLog("user {$metadata->email} not found");
                return false;
            }

            $service = Service::whereUuid($metadata->service_id)->first();

            if (!$service) {
                sendToLog("service {$metadata->service_id} not found");
                return false;
            }

            $subscriptionService = new SubscriptionService();

            $subscriptionService->startService($service, $user);

            $invoice->update([
                'user_id' => $user->id,
                'is_paid' => true,
                'external_reference' => $externalReference
            ]);
        }
    }
}
