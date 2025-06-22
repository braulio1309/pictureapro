<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use App\Models\User;
use App\Models\Booking;
use App\Enums\BookingStatus;
use App\Enums\PaymentStatus;
use App\Notifications\Bookings\ClientConfirmed;
use App\Notifications\Bookings\PhotographerConfirmed;

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
}
