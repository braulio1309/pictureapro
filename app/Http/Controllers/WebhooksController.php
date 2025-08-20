<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Notifications\Bookings\ClientConfirmed;
use App\Notifications\Bookings\PhotographerConfirmed;
use Stripe\Webhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;



class WebhooksController extends Controller
{
    public function stripe(string $code)
    {
        $user = User::query()
            ->whereRaw("MD5(id) = '$code'")
            ->firstOrFail();

        Stripe::setApiKey($user->stripe_priv);

        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $user->stripe_wh_secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                break;
            default:
                // Unexpected event type
                http_response_code(400);
                exit();
        }

        http_response_code(200);

        $booking = Booking::query()
            ->with(['calendar', 'calendar.tenant', 'client'])
            ->findOrFail($session->metadata->booking_id);

        $booking->update(['status' => BookingStatus::CONFIRMED]);
        $booking->payment()->update(['status' => PaymentStatus::COMPLETED]);

        $booking->calendar->tenant->notify(new PhotographerConfirmed($booking));
        $booking->client->notify(new ClientConfirmed($booking));
    }

    public function handle(Request $request)
    {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->server('HTTP_STRIPE_SIGNATURE');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Manejar eventos según el tipo
        switch ($event->type) {
            case 'customer.subscription.created':
                Log::info('Nueva suscripción creada', $event->data->object);
                break;

            case 'customer.subscription.updated':
                Log::info('Suscripción actualizada', $event->data->object);
                break;

            case 'invoice.payment_succeeded':
                Log::info('Pago exitoso', $event->data->object);
                break;

            case 'invoice.payment_failed':
                Log::warning('Pago fallido', $event->data->object);
                break;

            default:
                Log::info('Evento recibido: ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }
}
