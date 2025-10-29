<aside
    class="fixed inset-y-0 left-0 bg-white border-r border-gray-200 z-30 transition-all duration-300 ease-in-out shadow-sm"
    :class="sidebarOpen ? 'w-64' : 'w-20'">

    {{-- Header / Logo --}}
    <div class="h-16 flex items-center border-b border-gray-100 transition-all duration-300"
        :class="sidebarOpen ? 'px-6 justify-between' : 'px-3 justify-center'">
        <div class="flex items-center gap-3 overflow-hidden">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                <span class="material-symbols-outlined text-white text-xl">account_balance</span>
            </div>
            <h1 class="text-base font-semibold text-gray-800 tracking-tight whitespace-nowrap transition-all duration-300"
                :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Espay | IT PBA</h1>
        </div>
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 hover:text-blue-600 transition">
            <span class="material-symbols-outlined text-xl">menu_open</span>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="mt-8 px-3 overflow-y-auto" style="max-height: calc(100vh - 70px);">

        {{-- === MAIN MENU === --}}
        <div class="mb-7">
            <h3 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-4"
                :class="sidebarOpen ? 'opacity-100 ml-1' : 'opacity-0 w-0'">
                Main Menu
            </h3>

            <ul class="space-y-3">
                <li>
                    <a href="{{ url('/') }}"
                        class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                                                                                                                                                                                            sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                                                                                                                                                                                            request()->is('/') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                                                                                                                                                                                        ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">dashboard</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Dashboard</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- === DATA MASTER === --}}
        <div class="mt-7 mb-7">
            <h3 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-4"
                :class="sidebarOpen ? 'opacity-100 ml-1' : 'opacity-0 w-0'">
                Data Master
            </h3>

            <ul class="space-y-3">
                <li>
                    <a href="{{ url('/va') }}"
                        class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                    sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                    request()->is('va*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">credit_score</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Virtual Account</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                                                                                                                                                                                            sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                                                                                                                                                                                            request()->is('va*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                                                                                                                                                                                        ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">wallet</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Tabungan</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/transactions') }}"
                        class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                                                                                                                                                                                            sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                                                                                                                                                                                            request()->is('transactions*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                                                                                                                                                                                        ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">price_check</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Transactions</span>
                    </a>
                </li>
            </ul>
        </div>

        {{-- === DATA LOG === --}}
        <div class="mt-7">
            <h3 class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-4"
                :class="sidebarOpen ? 'opacity-100 ml-1' : 'opacity-0 w-0'">
                Data Log
            </h3>

            <ul class="space-y-3">
                <li>
                    <a href="{{ url('/activities-log') }}"
                        class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                                                                                                                                                                                            sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                                                                                                                                                                                            request()->is('activities-log*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                                                                                                                                                                                        ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">search_activity</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Create VA Log</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/activities-auth') }}"
                        class="flex items-center gap-3 rounded-lg transition overflow-hidden group"
                        :class="[
                                                                                                                                                                                                                            sidebarOpen ? 'px-4 py-3' : 'px-3 py-3 justify-center',
                                                                                                                                                                                                                            request()->is('activities-auth*') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600'
                                                                                                                                                                                                                        ]">
                        <span
                            class="material-symbols-outlined text-xl flex-shrink-0 group-hover:scale-110 transition">monitor_heart</span>
                        <span class="text-sm font-medium whitespace-nowrap transition-all duration-300"
                            :class="sidebarOpen ? 'opacity-100' : 'opacity-0 w-0'">Auth Log</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>
