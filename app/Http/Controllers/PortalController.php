<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PortalController extends Controller
{
    public function portal(Request $request)
    {
        $user = $request->user();

        // URL del portal de facturación de Stripe
       // $url = $user->billingPortalUrl(route('subscriptions.plans'));

        return redirect()->away($url);
    }
}
