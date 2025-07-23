<div>
    <x-button
        wire:click="export"
        wire:loading.attr="disabled"
        wire:target="export"
        class="{{ $btnClass }} position-relative"
        @if($tooltip) title="{{ $tooltip }}" @endif
    >
        <span wire:loading.remove wire:target="export">
            <i class="fas fa-{{ $icon }} mr-2"></i>{{ $label }}
        </span>
        
        <span wire:loading wire:target="export">
            <span class="spinner-border spinner-border-sm mr-2" role="status">
                <span class="sr-only">Cargando...</span>
            </span>
            Generando...
        </span>
        
        @if($badge)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ $badge }}
            </span>
        @endif
    </button>
    
    @if($helpText)
        <small class="form-text text-muted mt-1">{{ $helpText }}</small>
    @endif
</div>