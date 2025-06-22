<div>
    <x-header title="Materiales" separator progress-indicator>
        <x-slot:middle class="!justify-end">
            <x-input
                placeholder="Buscar..."
                wire:model.live.debounce.750ms="search"
                clearable
                icon="o-magnifying-glass"
                class="rounded-3xl"
            />
        </x-slot:middle>
    </x-header>

    <div class="flex w-full justify-end">
        <livewire:components.dashboard.active-switcher />
    </div>

    <div class="grid md:grid-cols-3 lg:grid-cols-4 gap-8 py-5">
        @foreach ($materials as $material)
            <div class="col-span-1">
                <x-card class="shadow-lg hover:scale-105 transition-all">
                    <p class="font-medium">{{ $material->name }}</p>
                    <span class="badge bg-slate-200">
                        @if($material->colors_count === 0)
                            Sin colores
                        @else
                            {{ $material->colors_count }} colores
                        @endif
                    </span>
                
                    <x-slot:figure>
                        <img src="{{ $material->getFirstMediaUrlCustom('cover') ?? asset('images/placeholder.webp') }}" />
                    </x-slot:figure>
                    <x-slot:actions>
                        <x-button
                            label="Ver"
                            class="btn-primary btn-outline btn-sm rounded-3xl"
                            wire:click="openDrawer('edit', {{ $material->id }})" 
                        />
                    </x-slot:actions>
                </x-card>
            </div>
        @endforeach
    </div>

    <div class="fixed bottom-4 right-4">
        <x-button icon="s-plus" class="btn-circle btn-lg btn-primary" @click="$wire.openDrawer" />
    </div>

    <livewire:components.dashboard.materials.drawer />
    <livewire:components.dashboard.materials.color-drawer />
</div>