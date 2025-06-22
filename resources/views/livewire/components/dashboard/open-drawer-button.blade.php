<?php

use function Livewire\Volt\{state};

$openDrawer = function () {
    $this->dispatch('open-drawer');
};

?>

<div class="fixed bottom-4 right-4">
    <x-button icon="s-plus" class="btn-circle btn-lg btn-primary" @click="$wire.openDrawer" />
</div>
