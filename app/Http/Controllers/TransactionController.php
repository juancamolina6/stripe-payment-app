<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\PaymentIntent;

class TransactionController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type == 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;
            // Lógica para manejar el evento de pago exitoso
        }

        return response()->json(['status' => 'success']);
    }

    public function showTransactionForm()
    {
        return view('transaction');
    }

    public function checkTransactionStatus(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $paymentIntent = PaymentIntent::retrieve($request->transaction_id);
            return view('transaction', ['status' => $paymentIntent->status]);
        } catch (\Exception $e) {
            return view('transaction', ['error' => 'Transaction not found or invalid ID']);
        }
    }
}
