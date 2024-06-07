<?php

namespace App\Http\Controllers;

use App\Models\AmbassedorPayments;
use App\Models\Bonus;
use App\Models\Service;
use App\Models\User;
use App\Models\UserActivities;
use App\Models\UserSubscription;
use App\Services\StripeService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    function success()
    {
        return view('user.stripe.success');
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

            $data['title'] = "Payment Successful";
            $data['message'] = "Your account has been successfully upgraded to Ambassador status. You can now refer others and earn sales bonuses!";
            $data['image'] = asset('assets/images/success.png');
            $data['success'] = true;

            return view('user.stripe.success', $data);
        }

        $data['title'] = "Payment Awaiting Approval";
        $data['message'] = "Your payment has been received and is awaiting approval";
        $data['image'] = asset('assets/images/pending.png');
        $data['success'] = false;

        return view('user.stripe.success', $data);
    }

    function subscriptionSuccess(Request $request, StripeService $stripeService)
    {
        $sessionId = $request->get('session_id');

        $session = $stripeService->retrieveSession($sessionId);

        if ($session->payment_status === 'paid') {

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


            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays($service->duration);

            $userSubscription = UserSubscription::where('user_id', $user->id)->where('service_id', $service->id)->first();

            if ($userSubscription) {
                if (!$userSubscription->is_active) {
                    $userSubscription->update([
                        'start_date' => Carbon::now(),
                        'end_date' => Carbon::now()->addDays($service->duration),
                        'is_active' => true
                    ]);

                    $message = "You have successfully renewed the {$service->name} service.";

                    UserActivities::create([
                        'user_id' => $user->id,
                        'log'  => "Renewed subscription for {$service->name} service."
                    ]);
                } else {
                    $data['title'] = "Payment Successful";
                    $data['message'] = "Your subscription is active";
                    $data['image'] = asset('assets/images/success.png');
                    $data['success'] = true;

                    return view('user.stripe.success', $data);
                }
            } else {
                UserSubscription::create([
                    'user_id' => $user->id,
                    'service_id' => $service->id,
                    'reference'  => generateReference(),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'is_active' => true
                ]);

                $message = "You have successfully subscribed to the {$service->name} service.";

                UserActivities::create([
                    'user_id' => $user->id,
                    'log'  => "Subscribed to {$service->name} service."
                ]);
            }

            $data['title'] = "Payment Successful";
            $data['message'] = $message;
            $data['image'] = asset('assets/images/success.png');
            $data['success'] = true;

            return view('user.stripe.success', $data);
        }

        $data['title'] = "Payment Awaiting Approval";
        $data['message'] = "Your payment has been received and is awaiting approval";
        $data['image'] = asset('assets/images/pending.png');
        $data['success'] = false;

        return view('user.stripe.success', $data);
    }

    function cancel()
    {
        return view('user.stripe.cancel');
    }

    function webhook(Request $request, StripeService $stripeService)
    {

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        logger($payload);

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
