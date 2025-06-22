<div class="space-x-1">
    @foreach ($tabs as $tab)
        <x-button
            :label="$tab['name']"
            class="{{ $tab['active'] ? 'btn-accent' : 'btn-secondary' }} rounded-3xl text-white"
            :icon="$tab['icon']"
            wire-navigate
            link="{{ route($tab['route']) }}"
        />        
    @endforeach
</div>