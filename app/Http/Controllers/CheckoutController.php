<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stripe\StripeClient;

class CheckoutController extends Controller
{
   public function subscribe(Request $request)
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        // Crear suscripción en Stripe
        $subscription = $stripe->subscriptions->create([
            'customer' => $request->user()->stripe_id,
            'items' => [[
                'price' => $request->price_id,
            ]],
        ]);

        // Guardar en tu base de datos
        $request->user()->update([
            'stripe_subscription_id' => $subscription->id,
        ]);

        return redirect()->route('dashboard')->with('success', 'Suscripción creada con éxito.');
    }

    
}
