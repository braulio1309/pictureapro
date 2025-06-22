<div>
    <!-- HEADER -->
    <x-header title="Usuario" separator progress-indicator>
        <x-slot:actions>
            <x-button
                label="Volver"
                class="btn-outline rounded-3xl"
                icon="o-arrow-uturn-left"
                wire-navigate
                link="{{ route('admin.users.index') }}"
            />
        </x-slot:actions>
    </x-header>

    <x-card class="bg-white rounded-3xl shadow-xl">
        @foreach ($items as $item)
            <x-list-item
                :item="$item"
                value="label"
                sub-value="value"
            />
        @endforeach
    </x-card>

    <div class="flex space-x-2 mt-3">
        <livewire:components.admin.users.toggle-enabled-button :user="$user" classes="text-white rounded-3xl" />
        <livewire:components.admin.users.impersonate-button :user="$user" classes="rounded-3xl" />
    </div>
</div>
