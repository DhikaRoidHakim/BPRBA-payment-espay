<header class="h-16 bg-white border-b border-gray-200 shadow-sm flex items-center justify-between px-4 md:px-6 relative z-30">
    <div class="flex items-center gap-3">
        <!-- burger: type="button", high z to ensure clickable -->
        <button type="button" @click="sidebarOpen = !sidebarOpen; console.log('Burger clicked, sidebarOpen:', sidebarOpen)"
            class="p-2 rounded-md text-gray-600 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-200"
            aria-label="Toggle sidebar">
            <span class="material-symbols-outlined text-2xl">menu</span>
        </button>

        <h2 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
    </div>

    <div class="flex items-center gap-4">
        {{-- search / actions area (placeholder) --}}
        <div class="hidden sm:block">
            <input type="search" placeholder="Search..."
                class="px-3 py-2 border rounded-lg text-sm focus:ring-1 focus:ring-blue-300" />
        </div>

        {{-- user dropdown handled here for desktop --}}
        <div class="relative" x-data="{ openUser: false }">
            <button type="button" @click="openUser = !openUser" class="flex items-center gap-3 focus:outline-none"
                aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name=Dhika+Roid+Hakim&background=2563eb&color=fff"
                    class="w-9 h-9 rounded-full" alt="avatar">
                <div class="hidden md:flex flex-col text-left">
                    <span class="text-sm font-semibold text-gray-800">Dhika Roid Hakim</span>
                    <span class="text-xs text-gray-500">Administrator</span>
                </div>
                <span class="material-symbols-outlined text-gray-500">expand_more</span>
            </button>

            <div x-show="openUser" x-transition @click.outside="openUser=false"
                class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50">
                <a href="{{ url('/profile/reset-password') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reset Password</a>
                <a href="{{ url('/logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>
</header>
