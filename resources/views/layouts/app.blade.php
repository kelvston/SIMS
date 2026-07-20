<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'PhoneStore Pro')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
        }

        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: -100%;
            height: 100%;
            width: min(20rem, 85vw);
            background-color: #AD5D29;
            transition: left 0.3s ease;
            z-index: 50;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .sidebar-mobile.active {
            left: 0;
        }

        [x-cloak] {
            display: none !important;
        }

        .notification-dot {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background-color: #ef4444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.65rem;
            font-weight: bold;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.7; }
            100% { transform: scale(1); opacity: 1; }
        }

        .chart-wrapper {
            height: 320px;
        }
        @keyframes loader-bar {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(2); }
        }

        .animate-loader-bar {
            animation: loader-bar 1s infinite ease-in-out;
        }

        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        #page-loader {
            transition: opacity 0.3s ease;
        }
        #page-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }


    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans">
<!-- Page Loader -->
<div id="page-loader"
     class="fixed inset-0 z-50 bg-white flex items-center justify-center">
    <div class="loader-bars flex space-x-1">
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-100"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-200"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-300"></div>
        <div class="w-2 h-6 bg-indigo-600 animate-loader-bar delay-400"></div>
    </div>
</div>

<div class="flex min-h-screen overflow-hidden">
    <!-- Sidebar for desktop -->
    <aside class="fixed inset-y-0 left-0 w-60 bg-[#AD5D29] text-white p-4 hidden lg:flex flex-col z-40" x-data="{ reportsOpen: false, manageOpen: false }">
        <h2 class="text-xl font-bold mb-6 shrink-0">PhoneStore Pro</h2>
        <nav class="space-y-2 overflow-y-auto pr-1">
            @can('view dashboard')
                <a href="{{ route('dashboard') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">📊</span> Dashboard
                </a>
            @endcan
            @can('view phones')
                <a href="{{ route('phones.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">📱</span> Inventory
                </a>
            @endcan
            @can('view sales')
                <a href="{{ route('sales.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">💰</span> Sales
                </a>
            @endcan
            @can('view installments')
                <a href="{{ route('installments.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">🗓️</span> Installments
                </a>
            @endcan
            @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])

                    <button type="button" @click="reportsOpen = !reportsOpen"
                            class="w-full text-left py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center transition"
                            :aria-expanded="reportsOpen.toString()" aria-controls="reports-menu">
                        <span><span class="mr-2">⚙️</span> Reports</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"
                             :class="{'rotate-180': reportsOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                             stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div id="reports-menu" x-cloak x-show="reportsOpen" x-transition.opacity.duration.150ms class="pl-4 space-y-1 mt-1 overflow-hidden">
                        @can('view sales reports')
                            <a href="{{ route('reports.sales') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                                <span class="mr-2">👥</span> Sales Report
                            </a>
                        @endcan
                        @can('view stock reports')
                            <a href="{{ route('reports.stock') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                                <span class="mr-2">🔐</span> Stock Report
                            </a>
                        @endcan
                        @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])
                            <a href="{{ route('reports.general') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                                <span class="mr-2">👥</span> General Report
                            </a>
                        @endcanany
                        @can('view profit loss reports')
                            <a href="{{ route('reports.profit_loss') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                                <span class="mr-2">🏷️</span> Profit/loss
                            </a>
                        @endcan
                    </div>
            @endcanany
            @canany(['manage users', 'manage roles', 'manage brands'])
                <button type="button" @click="manageOpen = !manageOpen"
                        class="w-full text-left py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center transition"
                        :aria-expanded="manageOpen.toString()" aria-controls="manage-menu">
                    <span><span class="mr-2">⚙️</span> Manage</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"
                         :class="{'rotate-180': manageOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="manage-menu" x-cloak x-show="manageOpen" x-transition.opacity.duration.150ms class="pl-4 space-y-1 mt-1 overflow-hidden">
                    @can('manage users')
                        <a href="{{ route('users.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                            <span class="mr-2">👥</span> Manage Users
                        </a>
                    @endcan
                    @can('manage roles')
                        <a href="{{ route('roles.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                            <span class="mr-2">🔐</span> Manage Roles
                        </a>
                    @endcan
                    @can('manage brands')
                        <a href="{{ route('brands.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                            <span class="mr-2">🏷️</span> Manage Brands
                        </a>
                    @endcan
                </div>
            @endcanany
            @can('view expenses')
                <a href="{{ route('expenses.index') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">💸</span> Expenses
                </a>
            @endcan
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="flex items-center w-full text-left py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">🚪</span> Log Out
                </button>
            </form>
                @php($currentUser = Auth::user())
                <div class="mt-auto flex items-center gap-3 px-3 py-2 border-t border-white/30">
                    <div class="w-10 h-10 rounded-full bg-[#C87137] text-white flex items-center justify-center font-bold">
                        {{ strtoupper(mb_substr($currentUser?->name ?? 'User', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-semibold text-sm leading-none">{{ $currentUser?->name ?? 'User' }}</div>
                        <div class="text-xs text-white/70">{{ $currentUser?->email ?? '' }}</div>
                    </div>
                </div>
        </nav>
    </aside>

    <!-- Sidebar for mobile -->
    <div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/40 lg:hidden"></div>

    <aside id="mobileSidebar" class="sidebar-mobile text-white p-4 lg:hidden" x-data="{ reportsOpen: false, manageOpen: false }">
        <h2 class="text-xl font-bold mb-6">PhoneStore Pro</h2>
        <nav class="space-y-2">
            @can('view dashboard')
                <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Dashboard</a>
            @endcan
            @can('view phones')
                <a href="{{ route('phones.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Inventory</a>
            @endcan
            @can('view sales')
                <a href="{{ route('sales.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Sales</a>
            @endcan
            @can('view installments')
                <a href="{{ route('installments.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Installments</a>
            @endcan
            @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])
                <button type="button" @click="reportsOpen = !reportsOpen"
                        class="w-full py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center">
                    <span>Reports</span>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': reportsOpen }"></i>
                </button>
                <div x-cloak x-show="reportsOpen" x-transition.opacity.duration.150ms class="pl-4 space-y-1">
                    @can('view sales reports')
                        <a href="{{ route('reports.sales') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Sales Report</a>
                    @endcan
                    @can('view stock reports')
                        <a href="{{ route('reports.stock') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Stock Report</a>
                    @endcan
                    @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])
                        <a href="{{ route('reports.general') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">General Report</a>
                    @endcanany
                    @can('view profit loss reports')
                        <a href="{{ route('reports.profit_loss') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Profit/loss</a>
                    @endcan
                </div>
            @endcanany
            @canany(['manage users', 'manage roles', 'manage brands'])
                <button type="button" @click="manageOpen = !manageOpen"
                        class="w-full py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center">
                    <span>Manage</span>
                    <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': manageOpen }"></i>
                </button>
                <div x-cloak x-show="manageOpen" x-transition.opacity.duration.150ms class="pl-4 space-y-1">
                    @can('manage users')
                        <a href="{{ route('users.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Manage Users</a>
                    @endcan
                    @can('manage roles')
                        <a href="{{ route('roles.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Manage Roles</a>
                    @endcan
                    @can('manage brands')
                        <a href="{{ route('brands.index') }}" class="block py-2 px-3 rounded hover:bg-[#C87137]">Manage Brands</a>
                    @endcan
                </div>
            @endcanany
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-3 rounded hover:bg-[#C87137]">Log Out</button>
            </form>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col lg:ml-60">
        <!-- Top bar -->
        <header class="sticky top-0 z-30 flex justify-between items-center bg-white px-4 py-3 shadow lg:hidden">
            <button id="menuToggle" type="button" class="text-gray-600 p-2 -ml-2 rounded hover:bg-gray-100" aria-label="Open navigation" aria-controls="mobileSidebar" aria-expanded="false">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <h1 class="text-base sm:text-xl font-bold text-gray-800 truncate px-2">@yield('title', 'Dashboard')</h1>
            <div class="relative">
                <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-bell fa-lg"></i>
                </button>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-3 sm:p-5 lg:p-6 space-y-6 lg:space-y-8 overflow-y-auto overflow-x-hidden relative">
            <!-- All main content stays above watermark -->
            <div class="relative z-10 space-y-8">
{{--                <!-- Top header section -->--}}
{{--                <div class="hidden lg:flex justify-between items-center">--}}
{{--                    <div class="relative">--}}
{{--                        <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">--}}
{{--                            <i class="fas fa-bell fa-lg"></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                </div>--}}

                @yield('content')
            </div>
        </main>


    </div>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileSidebarOverlay = document.getElementById('mobileSidebarOverlay');

    function setMobileSidebar(open) {
        if (!menuToggle || !mobileSidebar || !mobileSidebarOverlay) {
            return;
        }

        mobileSidebar.classList.toggle('active', open);
        mobileSidebarOverlay.classList.toggle('hidden', !open);
        menuToggle.setAttribute('aria-expanded', open.toString());
        document.body.classList.toggle('overflow-hidden', open);
    }

    menuToggle?.addEventListener('click', () => {
        setMobileSidebar(!mobileSidebar.classList.contains('active'));
    });

    mobileSidebarOverlay?.addEventListener('click', () => setMobileSidebar(false));

    mobileSidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setMobileSidebar(false));
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setMobileSidebar(false);
        }
    });

    window.addEventListener('load', () => {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    });
</script>
<script src="{{ asset('vendor/chart.js/chart.umd.min.js') }}"></script>
@stack('scripts')
</body>
</html>
