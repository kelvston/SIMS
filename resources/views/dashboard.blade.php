{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    <style>--}}
{{--        .hexagon-shape {--}}
{{--            clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);--}}
{{--            transition: all 0.3s ease-in-out;--}}
{{--            height: 90px;--}}
{{--            font-size: 15px;--}}
{{--        }--}}

{{--        .hexagon-shape:hover {--}}
{{--            transform: scale(1.23);--}}
{{--        }--}}

{{--        .arrow-curve {--}}
{{--            position: absolute;--}}
{{--            z-index: 0;--}}
{{--            pointer-events: none;--}}
{{--        }--}}

{{--        .arrow-right {--}}
{{--            top: 25px;--}}
{{--            left: 32%;--}}
{{--        }--}}

{{--        .arrow-down {--}}
{{--            top: 80px;--}}
{{--            left: 66%;--}}
{{--        }--}}
{{--    </style>--}}

{{--    <!-- Watermark -->--}}

{{--	@if(isset($settings['organization_logo_path']))--}}
{{--    <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}"--}}
{{--         alt="Watermark"--}}
{{--         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"--}}
{{--         style="transform: translate(-50%, -90%);" />--}}
{{--@endif--}}





{{--    <!-- Hexagon Buttons and Arrows Wrapper -->--}}
{{--    <!-- Hexagon Buttons and Arrows Wrapper -->--}}
{{--    <div class="relative">--}}
{{--        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 relative z-10">--}}
{{--            @can('receive cashews')--}}
{{--                <a href="{{ route('cashew.receive.form') }}"--}}
{{--                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-1 transition duration-200 mt-6">--}}
{{--                    <i class="fas fa-download text-[14px]"></i> Receive--}}
{{--                </a>--}}
{{--            @endcan--}}


{{--        @can('create sales')--}}
{{--                <a href="{{ route('sales.create') }}"--}}
{{--                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-green-600 hover:bg-green-700 text-white py-2 px-1 transition duration-200">--}}
{{--                    <i class="fas fa-dollar-sign text-[14px]"></i> Sale--}}
{{--                </a>--}}
{{--            @endcan--}}

{{--            @can('create expenses')--}}
{{--                <div class="flex items-center">--}}
{{--                    <!-- Expense Button -->--}}
{{--                    <a href="{{ route('expenses.create') }}"--}}
{{--                       class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-red-500 hover:bg-red-600 text-white py-2 px-1 transition duration-200">--}}
{{--                        <i class="fas fa-receipt text-[14px]"></i> Expense--}}
{{--                    </a>--}}

{{--                    <!-- Vertical Line -->--}}
{{--                    <div class="border-l border-gray-400 h-14 mx-3"></div>--}}
{{--                </div>--}}
{{--            @endcan--}}

{{--            <!-- Summary block -->--}}
{{--            <div class="lg:col-span-1 p-3 bg-white rounded-md shadow-sm border border-gray-200">--}}
{{--                <h2 class="text-xs font-bold mb-2 text-gray-800 flex items-center gap-1">--}}
{{--                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"--}}
{{--                         viewBox="0 0 24 24">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />--}}
{{--                    </svg>--}}
{{--                    SUMMARY--}}
{{--                </h2>--}}
{{--                <table class="text-xs w-full text-left">--}}
{{--                    <tr class="font-semibold text-gray-700">--}}
{{--                        <th colspan="2" class="pb-1">KEY METRICS (Current Month)</th>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Total Products:</td>--}}
{{--                        <td><b>{{ $product_count }}</b></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Units in Stock:</td>--}}
{{--                        <td><b>{{ number_format($availableStockUnits) }}</b></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Monthly Sales:</td>--}}
{{--                        <td><b>Tsh {{ number_format($monthlySales, 2) }}</b></td>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <td>Pending Installments:</td>--}}
{{--                        <td><b>Tsh {{ number_format($pendingInstallmentsAmount, 2) }}</b></td>--}}
{{--                    </tr>--}}
{{--                </table>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}


{{--    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">--}}
{{--        @php--}}
{{--            $cards = [--}}
{{--                ['icon' => '📦', 'label' => 'Products', 'value' => number_format($product_count), 'color' => 'indigo'],--}}
{{--                ['icon' => '📊', 'label' => 'Stock Units', 'value' => number_format($availableStockUnits), 'color' => 'blue'],--}}
{{--                ['icon' => '💰', 'label' => 'Sales (' . \Carbon\Carbon::now()->format('M') . ')', 'value' => 'Tsh ' . number_format($monthlySales, 2), 'color' => 'green'],--}}
{{--                ['icon' => '🏦', 'label' => 'Inventory Value', 'value' => 'Tsh ' . number_format($inventoryValue, 2), 'color' => 'cyan'],--}}
{{--                ['icon' => '💸', 'label' => 'Expenses', 'value' => 'Tsh ' . number_format($monthlyExpenses, 2), 'color' => 'red'],--}}
{{--                ['icon' => '📈', 'label' => 'Net Profit', 'value' => 'Tsh ' . number_format($netProfit, 2), 'color' => $netProfit >= 0 ? 'green' : 'red'],--}}
{{--                ['icon' => '⏳', 'label' => 'Pending', 'value' => 'Tsh ' . number_format($pendingInstallmentsAmount, 2), 'color' => 'yellow'],--}}
{{--                ['icon' => '📈', 'label' => 'Profit', 'value' => number_format($profitMarginPercentage, 2) . '%', 'color' => $profitMarginPercentage >= 0 ? 'green' : 'red'],--}}
{{--            ];--}}
{{--        @endphp--}}

{{--        @foreach($cards as $card)--}}
{{--            <div class="p-2 bg-gradient-to-br from-{{ $card['color'] }}-50 to-white rounded-lg border border-{{ $card['color'] }}-200 shadow-sm hover:shadow-md transition duration-200 transform hover:-translate-y-0.5">--}}
{{--                <div class="flex items-center gap-1 mb-0.5 text-{{ $card['color'] }}-600 text-xs">--}}
{{--                    <span class="text-base">{{ $card['icon'] }}</span>--}}
{{--                    <span class="font-semibold uppercase tracking-wide truncate">{{ $card['label'] }}</span>--}}
{{--                </div>--}}
{{--                <p class="text-lg font-bold text-gray-800">{{ $card['value'] }}</p>--}}
{{--            </div>--}}
{{--        @endforeach--}}
{{--    </div>--}}

{{--    <!-- Charts -->--}}
{{--    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">--}}
{{--        <!-- Sales Chart -->--}}
{{--        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">--}}
{{--            <h2 class="text-xs font-semibold mb-1 text-gray-800">Sales (30 Days)</h2>--}}
{{--            <div class="h-48 overflow-hidden">--}}
{{--                <canvas id="salesChart"></canvas>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        <!-- Inventory Chart -->--}}
{{--        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">--}}
{{--            <h2 class="text-xs font-semibold mb-1 text-gray-800">Inventory by Product</h2>--}}
{{--            <div class="h-48 overflow-hidden">--}}
{{--                <canvas id="inventoryChart"></canvas>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}



{{--    <!-- Recent Activity -->--}}
{{--    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">--}}

{{--    <!-- Low Stock Table -->--}}
{{--    @can('view stock reports')--}}
{{--        <div class="p-2 bg-white rounded-lg shadow overflow-x-auto">--}}
{{--            <div class="flex items-center justify-between mb-4">--}}
{{--                <h2 class="font-semibold">Low Stock Products Overview</h2>--}}
{{--                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">{{ $notificationCount }} alerts</span>--}}
{{--            </div>--}}
{{--            @if ($lowStockProducts->isEmpty())--}}
{{--                <p class="text-center text-gray-600">No products are currently low in stock.</p>--}}
{{--            @else--}}
{{--                <table class="min-w-full text-sm">--}}
{{--                    <thead class="bg-gray-100">--}}
{{--                    <tr>--}}
{{--                        <th class="text-left p-2">Item</th>--}}
{{--                        <th class="text-left p-2">Stock</th>--}}
{{--                        <th class="text-left p-2">Threshold</th>--}}
{{--                        <th class="text-left p-2">Status</th>--}}
{{--                    </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                    @foreach ($lowStockProducts as $item)--}}
{{--                        <tr>--}}
{{--                            <td class="p-2">{{ optional($item->product)->name ?? 'Unknown product' }}</td>--}}
{{--                            <td class="p-2">{{ number_format($item->quantity) }} units</td>--}}
{{--                            <td class="p-2">{{ number_format($item->low_stock_threshold) }} units</td>--}}
{{--                            <td class="p-2">--}}
{{--                                <span class="text-red-700 bg-red-100 px-2 py-1 rounded-full text-xs font-semibold">Low</span>--}}
{{--                            </td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    @endcan--}}

{{--        <div class="p-2 bg-white rounded-lg shadow overflow-x-auto">--}}
{{--            <h2 class="font-semibold mb-4">Recent Activity</h2>--}}
{{--            @if ($recentActivities->isEmpty())--}}
{{--                <p class="text-center text-gray-600">No recent activity found.</p>--}}
{{--            @else--}}
{{--                <div class="space-y-2">--}}
{{--                    @foreach ($recentActivities as $activity)--}}
{{--                        <a href="{{ $activity['link'] }}" class="block border border-gray-100 rounded-md p-2 hover:bg-gray-50 transition">--}}
{{--                            <div class="flex items-start justify-between gap-3">--}}
{{--                                <div>--}}
{{--                                    <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>--}}
{{--                                    <p class="text-xs text-gray-500">{{ ucfirst($activity['type']) }}</p>--}}
{{--                                </div>--}}
{{--                                <span class="text-xs text-gray-500 whitespace-nowrap">--}}
{{--                                    {{ \Carbon\Carbon::parse($activity['date'])->format('d M H:i') }}--}}
{{--                                </span>--}}
{{--                            </div>--}}
{{--                        </a>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--@endsection--}}

{{--@push('scripts')--}}
{{--    <script>--}}
{{--        // Chart Data from Laravel Controller--}}
{{--        const salesChartLabels = @json($salesChartLabels);--}}
{{--        const salesChartData = @json($salesChartData);--}}
{{--        const inventoryChartLabels = @json($inventoryChartLabels);--}}
{{--        const inventoryChartData = @json($inventoryChartData);--}}

{{--        new Chart(document.getElementById('salesChart'), {--}}
{{--            type: 'line',--}}
{{--            data: {--}}
{{--                labels: salesChartLabels,--}}
{{--                datasets: [{--}}
{{--                    label: 'Sales (Tsh)',--}}
{{--                    data: salesChartData,--}}
{{--                    borderColor: '#4f46e5',--}}
{{--                    backgroundColor: 'rgba(79, 70, 229, 0.1)',--}}
{{--                    borderWidth: 3,--}}
{{--                    fill: true--}}
{{--                }]--}}
{{--            },--}}
{{--            options: {--}}
{{--                responsive: true,--}}
{{--                maintainAspectRatio: false,--}}
{{--                scales: {--}}
{{--                    y: {--}}
{{--                        beginAtZero: true,--}}
{{--                        title: {--}}
{{--                            display: true,--}}
{{--                            text: 'Sales Amount'--}}
{{--                        }--}}
{{--                    },--}}
{{--                    x: {--}}
{{--                        title: {--}}
{{--                            display: true,--}}
{{--                            text: 'Date'--}}
{{--                        }--}}
{{--                    }--}}
{{--                },--}}
{{--                plugins: {--}}
{{--                    tooltip: {--}}
{{--                        callbacks: {--}}
{{--                            label: function(context) {--}}
{{--                                return context.dataset.label + ': Tsh ' + context.parsed.y.toFixed(2);--}}
{{--                            }--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        new Chart(document.getElementById('inventoryChart'), {--}}
{{--            type: 'doughnut',--}}
{{--            data: {--}}
{{--                labels: inventoryChartLabels,--}}
{{--                datasets: [{--}}
{{--                    data: inventoryChartData,--}}
{{--                    backgroundColor: [--}}
{{--                        '#4f46e5', // Indigo--}}
{{--                        '#10b981', // Green--}}
{{--                        '#f59e0b', // Amber--}}
{{--                        '#ef4444', // Red--}}
{{--                        '#8b5cf6', // Purple--}}
{{--                        '#06b6d4', // Cyan--}}
{{--                        '#f97316', // Orange--}}
{{--                        '#6b7280', // Gray--}}
{{--                        '#ec4899', // Pink--}}
{{--                        '#3b82f6'  // Blue--}}
{{--                    ]--}}
{{--                }]--}}
{{--            },--}}
{{--            options: {--}}
{{--                responsive: true,--}}
{{--                maintainAspectRatio: false,--}}
{{--                cutout: '70%',--}}
{{--                plugins: {--}}
{{--                    legend: {--}}
{{--                        position: 'right'--}}
{{--                    },--}}
{{--                    tooltip: {--}}
{{--                        callbacks: {--}}
{{--                            label: function(context) {--}}
{{--                                let label = context.label || '';--}}
{{--                                if (label) {--}}
{{--                                    label += ': ';--}}
{{--                                }--}}
{{--                                if (context.parsed !== null) {--}}
{{--                                    label += context.parsed + ' units';--}}
{{--                                }--}}
{{--                                return label;--}}
{{--                            }--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}
{{--    </script>--}}
{{--@endpush--}}

@extends('layouts.app')

@section('content')
    <style>
        /* =========================================================
           PREMIUM CASHEW DASHBOARD
        ========================================================= */

        :root {
            --dash-bg: #f5f7fb;
            --glass: rgba(255, 255, 255, 0.78);
            --glass-border: rgba(255, 255, 255, 0.9);
            --dark: #172033;
            --muted: #718096;
            --gold: #c9962d;
            --gold-light: #f4d58a;
            --shadow:
                0 20px 50px rgba(15, 23, 42, .08),
                0 5px 15px rgba(15, 23, 42, .04);
        }

        .premium-dashboard {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 1rem;
            background:
                radial-gradient(circle at 5% 10%, rgba(201,150,45,.12), transparent 25%),
                radial-gradient(circle at 95% 15%, rgba(79,70,229,.10), transparent 25%),
                radial-gradient(circle at 50% 100%, rgba(16,185,129,.07), transparent 30%),
                var(--dash-bg);
        }

        /* Ambient background */
        .ambient-orb {
            position: absolute;
            border-radius: 999px;
            filter: blur(60px);
            pointer-events: none;
            opacity: .35;
        }

        .orb-one {
            width: 220px;
            height: 220px;
            background: rgba(201,150,45,.18);
            top: 5%;
            right: 8%;
        }

        .orb-two {
            width: 180px;
            height: 180px;
            background: rgba(79,70,229,.12);
            bottom: 20%;
            left: 2%;
        }

        /* Watermark */
        .dashboard-watermark {
            position: fixed;
            pointer-events: none;
            user-select: none;
            width: 420px;
            max-width: 55vw;
            opacity: .035;
            top: 50%;
            left: 50%;
            z-index: 0;
            transform: translate(-50%, -50%);
            filter: grayscale(1);
        }

        /* Header */
        .dashboard-header {
            position: relative;
            z-index: 5;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1rem;
            border-radius: 24px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(255,255,255,.9);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .header-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 21px;
            background:
                linear-gradient(145deg, #d9ad4f, #9c6e19);
            box-shadow:
                inset 2px 2px 4px rgba(255,255,255,.35),
                inset -3px -3px 6px rgba(0,0,0,.18),
                0 10px 25px rgba(156,110,25,.25);
        }

        .header-title h1 {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 900;
            letter-spacing: -.025em;
            color: var(--dark);
        }

        .header-title p {
            margin: 3px 0 0;
            font-size: .72rem;
            color: var(--muted);
            font-weight: 600;
        }

        .live-status {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 13px;
            border-radius: 999px;
            background: rgba(16,185,129,.08);
            border: 1px solid rgba(16,185,129,.15);
            color: #047857;
            font-size: .7rem;
            font-weight: 800;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 5px rgba(16,185,129,.12);
            animation: pulseLive 2s infinite;
        }

        @keyframes pulseLive {
            0%,100% { box-shadow: 0 0 0 4px rgba(16,185,129,.10); }
            50% { box-shadow: 0 0 0 8px rgba(16,185,129,.03); }
        }

        /* Quick actions */
        .quick-actions {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-bottom: 1rem;
        }

        .action-card {
            position: relative;
            min-height: 110px;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px;
            overflow: hidden;
            border-radius: 20px;
            text-decoration: none;
            color: white;
            box-shadow:
                0 15px 30px rgba(15,23,42,.12),
                inset 1px 1px 0 rgba(255,255,255,.28);
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .action-card::before {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            right: -30px;
            top: -50px;
            border-radius: 50%;
            background: rgba(255,255,255,.13);
        }

        .action-card::after {
            content: "";
            position: absolute;
            width: 70%;
            height: 1px;
            top: 0;
            left: 15%;
            background: rgba(255,255,255,.4);
        }

        .action-card:hover {
            transform: translateY(-5px) scale(1.015);
            box-shadow:
                0 25px 45px rgba(15,23,42,.18),
                inset 1px 1px 0 rgba(255,255,255,.35);
        }

        .action-receive {
            background: linear-gradient(135deg, #4f46e5, #312e81);
        }

        .action-sale {
            background: linear-gradient(135deg, #10b981, #047857);
        }

        .action-expense {
            background: linear-gradient(135deg, #ef4444, #991b1b);
        }

        .action-icon {
            width: 52px;
            height: 52px;
            flex: 0 0 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.24);
            box-shadow:
                inset 2px 2px 5px rgba(255,255,255,.18),
                inset -3px -3px 6px rgba(0,0,0,.12);
            font-size: 20px;
        }

        .action-card h3 {
            margin: 0;
            font-size: .92rem;
            font-weight: 900;
        }

        .action-card p {
            margin: 3px 0 0;
            font-size: .68rem;
            opacity: .8;
        }

        .action-arrow {
            margin-left: auto;
            opacity: .7;
            font-size: 12px;
        }

        /* Summary */
        .summary-card {
            position: relative;
            z-index: 5;
            padding: 18px;
            margin-bottom: 1rem;
            border-radius: 22px;
            background: var(--glass);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .section-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .section-heading-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-heading-icon {
            width: 34px;
            height: 34px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(79,70,229,.09);
            color: #4f46e5;
        }

        .section-heading h2 {
            margin: 0;
            font-size: .8rem;
            font-weight: 900;
            color: var(--dark);
            letter-spacing: .04em;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .summary-item {
            padding: 13px;
            border-radius: 15px;
            background: rgba(248,250,252,.85);
            border: 1px solid #edf0f5;
        }

        .summary-item span {
            display: block;
            color: #8a94a6;
            font-size: .62rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .summary-item strong {
            color: var(--dark);
            font-size: .82rem;
        }

        /* KPI cards */
        .kpi-grid {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 1rem;
        }

        .kpi-card {
            position: relative;
            min-height: 135px;
            padding: 18px;
            overflow: hidden;
            border-radius: 21px;
            background: rgba(255,255,255,.82);
            border: 1px solid rgba(255,255,255,.95);
            box-shadow: var(--shadow);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            box-shadow:
                0 25px 50px rgba(15,23,42,.12),
                0 5px 15px rgba(15,23,42,.05);
        }

        .kpi-card::after {
            content: "";
            position: absolute;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            right: -45px;
            bottom: -50px;
            background: var(--kpi-color);
            opacity: .08;
        }

        .kpi-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kpi-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: color-mix(in srgb, var(--kpi-color) 10%, white);
            color: var(--kpi-color);
            font-size: 18px;
        }

        .kpi-label {
            margin-top: 13px;
            color: #7b8495;
            font-size: .65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .055em;
        }

        .kpi-value {
            margin-top: 3px;
            color: var(--dark);
            font-size: 1.1rem;
            line-height: 1.25;
            font-weight: 900;
            letter-spacing: -.02em;
        }

        .kpi-line {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            width: 35%;
            background: var(--kpi-color);
            border-radius: 0 5px 0 0;
        }

        /* Charts */
        .charts-grid {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1.35fr 1fr;
            gap: 14px;
            margin-bottom: 1rem;
        }

        .chart-card {
            padding: 18px;
            min-height: 340px;
            border-radius: 22px;
            background: rgba(255,255,255,.84);
            border: 1px solid rgba(255,255,255,.95);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .chart-container {
            height: 260px;
            position: relative;
        }

        .chart-title {
            font-size: .78rem;
            font-weight: 900;
            color: var(--dark);
            margin: 0;
        }

        .chart-subtitle {
            font-size: .62rem;
            color: #98a1b2;
            margin-top: 3px;
        }

        /* Bottom panels */
        .bottom-grid {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .premium-panel {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255,255,255,.84);
            border: 1px solid rgba(255,255,255,.95);
            box-shadow: var(--shadow);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .alert-badge {
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(239,68,68,.08);
            color: #dc2626;
            font-size: .62rem;
            font-weight: 900;
        }

        .premium-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 6px;
            font-size: .72rem;
        }

        .premium-table th {
            padding: 7px 9px;
            color: #9aa3b2;
            text-align: left;
            font-size: .6rem;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .premium-table td {
            padding: 10px 9px;
            background: #f8fafc;
            border-top: 1px solid #edf0f4;
            border-bottom: 1px solid #edf0f4;
        }

        .premium-table td:first-child {
            border-left: 1px solid #edf0f4;
            border-radius: 10px 0 0 10px;
        }

        .premium-table td:last-child {
            border-right: 1px solid #edf0f4;
            border-radius: 0 10px 10px 0;
        }

        .status-low {
            display: inline-flex;
            padding: 5px 9px;
            border-radius: 999px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: .58rem;
            font-weight: 900;
        }

        /* Activity */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .activity-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px;
            border-radius: 13px;
            background: #f8fafc;
            border: 1px solid #edf0f4;
            text-decoration: none;
            transition: all .25s ease;
        }

        .activity-item:hover {
            background: white;
            transform: translateX(3px);
            box-shadow: 0 7px 18px rgba(15,23,42,.06);
        }

        .activity-icon {
            width: 34px;
            height: 34px;
            flex: 0 0 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: rgba(79,70,229,.08);
            color: #4f46e5;
            font-size: 12px;
        }

        .activity-description {
            flex: 1;
            min-width: 0;
        }

        .activity-description p {
            margin: 0;
            color: #344054;
            font-size: .7rem;
            font-weight: 700;
        }

        .activity-description span {
            display: block;
            margin-top: 2px;
            color: #98a1b2;
            font-size: .58rem;
        }

        .activity-date {
            color: #98a1b2;
            font-size: .58rem;
            white-space: nowrap;
        }

        /* Responsive */
        @media (max-width: 1100px) {
            .kpi-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 850px) {
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .charts-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .premium-dashboard {
                padding: .65rem;
            }

            .dashboard-header {
                padding: 1rem;
            }

            .header-title h1 {
                font-size: 1rem;
            }

            .live-status {
                display: none;
            }

            .kpi-grid,
            .summary-grid {
                grid-template-columns: 1fr 1fr;
                gap: 9px;
            }

            .kpi-card {
                min-height: 120px;
                padding: 13px;
            }

            .kpi-value {
                font-size: .9rem;
            }

            .chart-card,
            .premium-panel,
            .summary-card {
                padding: 13px;
            }

            .chart-container {
                height: 230px;
            }

            .premium-table {
                min-width: 520px;
            }
        }
    </style>

    <div class="premium-dashboard">

        <div class="ambient-orb orb-one"></div>
        <div class="ambient-orb orb-two"></div>

        {{-- Watermark --}}
        @if(isset($settings['organization_logo_path']))
            <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}"
                 alt="Watermark"
                 class="dashboard-watermark">
        @endif

        {{-- =====================================================
             HEADER
        ====================================================== --}}
        <div class="dashboard-header">

            <div class="header-title">
                <div class="header-icon">
                    <i class="fas fa-chart-line"></i>
                </div>

                <div>
                    <h1>Business Intelligence Dashboard</h1>
                    <p>Cashew Management • Inventory • Sales • Finance</p>
                </div>
            </div>

            <div class="live-status">
                <span class="live-dot"></span>
                SYSTEM LIVE
            </div>

        </div>


        {{-- =====================================================
             QUICK ACTIONS
        ====================================================== --}}
        <div class="quick-actions">

            @can('receive cashews')
                <a href="{{ route('cashew.receive.form') }}"
                   class="action-card action-receive">

                    <div class="action-icon">
                        <i class="fas fa-download"></i>
                    </div>

                    <div>
                        <h3>Receive Cashews</h3>
                        <p>Record incoming stock</p>
                    </div>

                    <i class="fas fa-arrow-right action-arrow"></i>
                </a>
            @endcan


            @can('create sales')
                <a href="{{ route('sales.create') }}"
                   class="action-card action-sale">

                    <div class="action-icon">
                        <i class="fas fa-cart-plus"></i>
                    </div>

                    <div>
                        <h3>Create Sale</h3>
                        <p>Process a new transaction</p>
                    </div>

                    <i class="fas fa-arrow-right action-arrow"></i>
                </a>
            @endcan


            @can('create expenses')
                <a href="{{ route('expenses.create') }}"
                   class="action-card action-expense">

                    <div class="action-icon">
                        <i class="fas fa-receipt"></i>
                    </div>

                    <div>
                        <h3>Record Expense</h3>
                        <p>Track business expenditure</p>
                    </div>

                    <i class="fas fa-arrow-right action-arrow"></i>
                </a>
            @endcan

        </div>


        {{-- =====================================================
             SUMMARY
        ====================================================== --}}
        <div class="summary-card">

            <div class="section-heading">

                <div class="section-heading-left">
                    <div class="section-heading-icon">
                        <i class="fas fa-layer-group"></i>
                    </div>

                    <div>
                        <h2>EXECUTIVE SUMMARY</h2>
                    </div>
                </div>

                <span style="font-size:.62rem;color:#98a1b2;font-weight:700;">
                {{ \Carbon\Carbon::now()->format('F Y') }}
            </span>

            </div>

            <div class="summary-grid">

                <div class="summary-item">
                    <span>Total Products</span>
                    <strong>{{ number_format($product_count) }}</strong>
                </div>

                <div class="summary-item">
                    <span>Units in Stock</span>
                    <strong>{{ number_format($availableStockUnits) }}</strong>
                </div>

                <div class="summary-item">
                    <span>Monthly Sales</span>
                    <strong>Tsh {{ number_format($monthlySales, 2) }}</strong>
                </div>

                <div class="summary-item">
                    <span>Pending Installments</span>
                    <strong>Tsh {{ number_format($pendingInstallmentsAmount, 2) }}</strong>
                </div>

            </div>
        </div>


        {{-- =====================================================
             KPI CARDS
        ====================================================== --}}
        @php
            $cards = [
                [
                    'icon' => '📦',
                    'label' => 'Products',
                    'value' => number_format($product_count),
                    'color' => '#4f46e5'
                ],
                [
                    'icon' => '📊',
                    'label' => 'Stock Units',
                    'value' => number_format($availableStockUnits),
                    'color' => '#2563eb'
                ],
                [
                    'icon' => '💰',
                    'label' => 'Sales (' . \Carbon\Carbon::now()->format('M') . ')',
                    'value' => 'Tsh ' . number_format($monthlySales, 2),
                    'color' => '#10b981'
                ],
                [
                    'icon' => '🏦',
                    'label' => 'Inventory Value',
                    'value' => 'Tsh ' . number_format($inventoryValue, 2),
                    'color' => '#0891b2'
                ],
                [
                    'icon' => '💸',
                    'label' => 'Expenses',
                    'value' => 'Tsh ' . number_format($monthlyExpenses, 2),
                    'color' => '#ef4444'
                ],
                [
                    'icon' => '📈',
                    'label' => 'Net Profit',
                    'value' => 'Tsh ' . number_format($netProfit, 2),
                    'color' => $netProfit >= 0 ? '#10b981' : '#ef4444'
                ],
                [
                    'icon' => '⏳',
                    'label' => 'Pending',
                    'value' => 'Tsh ' . number_format($pendingInstallmentsAmount, 2),
                    'color' => '#f59e0b'
                ],
                [
                    'icon' => '📈',
                    'label' => 'Profit Margin',
                    'value' => number_format($profitMarginPercentage, 2) . '%',
                    'color' => $profitMarginPercentage >= 0 ? '#10b981' : '#ef4444'
                ],
            ];
        @endphp


        <div class="kpi-grid">

            @foreach($cards as $card)

                <div class="kpi-card"
                     style="--kpi-color: {{ $card['color'] }};">

                    <div class="kpi-top">
                        <div class="kpi-icon">
                            {{ $card['icon'] }}
                        </div>

                        <i class="fas fa-ellipsis-h"
                           style="color:#c1c7d0;font-size:11px;"></i>
                    </div>

                    <div class="kpi-label">
                        {{ $card['label'] }}
                    </div>

                    <div class="kpi-value">
                        {{ $card['value'] }}
                    </div>

                    <div class="kpi-line"></div>

                </div>

            @endforeach

        </div>


        {{-- =====================================================
             CHARTS
        ====================================================== --}}
        <div class="charts-grid">

            {{-- Sales --}}
            <div class="chart-card">

                <div class="section-heading">

                    <div>
                        <h2 class="chart-title">
                            Sales Performance
                        </h2>

                        <div class="chart-subtitle">
                            Revenue movement over the last 30 days
                        </div>
                    </div>

                    <div class="section-heading-icon">
                        <i class="fas fa-chart-area"></i>
                    </div>

                </div>

                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>

            </div>


            {{-- Inventory --}}
            <div class="chart-card">

                <div class="section-heading">

                    <div>
                        <h2 class="chart-title">
                            Inventory Distribution
                        </h2>

                        <div class="chart-subtitle">
                            Current stock by product
                        </div>
                    </div>

                    <div class="section-heading-icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                </div>

                <div class="chart-container">
                    <canvas id="inventoryChart"></canvas>
                </div>

            </div>

        </div>


        {{-- =====================================================
             LOW STOCK + RECENT ACTIVITY
        ====================================================== --}}
        <div class="bottom-grid">

            {{-- Low Stock --}}
            @can('view stock reports')

                <div class="premium-panel">

                    <div class="section-heading">

                        <div class="section-heading-left">

                            <div class="section-heading-icon"
                                 style="background:rgba(239,68,68,.08);color:#dc2626;">
                                <i class="fas fa-triangle-exclamation"></i>
                            </div>

                            <div>
                                <h2>LOW STOCK MONITOR</h2>
                            </div>

                        </div>

                        <span class="alert-badge">
                        {{ $notificationCount }} alerts
                    </span>

                    </div>


                    @if ($lowStockProducts->isEmpty())

                        <div style="padding:35px 10px;text-align:center;">

                            <div style="
                            width:52px;
                            height:52px;
                            margin:0 auto 10px;
                            border-radius:16px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            background:#ecfdf5;
                            color:#10b981;
                        ">
                                <i class="fas fa-check"></i>
                            </div>

                            <p style="
                            margin:0;
                            color:#667085;
                            font-size:.72rem;
                            font-weight:700;
                        ">
                                Inventory levels are healthy.
                            </p>

                        </div>

                    @else

                        <div style="overflow-x:auto;">

                            <table class="premium-table">

                                <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Stock</th>
                                    <th>Threshold</th>
                                    <th>Status</th>
                                </tr>
                                </thead>

                                <tbody>

                                @foreach ($lowStockProducts as $item)

                                    <tr>

                                        <td>
                                            <strong style="color:#344054;">
                                                {{ optional($item->product)->name ?? 'Unknown product' }}
                                            </strong>
                                        </td>

                                        <td>
                                            {{ number_format($item->quantity) }}
                                        </td>

                                        <td>
                                            {{ number_format($item->low_stock_threshold) }}
                                        </td>

                                        <td>
                                        <span class="status-low">
                                            LOW
                                        </span>
                                        </td>

                                    </tr>

                                @endforeach

                                </tbody>

                            </table>

                        </div>

                    @endif

                </div>

            @endcan


            {{-- Recent Activity --}}
            <div class="premium-panel">

                <div class="section-heading">

                    <div class="section-heading-left">

                        <div class="section-heading-icon">
                            <i class="fas fa-clock-rotate-left"></i>
                        </div>

                        <div>
                            <h2>RECENT ACTIVITY</h2>
                        </div>

                    </div>

                    <span style="
                    font-size:.6rem;
                    color:#98a1b2;
                    font-weight:800;
                ">
                    LIVE FEED
                </span>

                </div>


                @if ($recentActivities->isEmpty())

                    <div style="padding:35px 10px;text-align:center;">

                        <div style="
                        width:52px;
                        height:52px;
                        margin:0 auto 10px;
                        border-radius:16px;
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        background:#f1f5f9;
                        color:#94a3b8;
                    ">
                            <i class="fas fa-inbox"></i>
                        </div>

                        <p style="
                        margin:0;
                        color:#667085;
                        font-size:.72rem;
                        font-weight:700;
                    ">
                            No recent activity found.
                        </p>

                    </div>

                @else

                    <div class="activity-list">

                        @foreach ($recentActivities as $activity)

                            <a href="{{ $activity['link'] }}"
                               class="activity-item">

                                <div class="activity-icon">

                                    @if(str_contains(strtolower($activity['type']), 'sale'))
                                        <i class="fas fa-cart-shopping"></i>
                                    @elseif(str_contains(strtolower($activity['type']), 'expense'))
                                        <i class="fas fa-receipt"></i>
                                    @elseif(str_contains(strtolower($activity['type']), 'stock'))
                                        <i class="fas fa-box"></i>
                                    @else
                                        <i class="fas fa-bolt"></i>
                                    @endif

                                </div>

                                <div class="activity-description">

                                    <p>
                                        {{ $activity['description'] }}
                                    </p>

                                    <span>
                                    {{ ucfirst($activity['type']) }}
                                </span>

                                </div>

                                <div class="activity-date">
                                    {{ \Carbon\Carbon::parse($activity['date'])->format('d M H:i') }}
                                </div>

                            </a>

                        @endforeach

                    </div>

                @endif

            </div>

        </div>

    </div>
@endsection


@push('scripts')

    <script>

        /* =========================================================
           CHART DATA
        ========================================================= */

        const salesChartLabels = @json($salesChartLabels);
        const salesChartData = @json($salesChartData);

        const inventoryChartLabels = @json($inventoryChartLabels);
        const inventoryChartData = @json($inventoryChartData);


        /* =========================================================
           SALES CHART
        ========================================================= */

        const salesCanvas = document.getElementById('salesChart');

        new Chart(salesCanvas, {

            type: 'line',

            data: {

                labels: salesChartLabels,

                datasets: [{

                    label: 'Sales (Tsh)',

                    data: salesChartData,

                    borderColor: '#4f46e5',

                    backgroundColor: 'rgba(79,70,229,.08)',

                    borderWidth: 3,

                    fill: true,

                    tension: .42,

                    pointRadius: 3,

                    pointHoverRadius: 6,

                    pointBackgroundColor: '#ffffff',

                    pointBorderWidth: 2

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                interaction: {
                    intersect: false,
                    mode: 'index'
                },

                scales: {

                    y: {

                        beginAtZero: true,

                        grid: {
                            color: 'rgba(148,163,184,.12)',
                            drawBorder: false
                        },

                        ticks: {
                            color: '#98a1b2',
                            font: {
                                size: 10
                            }
                        },

                        title: {
                            display: true,
                            text: 'Sales Amount',
                            color: '#98a1b2',
                            font: {
                                size: 10,
                                weight: '600'
                            }
                        }

                    },

                    x: {

                        grid: {
                            display: false
                        },

                        ticks: {
                            color: '#98a1b2',
                            font: {
                                size: 9
                            },
                            maxRotation: 0
                        },

                        title: {
                            display: true,
                            text: 'Date',
                            color: '#98a1b2',
                            font: {
                                size: 10,
                                weight: '600'
                            }
                        }

                    }

                },

                plugins: {

                    legend: {
                        display: false
                    },

                    tooltip: {

                        backgroundColor: '#172033',

                        titleColor: '#ffffff',

                        bodyColor: '#e5e7eb',

                        padding: 12,

                        cornerRadius: 10,

                        displayColors: false,

                        callbacks: {

                            label: function(context) {

                                return 'Tsh ' +
                                    Number(context.parsed.y)
                                        .toLocaleString(undefined, {
                                            minimumFractionDigits: 2,
                                            maximumFractionDigits: 2
                                        });

                            }

                        }

                    }

                }

            }

        });


        /* =========================================================
           INVENTORY CHART
        ========================================================= */

        const inventoryCanvas = document.getElementById('inventoryChart');

        new Chart(inventoryCanvas, {

            type: 'doughnut',

            data: {

                labels: inventoryChartLabels,

                datasets: [{

                    data: inventoryChartData,

                    backgroundColor: [

                        '#4f46e5',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6',
                        '#06b6d4',
                        '#f97316',
                        '#64748b',
                        '#ec4899',
                        '#3b82f6'

                    ],

                    borderWidth: 3,

                    borderColor: '#ffffff',

                    hoverOffset: 8

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                cutout: '70%',

                plugins: {

                    legend: {

                        position: 'right',

                        labels: {

                            color: '#667085',

                            padding: 10,

                            usePointStyle: true,

                            pointStyle: 'circle',

                            font: {
                                size: 10,
                                weight: '600'
                            }

                        }

                    },

                    tooltip: {

                        backgroundColor: '#172033',

                        titleColor: '#ffffff',

                        bodyColor: '#e5e7eb',

                        padding: 12,

                        cornerRadius: 10,

                        callbacks: {

                            label: function(context) {

                                let label = context.label || '';

                                if (label) {
                                    label += ': ';
                                }

                                if (context.parsed !== null) {
                                    label += Number(context.parsed)
                                        .toLocaleString() + ' units';
                                }

                                return label;

                            }

                        }

                    }

                }

            }

        });

    </script>

@endpush
