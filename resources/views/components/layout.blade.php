<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::getValue('site_name') }}</title>
    <meta name="description" content="{{ \App\Models\Setting::getValue('site_name') }} - La piattaforma video italiana">
    <meta name="keywords" content="video, streaming, italiano, entertainment">
    <meta name="author" content="{{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ route('dynamic.styles') }}">
</head>

<body
    class="bg-gray-200/70 dark:bg-gray-900 text-gray-900 dark:text-gray-100 font-inter antialiased transition-colors duration-300">

    @if (!request()->routeIs('register') && !request()->routeIs('login'))
        <x-header />
    @endif

    @if (request()->routeIs('videos.show'))
        <main class="pt-16">
            <div class="pt-4">
                {{ $slot }}
            </div>
        </main>
    @else
        <div id="mainContainer" class="flex min-h-screen mt-16 main-content-safe" style="margin-bottom: 0px;">
            @if (!request()->routeIs('register') && !request()->routeIs('login'))
                <x-sidebar />
            @endif

            <!-- Content Area -->
            <div class="flex-1 lg:ml-0">
                <div class="max-w-full mx-auto">
                    {{ $slot }}
                </div>
            </div>
        </div>
    @endif

    <!-- Bottom Navigation per Mobile -->
    @if (!request()->routeIs('register') && !request()->routeIs('login'))
        <x-bottom-navigation />
    @endif

    <!-- Footer Advertisement -->
    <x-advertisements position="footer" />

    <!-- Connection Status Component -->
    <livewire:connection-status />

</body>

</html>
