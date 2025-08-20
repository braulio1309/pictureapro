<?php

namespace App\Livewire\Pages\Dashboard\Subscription;

use Livewire\Component;
use Stripe\Stripe;
use Stripe\Price;
use Illuminate\Support\Facades\Auth;


class IndexPage extends Component
{
    public $plans = [];

    public $paymentMethod;

    public function subscribe()
    {
        $user = Auth::user();

        try {
            // Suscribir al usuario al plan mensual
            $user->newSubscription('default', 'price_1Rxw0zENtaXS7qqxH0SAC67R')
                ->create($this->paymentMethod);

            session()->flash('success', 'Suscripción creada con éxito');
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.pages.dashboard.subscriptions.index-page');
    }
}
