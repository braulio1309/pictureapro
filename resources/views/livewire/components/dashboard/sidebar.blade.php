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
                title="Inicio"
                icon="s-sparkles"
                link="{{ route('dashboard') }}"
                route="dashboard"
            />
            <x-menu-item
                title="Mi cuenta"
                icon="s-cog-6-tooth"
                link="{{ route('account.details') }}"
                route="account.*"
            />

            <x-menu-item
                title="Clientes"
                icon="s-users"
                link="{{ route('dashboard.clients.index') }}"
                route="dashboard.clients.*"
            />

            <x-menu-sub
                title="Productos"
                icon="s-shopping-cart"
            >
                <x-menu-item
                    title="Materiales"
                    icon="s-table-cells"
                    link="{{ route('dashboard.materials.index') }}"
                    route="dashboard.materials.*"
                />

                <x-menu-item
                    title="Productos"
                    icon="s-shopping-cart"
                    link="{{ route('dashboard.products.index') }}"
                    route="dashboard.products.*"
                />
            </x-menu-sub>

            <x-menu-item
                title="Servicios"
                icon="s-list-bullet"
                link="{{ route('dashboard.services.index') }}"
                route="dashboard.services.*"
            />

            <x-menu-item
                title="Calendarios"
                icon="s-calendar"
                link="{{ route('dashboard.calendars.index') }}"
                route="dashboard.calendars.*"
            />

            <x-menu-item
                title="Galerías"
                icon="s-photo"
                link="{{ route('dashboard.galleries.index') }}"
                route="dashboard.galleries.*"
            />
        </x-menu>
    </x-slot:sidebar>
</div>
