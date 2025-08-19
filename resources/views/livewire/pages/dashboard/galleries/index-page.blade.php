<div>
    <x-header title="Galerias" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input
                placeholder="Buscar..."
                wire:model.live.debounce.750ms="search"
                clearable
                icon="o-magnifying-glass"
                class="rounded-3xl" />
        </x-slot:middle>
    </x-header>

    <div class="flex flex-col gap-3 md:flex-row md:justify-between">
        <livewire:components.dashboard.active-switcher />
    </div>

    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8 py-5">
        @foreach ($galleries as $product)
        <div class="col-span-1">
            <x-card class="shadow-lg hover:scale-105 transition-all">
                <p class="font-medium">{{ $product->internal_title }}</p>

                <x-slot:figure>
                    @if($product->photos->isNotEmpty())
                    <img src="{{ asset('storage/' . $product->photos->first()->path) }}" alt="Cover">
                    @else
                    <img src="{{ asset('images/placeholder.webp') }}" alt="Sin cover">
                    @endif
                </x-slot:figure>
                <x-slot:actions>
                    <x-button
                        label="Ver"
                        class="btn-primary btn-outline btn-sm rounded-3xl"
                        wire:click="openDrawer('edit', {{ $product->id }})" />
                </x-slot:actions>
            </x-card>
        </div>
        @endforeach
    </div>

    <div class="fixed bottom-4 right-4">
        <x-button icon="s-plus" class="btn-circle btn-lg btn-primary" @click="$wire.openDrawer" />
    </div>

    <livewire:components.dashboard.galleries.drawer />
</div>