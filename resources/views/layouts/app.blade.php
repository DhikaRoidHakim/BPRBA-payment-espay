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

        [x-cloak] {
            display: none !important;
        }

        /* Toast Animation */
        @keyframes slide-in {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>

</head>

{{-- Note: x-data moved to body so all partials share same Alpine scope --}}

<body class="h-full bg-gray-50 text-gray-800" x-data="{ sidebarOpen: window.innerWidth >= 768 }">
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
    <script src="https://js.pusher.com/8.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script>
        // Toast Notification Function
        function showToast(message, trxId) {
            // Create toast container if not exists
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-4 right-4 z-50 space-y-2';
                document.body.appendChild(container);
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.style.cssText = 'min-width: 320px; max-width: 28rem;';
            toast.className = 'bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-start gap-3 animate-slide-in';
            toast.innerHTML = `
                <span class="material-symbols-outlined text-2xl">check_circle</span>
                <div class="flex-1">
                    <div class="font-semibold">${message}</div>
                    <div class="text-sm opacity-90">ID: ${trxId}</div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-white hover:text-gray-200">
                    <span class="material-symbols-outlined">close</span>
                </button>
            `;

            container.appendChild(toast);

            // Auto remove after 5 seconds
            setTimeout(() => {
                toast.style.animation = 'slide-out 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        Pusher.logToConsole = false;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true,
            authEndpoint: '/broadcasting/auth',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }
        });

        // Subscribe ke private user channel
        const channel = Echo.private('App.Models.User.{{ auth()->id() }}');

        // Listener utama untuk notification
        channel.notification((notification) => {

            // update badge
            const badge = document.getElementById('notifBadge');
            const count = parseInt(badge.innerText || 0) + 1;
            badge.innerText = count;

            // prepend ke list
            const list = document.getElementById('notifList');
            const html = `
        <a href="#" class="block px-4 py-3 border-b hover:bg-gray-50">
          <div class="text-sm">${notification.message} <strong>${notification.trx_id}</strong></div>
          <div class="text-xs text-gray-400">baru saja</div>
        </a>
      `;
            list.insertAdjacentHTML('afterbegin', html);

            // Show toast notification (non-intrusive popup)
            try {
                showToast(notification.message, notification.trx_id);
            } catch (error) {
                console.error('Toast error:', error);
            }
        });

        channel.error((error) => {
            console.error('Echo channel error:', error);
        });
    </script>

</body>

</html>
