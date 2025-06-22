<?php

namespace App\Http\Controllers;

use App\Mail\UserWelcome;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    public function logout()
    {
        auth()->logout();
     
        return redirect()->route('login');
    }

    public function verifyEmail($id, $hash, EmailVerificationRequest $request)
    {
        $request->fulfill();
        $user = $request->user();

        Mail::to($user)
            ->send(
                (new UserWelcome($user->name))->afterCommit()
            );
     
        return redirect()
            ->route('dashboard')
            ->with('success', 'Correo verificado correctamente');
    }

    public function stripe()
    {
        $user = Auth::user();
        
        \Stripe\Stripe::setApiKey($user->stripe_priv);

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Example Product',
                    ],
                    'unit_amount' => 200,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('login'),
            'cancel_url' => route('login'),
            'metadata' => [
                'booking_id' => 'ASD123',
            ],
        ]);

        return redirect($session->url);
    }
}
