<div>
    <x-button
        label="{{ __('Impersonate') }}"
        icon="{{ $this->icon }}"
        wire:click="impersonate"
        class="btn-outline {{ $classes }}"
    />
</div>
