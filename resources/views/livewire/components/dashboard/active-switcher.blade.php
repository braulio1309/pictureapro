<div>
    @foreach ($this->statuses as $status)
        <x-button
            :label="$status['name']"
            class="{{ $status['active'] ? 'btn-accent' : 'btn-secondary' }} text-white rounded-3xl"
            :icon="$status['icon']"
            wire:click="emitChange({{ $status['value'] }})"
            wire:loading.attr="disabled"
        />        
    @endforeach
</div>