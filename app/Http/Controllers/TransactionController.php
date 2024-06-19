<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\PaymentIntent;

class TransactionController extends Controller
{
    /**
     * Maneja el webhook de Stripe para eventos de pago.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        // Establecer la clave secreta de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Obtener el payload y la firma del encabezado
        $payload = @file_get_contents('php://input');
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            // Construir el evento webhook
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Manejar diferentes tipos de eventos
        switch ($event->type) {
            case 'payment_intent.succeeded':
                return $this->handlePaymentSucceeded($event->data->object);
            case 'payment_intent.payment_failed':
                return $this->handlePaymentFailed($event->data->object);
            default:
                Log::warning('Unhandled event type: ' . $event->type);
                return response()->json(['status' => 'unhandled_event'], 200);
        }
    }

    /**
     * Maneja un evento de pago exitoso.
     *
     * @param PaymentIntent $paymentIntent
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handlePaymentSucceeded($paymentIntent)
    {
        Log::info('Payment succeeded:', (array) $paymentIntent);

        // Aquí podrías ejecutar acciones adicionales si el pago fue exitoso

        return response()->json([
            'status' => 'success',
            'message' => 'La transacción fue exitosa.'
        ]);
    }

    /**
     * Maneja un evento de pago fallido.
     *
     * @param PaymentIntent $paymentIntent
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handlePaymentFailed($paymentIntent)
    {
        Log::error('Payment failed:', (array) $paymentIntent);

        // Aquí podrías ejecutar acciones adicionales si el pago falló

        return response()->json([
            'status' => 'failed',
            'message' => 'La transacción falló.'
        ]);
    }

    /**
     * Muestra el formulario para verificar el estado de una transacción.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function showTransactionForm()
    {
        return view('transaction');
    }

    /**
     * Verifica el estado de una transacción basada en el ID proporcionado.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function checkTransactionStatus(Request $request)
    {
        // Validar la entrada del formulario
        $request->validate([
            'transaction_id' => 'required|string',
        ]);

        // Establecer la clave secreta de Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Obtener el objeto PaymentIntent desde Stripe
            $paymentIntent = PaymentIntent::retrieve($request->transaction_id);

            // Devolver la vista con el estado de la transacción
            return view('transaction', ['status' => $paymentIntent->status]);
        } catch (\Exception $e) {
            // Manejar errores si no se encuentra la transacción o el ID es inválido
            return view('transaction', ['error' => 'Transacción no encontrada o ID inválido']);
        }
    }
}
