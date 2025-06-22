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
    @vite(['resources/css/app.css', 'resources/css/public.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen font-sans antialiased bg-base-200/50">
    <x-main full-width>
        <x-slot:content class="!p-0">
            <div class="max-w-screen-xl mx-auto">
                {{ $slot }}
            </div>
        </x-slot:content>
    </x-main>

    <x-toast />

    @livewireScriptConfig
</body>
</html>
