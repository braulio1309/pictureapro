<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) ? $title.' - '.config('app.name') : config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <meta name="apple-mobile-web-app-title" content="PictureaPRO" />
    <link rel="manifest" href="/site.webmanifest" />

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script src="{{ asset('js/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr@4.6.13/dist/l10n/es.js"></script>
</head>
<body class="min-h-screen font-sans antialiased bg-white">
    {{-- mobile navbar --}}
    <x-nav sticky class="lg:hidden">
        <x-slot:brand>
            <x-app-brand />
        </x-slot:brand>
        <x-slot:actions>
            <label for="main-drawer" class="lg:hidden me-3">
                <x-icon name="o-bars-3" class="cursor-pointer" />
            </label>
        </x-slot:actions>
    </x-nav>

    <x-main full-width>
        <livewire:components.dashboard.sidebar />

        <x-slot:content>
            <div class="max-w-screen-xl mx-auto">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    {{--  TOAST area --}}
    <x-toast />

    @livewireScriptConfig
    @stack('scripts')

</body>
</html>
