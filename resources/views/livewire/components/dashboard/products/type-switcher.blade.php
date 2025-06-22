<div>
    @foreach ($this->types as $type)
        <x-button
            :label="$type['name']"
            class="{{ $type['active'] ? 'btn-accent' : 'btn-secondary' }} text-white rounded-3xl"
            :icon="$type['icon']"
            wire:click="emitChange('{{ $type['value'] }}')"
        />        
    @endforeach
</div>