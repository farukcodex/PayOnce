<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\PaymentSuccessNotification;
use Illuminate\Http\Request;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $secret
            );
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        // ✅ Payment completed
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $order = Order::where('stripe_session_id', $session->id)->first();

            if ($order) {
                $order->update([
                    'status' => 'paid',
                    'stripe_payment_intent_id' => $session->payment_intent,
                ]);
            }
            // 🔔 Send notification
            $order->user->notify(
                new PaymentSuccessNotification($order)
            );
        }

        return response('Webhook handled', 200);
    }
}
