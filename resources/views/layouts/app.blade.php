<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'PhoneStore Pro')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .sidebar-mobile {
            position: fixed;
            top: 0;
            left: -100%;
            height: 100%;
            width: 240px;
            background-color: #1f2937;
            transition: left 0.3s ease;
            z-index: 50;
            overflow: hidden;
        }

        .sidebar-mobile.active {
            left: 0;
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
    </style>
</head>
<body class="bg-gray-100 font-sans">
<div class="flex min-h-screen overflow-hidden">
    <!-- Sidebar for desktop -->
    <aside class="fixed inset-y-0 left-0 w-60 bg-[#AD5D29] text-white p-4 hidden lg:flex flex-col z-40 overflow-hidden" x-data="{ manageOpen: false }">
        <h2 class="text-xl font-bold mb-6">PhoneStore Pro</h2>
        <nav class="space-y-2 overflow-hidden">
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
                <a href="{{ route('reports.sales') }}" class="flex items-center py-2 px-3 rounded hover:bg-[#C87137] transition">
                    <span class="mr-2">📈</span> Reports
                </a>
            @endcanany
            @canany(['manage users', 'manage roles', 'manage brands'])
                <button @click="manageOpen = !manageOpen"
                        class="w-full text-left py-2 px-3 rounded hover:bg-[#C87137] flex justify-between items-center transition"
                        :aria-expanded="manageOpen.toString()" aria-controls="manage-menu">
                    <span><span class="mr-2">⚙️</span> Manage</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform duration-300"
                         :class="{'rotate-180': manageOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                         stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="manage-menu" x-show="manageOpen" x-collapse class="pl-4 space-y-1 mt-1 overflow-hidden">
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
                <div class="mt-auto flex items-center gap-3 px-3 py-2 border-t border-white/30">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=AD5D29&color=fff&size=40" alt="Avatar" class="w-10 h-10 rounded-full" />
                    <div>
                        <div class="font-semibold text-sm leading-none">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-white/70">{{ Auth::user()->email }}</div>
                    </div>
                </div>
        </nav>
    </aside>

    <!-- Sidebar for mobile -->
    <aside id="mobileSidebar" class="sidebar-mobile text-white p-4 lg:hidden overflow-hidden">
        <h2 class="text-xl font-bold mb-6">PhoneStore Pro</h2>
        <nav class="space-y-2 overflow-hidden">
            @can('view dashboard')
                <a href="{{ route('dashboard') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Dashboard</a>
            @endcan
            @can('view phones')
                <a href="{{ route('phones.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Inventory</a>
            @endcan
            @can('view sales')
                <a href="{{ route('sales.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Sales</a>
            @endcan
            @can('view installments')
                <a href="{{ route('installments.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Installments</a>
            @endcan
            @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])
                <a href="{{ route('reports.sales') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Reports</a>
            @endcanany
            @can('manage users')
                <a href="{{ route('users.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Manage Users</a>
            @endcan
            @can('manage roles')
                <a href="{{ route('roles.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Manage Roles</a>
            @endcan
            @can('manage brands')
                <a href="{{ route('brands.index') }}" class="block py-2 px-3 rounded hover:bg-gray-700">Manage Brands</a>
            @endcan
            <form method="POST" action="{{ route('logout') }}" class="block">
                @csrf
                <button type="submit" class="w-full text-left py-2 px-3 rounded hover:bg-gray-700">Log Out</button>
            </form>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 flex flex-col lg:ml-60">
        <!-- Top bar -->
        <header class="flex justify-between items-center bg-white px-6 py-4 shadow lg:hidden">
            <button id="menuToggle" class="text-gray-600">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <h1 class="text-xl font-bold text-gray-800">@yield('title', 'Dashboard')</h1>
            <div class="relative">
                <button class="p-2 text-gray-600 hover:bg-gray-100 rounded-full">
                    <i class="fas fa-bell fa-lg"></i>
                </button>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 p-6 space-y-8 overflow-y-auto relative">
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

<script src="//unpkg.com/alpinejs" defer></script>
<script>
    document.getElementById('menuToggle').addEventListener('click', () => {
        document.getElementById('mobileSidebar').classList.toggle('active');
    });
</script>
@stack('scripts')
</body>
</html>
