<?php

namespace App\Http\Controllers;

use App\Mail\SubscriptionMail;
use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\StripeUser;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use App\Services\StripeService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
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

            // check if user exist on stripe
            $stripUser = StripeUser::where('user_id', $user->id)->first();

            if (!$stripUser) {
                $userData = [
                    'email'  => $user->email,
                    'name'  => $user->name,
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

                $data = [
                    'amount'        => $service->price * 100,
                    'product_data'  => [
                        'name'      => $service->name
                    ],
                    'customer_email' => $user->email,
                    'customer_name' => $user->name,
                    'customer_id'    => $stripUser->customer_id,
                    'metadata'       => ['user_id' => $user->uuid, 'service_id' => $service->uuid, 'invoice' => $invoice->uuid],
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
            } else {
                $data = [
                    'amount' => $service->price * 100,
                    'customer'    => $stripUser->customer_id,
                    'payment_method' => $stripUser->payment_method,
                    'metadata'       => ['user_id' => $user->uuid, 'service_id' => $service->uuid, 'invoice' => $invoice->uuid],
                    'success_url'    => route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                ];

                $response  = $this->stripeService->paymentIntent($data);

                logger($response);

                return route('payment.success');
            }

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
            logger($e->getMessage());
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            logger($e->getMessage());
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
            default:
                echo 'Received unknown event type ' . $event->type;
                break;
        }

        return response('Successful', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }

    function chargeCompleted($data)
    {
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
        $stripeUser = StripeUser::where('customer_id', $data->customer)->first();

        if (!$stripeUser) {
            sendToLog("stripe user {$data->customer} not found");
            return false;
        }

        $stripeUser->update([
            'payment_method'  => $data->payment_method
        ]);
    }

    function paymentIntentCompleted($data)
    {
        try {
            $this->processInternalInvoice($data->metadata, $data->id);
        } catch (\Exception $e) {
            sendToLog($e);
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

            $user = User::whereUuid($metadata->user_id)->first();

            // get the service
            if (!$user) {
                sendToLog("user {$metadata->user_id} not found");
                return false;
            }

            $service = Service::whereUuid($metadata->service_id)->first();

            if (!$service) {
                sendToLog("user {$metadata->service_id} not found");
                return false;
            }

            $subscriptionService = new SubscriptionService();

            $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $service->id)->first();

            if ($userSubscription) {
                $subscriptionService->updateSubscription($service, $userSubscription);
            } else {
                $subscriptionService->createSubscription($service, $user);
            }

            $invoice->update([
                'is_paid' => true,
                'external_reference' => $externalReference
            ]);
        }
    }
}
