<?php

namespace App\Livewire\Pages\Dashboard\Users;

use Stripe\Stripe;
use Mary\Traits\Toast;
use Livewire\Component;
use App\Livewire\Forms\UserForm;
use Illuminate\Support\Facades\Auth;

class PaymentsPage extends Component
{
    use Toast;

    public UserForm $form;

    public function mount()
    {
        $this->form->set(Auth::user());
    }

    public function update()
    {
        $old_stripe_priv = $this->form->user->stripe_priv;
        $user = $this->form->update();

        $this->success('Información actualizada', css: 'bg-primary text-white');

        if (!empty($user->stripe_priv)) {
            if ($old_stripe_priv === $user->stripe_priv) {
                return;
            }

            // mover esto a un evento > listener
            Stripe::setApiKey($user->stripe_priv);

            $endpoint = \Stripe\WebhookEndpoint::create([
                'url' => route('webhooks.stripe', ['code' => md5((string) $user->id)]),
                // 'url' => 'https://68d7-24-232-97-51.ngrok-free.app/webhooks/stripe/'.md5((string) $user->id),
                'enabled_events' => [
                    'checkout.session.completed',
                ],
            ]);

            $user->update([
                'stripe_wh_id' => $endpoint->id,
                'stripe_wh_secret' => $endpoint->secret,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.dashboard.users.payments-page');
    }
}
