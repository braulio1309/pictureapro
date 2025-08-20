<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Product;
use Stripe\Price;
use App\Http\Controllers\Controller;


class PlanController extends Controller
{
    public function index()
    {
        // Listar planes desde Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        $plans = Price::all([
            'limit' => 10,
            'expand' => ['data.product']
        ]);

        return view('livewire.pages.dashboard.plans.index-page', compact('plans'));
    }

    public function create(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        // Crear un producto (plan)
        $product = Product::create([
            'name' => $request->name,
        ]);

        // Crear un precio (tarifa del plan)
        $price = Price::create([
            'unit_amount' => $request->amount * 100, // en centavos
            'currency' => 'usd',
            'recurring' => ['interval' => $request->interval], // month, year
            'product' => $product->id,
        ]);

        return response()->json([
            'product' => $product,
            'price' => $price
        ]);
    }
}
