<aside
    class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 z-20 transition-all duration-300 ease-in-out overflow-hidden"
    :class="sidebarOpen ? 'w-64' : 'w-20'" x-init="$watch('sidebarOpen', value => console.log('Sidebar state changed:', value))">
    <div class="h-16 flex items-center border-b border-gray-100"
        :class="sidebarOpen ? 'justify-between px-6' : 'justify-center px-4'">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-white text-xl">account_balance</span>
            </div>
            <h1 class="text-base font-semibold text-gray-800 whitespace-nowrap transition-all duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Espay | IT PBA</h1>
        </div>
    </div>

    <nav class="mt-6 px-3 space-y-6 overflow-y-auto" style="max-height: calc(100vh - 180px);">
        {{-- Main Menu Section --}}
        <div>
            <div class="mb-3 overflow-hidden" :class="sidebarOpen ? 'px-3' : 'px-0'">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap transition-all duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Main Menu</h3>
            </div>
            <div class="space-y-1">
                <a href="{{ url('/') }}"
                    class="flex items-center gap-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition overflow-hidden"
                    :class="sidebarOpen ? 'px-3 py-2.5' : 'px-3 py-3 justify-center'">
                    <span class="material-symbols-outlined text-xl flex-shrink-0">dashboard</span>
                    <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Dashboard</span>
                </a>

                <a href="{{ url('/transactions') }}"
                    class="flex items-center gap-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition overflow-hidden"
                    :class="sidebarOpen ? 'px-3 py-2.5' : 'px-3 py-3 justify-center'">
                    <span class="material-symbols-outlined text-xl flex-shrink-0">price_check</span>
                    <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Transactions</span>
                </a>
            </div>
        </div>

        {{-- Data Master Section --}}
        <div>
            <div class="mb-3 overflow-hidden" :class="sidebarOpen ? 'px-3' : 'px-0'">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider whitespace-nowrap transition-all duration-300"
                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Data Master</h3>
            </div>
            <div class="space-y-1">
                <a href="{{ url('/va') }}"
                    class="flex items-center gap-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition overflow-hidden"
                    :class="sidebarOpen ? 'px-3 py-2.5' : 'px-3 py-3 justify-center'">
                    <span class="material-symbols-outlined text-xl flex-shrink-0">credit_score</span>
                    <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Virtual Account</span>
                </a>

                <a href="{{ url('/settings') }}"
                    class="flex items-center gap-3 rounded-lg text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition overflow-hidden"
                    :class="sidebarOpen ? 'px-3 py-2.5' : 'px-3 py-3 justify-center'">
                    <span class="material-symbols-outlined text-xl flex-shrink-0">settings</span>
                    <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Settings</span>
                </a>
            </div>
        </div>
    </nav>

    {{-- user area --}}
    {{-- <div class="absolute bottom-0 left-0 right-0 border-t border-gray-100 p-4 bg-white">
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" type="button" class="flex items-center gap-3 w-full text-sm text-gray-700 overflow-hidden" :class="sidebarOpen ? '' : 'justify-center'">
                <img src="https://ui-avatars.com/api/?name=Dhika+Hakim&background=2563eb&color=fff"
                    class="w-9 h-9 rounded-full flex-shrink-0">
                <div class="flex-1 text-left overflow-hidden transition-all duration-300" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">
                    <p class="font-semibold text-sm text-gray-800 whitespace-nowrap">Dhika Hakim</p>
                    <p class="text-xs text-gray-500 whitespace-nowrap">Administrator</p>
                </div>
                <span class="material-symbols-outlined text-gray-400 text-lg transition-all duration-300 flex-shrink-0" :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">expand_more</span>
            </button>

            <div x-show="open" x-transition @click.outside="open = false"
                class="absolute bottom-12 left-4 w-48 bg-white border border-gray-200 rounded-lg shadow-lg py-2 z-50">
                <a href="{{ url('/profile/reset-password') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reset Password</a>
                <a href="{{ url('/logout') }}" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div> --}}
</aside>
