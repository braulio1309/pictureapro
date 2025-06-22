<div>
    <x-slot:sidebar
        drawer="main-drawer"
        collapsible
        collapse-text="Ocultar"
        class="bg-primary text-white px-2"
        id="dashboard-sidebar"
    >
        <x-app-brand class="p-5 pt-3" />

        <x-menu activate-by-route>

            @if($user = auth()->user())
                <x-list-item :item="$user" value="name" sub-value="email" no-separator no-hover class="-mx-2 mt-3 mb-5 rounded">
                    <x-slot:actions>
                        <x-button
                            icon="o-arrow-top-right-on-square"
                            class="btn-circle btn-ghost btn-xs"
                            tooltip-left="Cerrar sesión"
                            no-wire-navigate
                            link="{{ route('logout') }}"
                        />
                    </x-slot:actions>
                </x-list-item>
            @endif

            <x-menu-item
                title="Usuarios"
                icon="o-users"
                link="{{ route('admin.users.index') }}" route="admin.users.index"
            />
        </x-menu>
    </x-slot:sidebar>
</div>
