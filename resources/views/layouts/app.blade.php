<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title', 'Espay Admin')</title>

    {{-- Tailwind --}}
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    {{-- Alpine --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Material Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    
    {{-- Inter Font --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <style>
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
        }
        [x-cloak] { display: none !important; }
    </style>

</head>

{{-- Note: x-data moved to body so all partials share same Alpine scope --}}

<body class="h-full bg-gray-50 text-gray-800" x-data="{ sidebarOpen: window.innerWidth >= 768 }" x-init="console.log('Alpine initialized, sidebarOpen:', sidebarOpen)">
    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar (partial) --}}
        @include('layouts.partials.sidebar')

        {{-- Main content --}}
        <div class="flex flex-col flex-1 min-w-0 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-20'">
            {{-- Header --}}
            @include('layouts.partials.header')

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 p-6">
                @yield('content')
            </main>

            {{-- Footer --}}
            @include('layouts.partials.footer')
        </div>
    </div>
</body>

</html>
