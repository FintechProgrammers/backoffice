<?php

namespace App\Http\Controllers;

use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Service;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use App\Services\StripeService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class StripeController extends Controller
{

    function iniatePayment($service=null)
    {
        try {

            $stripeService = new \App\Services\StripeService();

            $user = auth()->user();

            if (!empty($service)) {

                $data = [
                    'currency' => 'usd',
                    'amount' => $service->price * 100,
                    'product_data' => [
                        'name' => $service->name
                    ],
                    'customer_email' => $user->email,
                    'metadata' => ['user_id' => $user->id, 'service_id' => $service->id],
                    'success_url' => route('stripe.service.payment.Success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('payment.cancel')
                ];

                $response = $stripeService->processCheckout($data);

                return $response->url;
            } else {
                $data = [
                    'currency' => 'usd',
                    'amount' => systemSettings()->ambassador_fee * 100,
                    'product_data' => [
                        'name' => "Ambassedor Account Setup"
                    ],
                    'customer_email' => $user->email,
                    'metadata' => ['user_id' => $user->id],
                    'success_url' => route('stripe.abassador.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                    'cancel_url' => route('payment.cancel')
                ];

                $response = $stripeService->processCheckout($data);

                return $response->url;
            }
        } catch (\Exception $e) {
            sendToLog($e);

            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function abassedorPaymentSuccess(Request $request, StripeService $stripeService)
    {
        $sessionId = $request->get('session_id');

        $session = $stripeService->retrieveSession($sessionId);

        if ($session->payment_status === 'paid') {

            // make user an ambassedor
            $user = User::where('id', $session->metadata->user_id)->first();

            if (!$user->is_ambassedor) {

                AmbassedorPayments::create([
                    'reference' => generateReference(),
                    'user_id' => $session->metadata->user_id,
                    'amount'  => $session->amount_total / 100
                ]);

                $user->update([
                    'is_ambassador' => true
                ]);

                Bonus::create([
                    'user_id' => $user->id,
                    'amount' => 0
                ]);

                UserActivities::create([
                    'user_id' => $user->id,
                    'log'  => 'Payment for Ambassedor Account.'
                ]);
            }

            $title = "Payment Successful";
            $message = "Your account has been successfully upgraded to Ambassador status. You can now refer others and earn sales bonuses!";

            return redirect()->route('payment.success')->with(['message' => $message, 'success' => true, 'title' => $title]);
        }

        $title = "Payment Awaiting Approval";
        $message = "Your payment has been received and is awaiting approval";

        return redirect()->route('payment.success')->with(['message' => $message, 'success' => false, 'title' => $title]);
    }

    function subscriptionSuccess(Request $request, StripeService $stripeService)
    {
        $sessionId = $request->get('session_id');

        $session = $stripeService->retrieveSession($sessionId);

        if ($session->payment_status === 'paid') {

            $title = "Payment Successful";
            $success = true;

            $user = User::find($session->metadata->user_id);

            if (!$user) {
                sendToLog("User not found");

                return redirect()->route('stripe.cancel');
            }

            $service = Service::find($session->metadata->service_id);

            if (!$service) {
                sendToLog("Service not found");

                return redirect()->route('stripe.cancel');
            }

            $subscriptionService = new SubscriptionService();

            $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $service->id)->first();

            if ($userSubscription) {
                if (!$userSubscription->is_active) {

                    $subscriptionService->updateSubscription($service, $userSubscription);

                    $message = "You have successfully renewed the {$service->name} service.";
                } else {
                    $message = "Your subscription is active";
                }
            } else {

                $subscriptionService->createSubscription($service, $user);

                $message = "You have successfully subscribed to the {$service->name} service.";
            }
        } else {
            $title = "Payment Awaiting Approval";
            $message = "Your payment has been received and is awaiting approval";
            $success = false;
        }

        return redirect()->route('payment.success')->with(['message' => $message, 'success' => $success, 'title' => $title]);
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

        $session = null;

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.async_payment_succeeded':
                $session = $event->data->object;
            case 'checkout.session.completed':
                $session = $event->data->object;
            case 'invoice.finalized':
                $invoice = $event->data->object;
            case 'invoice.overdue':
                $invoice = $event->data->object;
            case 'invoice.paid':
                $invoice = $event->data->object;
            case 'invoice.payment_action_required':
                $invoice = $event->data->object;
            case 'invoice.payment_failed':
                $invoice = $event->data->object;
            case 'invoice.payment_succeeded':
                $invoice = $event->data->object;
                // ... handle other event types
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        logger($session);

        http_response_code(200);
    }
}
