<?php
 
use App\Models\User;
use function Livewire\Volt\{state};
use function Livewire\Volt\{computed};
use function Livewire\Volt\{action};
 
state('user');
state('classes');

$toggle = action(function () {
    $this->user->update(['is_enabled' => !$this->user->is_enabled]);
    $this->dispatch('user:toggle-enabled');
});

$style = computed(function () {
    $btn = $this->user->is_enabled ? 'btn-accent' : 'btn-primary';
    return $btn.' '.$this->classes;
});

$icon = computed(function () {
    if (str_contains($this->classes, 'btn-sm')) {
        return '';
    }

    return $this->user->is_enabled ? 'o-x-mark' : 'o-check';
});

?>
 
<div>
    <x-button
        label="{{ $this->user->is_enabled ? 'Desactivar' : 'Activar' }}"
        icon="{{ $this->icon }}"
        wire:click="toggle"
        class="{{ $this->style }}"
    />
</div>