<div class="grid md:grid-cols-2 gap-6">
    @foreach($plans as $plan)
        <div class="p-6 rounded-xl shadow">
            <h3 class="text-xl font-semibold">{{ $plan['name'] }}</h3>
            <ul class="mt-2 text-sm opacity-80">
                @foreach(($plan['features'] ?? []) as $f)
                    <li>• {{ $f }}</li>
                @endforeach
            </ul>

            @auth
                @if(auth()->user()->subscribed('default'))
                    <a href="{{ route('subscriptions.portal') }}" class="btn btn-secondary mt-4">
                        Gestionar suscripción
                    </a>
                @else
                    <form method="POST" action="{{ route('subscriptions.subscribe') }}" class="mt-4">
                        @csrf
                        <input type="hidden" name="price_id" value="{{ $plan['price_id'] }}">
                        <button type="submit" class="btn btn-primary">Suscribirme</button>
                    </form>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-primary mt-4">Inicia sesión</a>
            @endauth
        </div>
    @endforeach
</div>
