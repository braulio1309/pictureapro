<div class="flex justify-center items-center min-h-screen bg-gray-100">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-xl rounded-2xl p-6">
            <h2 class="text-2xl font-semibold text-gray-800 text-center mb-4">
                Suscríbete al Plan
            </h2>
            <p class="text-gray-600 text-center mb-6">
                Ingresa tu tarjeta para activar tu suscripción mensual.
            </p>

            <form id="payment-form" wire:submit.prevent="subscribe" class="space-y-4">
                <!-- Stripe Card Element -->
                <div id="card-element" class="p-3 border border-gray-300 rounded-lg"></div>

                <input type="hidden" id="payment-method" wire:model="paymentMethod">

                <button type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg font-semibold shadow-md transition duration-200 ease-in-out">
                    Suscribirse
                </button>
            </form>

            <!-- Mensajes de éxito o error -->
            @if (session()->has('success'))
                <div class="mt-4 bg-green-100 text-green-800 p-3 rounded-lg text-sm text-center">
                    {{ session('success') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mt-4 bg-red-100 text-red-800 p-3 rounded-lg text-sm text-center">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Stripe JS -->
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const cardElement = elements.create('card', { style: { base: { fontSize: '16px' } } });
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const {paymentMethod, error} = await stripe.createPaymentMethod({
            type: 'card',
            card: cardElement,
        });

        if (error) {
            alert(error.message);
        } else {
            @this.set('paymentMethod', paymentMethod.id);
            @this.call('subscribe');
        }
    });
</script>
