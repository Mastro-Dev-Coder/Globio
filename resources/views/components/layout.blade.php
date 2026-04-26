<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ \App\Models\Setting::getValue('site_name') }}</title>
    <meta name="description" content="{{ \App\Models\Setting::getValue('site_name') }} - La piattaforma video italiana">
    <meta name="keywords" content="video, streaming, italiano, entertainment">
    <meta name="author" content="{{ \App\Models\Setting::getValue('site_name') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::getValue('logo')) }}">
    <script>
        (() => {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = savedTheme ? savedTheme === 'dark' : prefersDark;

            document.documentElement.classList.toggle('dark', shouldUseDark);

            const metaThemeColor = document.querySelector('meta[name="theme-color"]');
            if (metaThemeColor) {
                metaThemeColor.content = shouldUseDark ? '#111827' : '#ffffff';
            }
        })();
    </script>
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
        <div id="mainContainer" class="flex mt-16 main-content-safe lg:h-[calc(100vh-4rem)] lg:overflow-hidden"
            style="margin-bottom: 0px;">
            @if (!request()->routeIs('register') && !request()->routeIs('login'))
                <x-sidebar />
            @endif

            <!-- Content Area -->
            <div class="flex-1 lg:h-[calc(100vh-4rem)] lg:overflow-y-auto lg:overscroll-contain">
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
