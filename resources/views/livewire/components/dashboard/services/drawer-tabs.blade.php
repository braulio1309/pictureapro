<div>
    @foreach ($this->tabs as $tab)
        <x-button
            :label="$tab['name']"
            class="{{ $tab['active'] ? 'btn-accent' : 'btn-secondary' }} text-white rounded-3xl"
            :icon="$tab['icon']"
            wire:click="$parent.changeTab('{{ $tab['value'] }}')"
        />
    @endforeach
</div>