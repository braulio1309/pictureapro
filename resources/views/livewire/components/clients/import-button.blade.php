<button 
    wire:click="navigateToImport"
    class="{{ $btnClass }}"
    title="{{ $label }}"
    wire:loading.attr="disabled"
>
    <span wire:loading.remove>
        <i class="fas fa-{{ $icon }} mr-2"></i> {{ $label }}
    </span>
    <span wire:loading>
        <span class="spinner-border spinner-border-sm mr-2" role="status"></span>
        Cargando...
    </span>
</button>