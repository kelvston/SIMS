@extends('layouts.app')

@section('content')
    <style>
        .hexagon-shape {
            clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
            transition: all 0.3s ease-in-out;
            height: 90px;
            font-size: 15px;
        }

        .hexagon-shape:hover {
            transform: scale(1.23);
        }

        .arrow-curve {
            position: absolute;
            z-index: 0;
            pointer-events: none;
        }

        .arrow-right {
            top: 25px;
            left: 32%;
        }

        .arrow-down {
            top: 80px;
            left: 66%;
        }
    </style>

    <!-- Watermark -->

	@if(isset($settings['organization_logo_path']))
{{--    <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}"--}}
{{--         alt="Watermark"--}}
{{--         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"--}}
{{--         style="transform: translate(-50%, -60%);border-radius: 20%" />--}}
@endif





    <!-- Hexagon Buttons and Arrows Wrapper -->
    <!-- Hexagon Buttons and Arrows Wrapper -->
    <div class="relative">
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 relative z-10">
            @can('receive cashew')
                <a href="{{ route('cashew.receive.form') }}"
                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-1 transition duration-200 mt-6">
                    <i class="fas fa-download text-[14px]"></i> Receive
                </a>
            @endcan


        @can('create sales')
                <a href="{{ route('sales.create') }}"
                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-green-600 hover:bg-green-700 text-white py-2 px-1 transition duration-200">
                    <i class="fas fa-dollar-sign text-[14px]"></i> Sale
                </a>
            @endcan

            @can('create expenses')
                <div class="flex items-center">
                    <!-- Expense Button -->
                    <a href="{{ route('expenses.create') }}"
                       class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-red-500 hover:bg-red-600 text-white py-2 px-1 transition duration-200">
                        <i class="fas fa-receipt text-[14px]"></i> Expense
                    </a>

                    <!-- Vertical Line -->
                    <div class="border-l border-gray-400 h-14 mx-3"></div>
                </div>
            @endcan

            <!-- Summary block -->
            <div class="lg:col-span-1 p-3 bg-white rounded-md shadow-sm border border-gray-200">
                <h2 class="text-xs font-bold mb-2 text-gray-800 flex items-center gap-1">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    SUMMARY
                </h2>
                <table class="text-xs w-full text-left">
                    <tr class="font-semibold text-gray-700">
                        <th colspan="2" class="pb-1">KEY METRICS (Current Month)</th>
                    </tr>
                    <tr>
                        <td>Total Products:</td>
                        <td><b>{{ $product_count }}</b></td>
                    </tr>
                    <tr>
                        <td>Units in Stock:</td>
                        <td><b>{{ number_format($availableStockUnits) }}</b></td>
                    </tr>
                    <tr>
                        <td>Monthly Sales:</td>
                        <td><b>Tsh {{ number_format($monthlySales, 2) }}</b></td>
                    </tr>
                    <tr>
                        <td>Pending Installments:</td>
                        <td><b>Tsh {{ number_format($pendingInstallmentsAmount, 2) }}</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
        @php
            $cards = [
                ['icon' => '📦', 'label' => 'Products', 'value' => number_format($product_count), 'color' => 'indigo'],
                ['icon' => '📊', 'label' => 'Stock Units', 'value' => number_format($availableStockUnits), 'color' => 'blue'],
                ['icon' => '💰', 'label' => 'Sales (' . \Carbon\Carbon::now()->format('M') . ')', 'value' => 'Tsh ' . number_format($monthlySales, 2), 'color' => 'green'],
                ['icon' => '🏦', 'label' => 'Inventory Value', 'value' => 'Tsh ' . number_format($inventoryValue, 2), 'color' => 'cyan'],
                ['icon' => '💸', 'label' => 'Expenses', 'value' => 'Tsh ' . number_format($monthlyExpenses, 2), 'color' => 'red'],
                ['icon' => '📈', 'label' => 'Net Profit', 'value' => 'Tsh ' . number_format($netProfit, 2), 'color' => $netProfit >= 0 ? 'green' : 'red'],
                ['icon' => '⏳', 'label' => 'Pending', 'value' => 'Tsh ' . number_format($pendingInstallmentsAmount, 2), 'color' => 'yellow'],
                ['icon' => '📈', 'label' => 'Profit', 'value' => number_format($profitMarginPercentage, 2) . '%', 'color' => $profitMarginPercentage >= 0 ? 'green' : 'red'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="p-2 bg-gradient-to-br from-{{ $card['color'] }}-50 to-white rounded-lg border border-{{ $card['color'] }}-200 shadow-sm hover:shadow-md transition duration-200 transform hover:-translate-y-0.5">
                <div class="flex items-center gap-1 mb-0.5 text-{{ $card['color'] }}-600 text-xs">
                    <span class="text-base">{{ $card['icon'] }}</span>
                    <span class="font-semibold uppercase tracking-wide truncate">{{ $card['label'] }}</span>
                </div>
                <p class="text-lg font-bold text-gray-800">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <!-- Sales Chart -->
        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">
            <h2 class="text-xs font-semibold mb-1 text-gray-800">Sales (30 Days)</h2>
            <div class="h-48 overflow-hidden">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Inventory Chart -->
        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">
            <h2 class="text-xs font-semibold mb-1 text-gray-800">Inventory by Product</h2>
            <div class="h-48 overflow-hidden">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>



    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">

    <!-- Low Stock Table -->
{{--    @can('view stock reports')--}}
        <div class="p-2 bg-white rounded-lg shadow overflow-x-auto">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold">Low Stock Products Overview</h2>
                <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full">{{ $notificationCount }} alerts</span>
            </div>
            @if ($lowStockProducts->isEmpty())
                <p class="text-center text-gray-600">No products are currently low in stock.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-2">Item</th>
                        <th class="text-left p-2">Stock</th>
                        <th class="text-left p-2">Threshold</th>
                        <th class="text-left p-2">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lowStockProducts as $item)
                        <tr>
                            <td class="p-2">{{ optional($item->product)->name ?? 'Unknown product' }}</td>
                            <td class="p-2">{{ number_format($item->quantity) }} units</td>
                            <td class="p-2">{{ number_format($item->low_stock_threshold) }} units</td>
                            <td class="p-2">
                                <span class="text-red-700 bg-red-100 px-2 py-1 rounded-full text-xs font-semibold">Low</span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
{{--    @endcan--}}

        <div class="p-2 bg-white rounded-lg shadow overflow-x-auto">
            <h2 class="font-semibold mb-4">Recent Activity</h2>
            @if ($recentActivities->isEmpty())
                <p class="text-center text-gray-600">No recent activity found.</p>
            @else
                <div class="space-y-2">
                    @foreach ($recentActivities as $activity)
                        <a href="{{ $activity['link'] }}" class="block border border-gray-100 rounded-md p-2 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm text-gray-800">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-500">{{ ucfirst($activity['type']) }}</p>
                                </div>
                                <span class="text-xs text-gray-500 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($activity['date'])->format('d M H:i') }}
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Chart Data from Laravel Controller
        const salesChartLabels = @json($salesChartLabels);
        const salesChartData = @json($salesChartData);
        const inventoryChartLabels = @json($inventoryChartLabels);
        const inventoryChartData = @json($inventoryChartData);

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesChartLabels,
                datasets: [{
                    label: 'Sales (Tsh)',
                    data: salesChartData,
                    borderColor: '#4f46e5',
                    backgroundColor: 'rgba(79, 70, 229, 0.1)',
                    borderWidth: 3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Sales Amount'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Tsh ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        new Chart(document.getElementById('inventoryChart'), {
            type: 'doughnut',
            data: {
                labels: inventoryChartLabels,
                datasets: [{
                    data: inventoryChartData,
                    backgroundColor: [
                        '#4f46e5', // Indigo
                        '#10b981', // Green
                        '#f59e0b', // Amber
                        '#ef4444', // Red
                        '#8b5cf6', // Purple
                        '#06b6d4', // Cyan
                        '#f97316', // Orange
                        '#6b7280', // Gray
                        '#ec4899', // Pink
                        '#3b82f6'  // Blue
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' units';
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
