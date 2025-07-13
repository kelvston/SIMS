@extends('layouts.app')

@section('content')
    <style>
        .hexagon-shape {
            clip-path: polygon(25% 5%, 75% 5%, 100% 50%, 75% 95%, 25% 95%, 0% 50%);
            transition: all 0.3s ease-in-out;
            height: 60px;
            font-size: 10px;
        }

        .hexagon-shape:hover {
            transform: scale(1.03);
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
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -90%);" />
    <div class="relative">
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 relative z-10">
            @can('receive phones')
                <a href="{{ route('phones.receive.form') }}"
                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-1 transition duration-200">
                    <i class="fas fa-download text-[11px]"></i> Receive
                </a>
            @endcan

            @can('create sales')
                <a href="{{ route('sales.create') }}"
                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-green-600 hover:bg-green-700 text-white py-2 px-1 transition duration-200">
                    <i class="fas fa-dollar-sign text-[11px]"></i> Sale
                </a>
            @endcan

            @can('create expenses')
                <a href="{{ route('expenses.create') }}"
                   class="hexagon-shape flex items-center justify-center gap-1 w-full text-[10px] bg-red-500 hover:bg-red-600 text-white py-2 px-1 transition duration-200">
                    <i class="fas fa-receipt text-[11px]"></i> Expense
                </a>
            @endcan
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
                        <th colspan="2" class="pb-1">GENERAL SUMMARY</th>
                    </tr>
                    <tr>
                        <td>Invested:</td>
                        <td><b>1,221,133,311</b></td>
                    </tr>
                    <tr>
                        <td>Profit:</td>
                        <td><b>1,221,992,722</b></td>
                    </tr>
                    <tr>
                        <td>Loss:</td>
                        <td><b>12,211,223</b></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>


<div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3">
        @php
            $cards = [
                ['icon' => '📱', 'label' => 'Phones', 'value' => number_format($totalPhones), 'color' => 'indigo'],
                ['icon' => '💰', 'label' => 'Sales (' . \Carbon\Carbon::now()->format('M') . ')', 'value' => '$' . number_format($monthlySales, 2), 'color' => 'green'],
                ['icon' => '⏳', 'label' => 'Pending', 'value' => '$' . number_format($pendingInstallmentsAmount, 2), 'color' => 'yellow'],
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-3">
        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">
            <h2 class="text-xs font-semibold mb-1 text-gray-800">Sales (30 Days)</h2>
            <div class="h-48 overflow-hidden">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        <div class="p-2 bg-white rounded-md shadow-sm border border-gray-200">
            <h2 class="text-xs font-semibold mb-1 text-gray-800">Inventory by Brand</h2>
            <div class="h-48 overflow-hidden">
                <canvas id="inventoryChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 p-3 bg-white rounded-md shadow-sm border border-gray-200">
            <h2 class="text-sm font-bold mb-3 text-gray-800 flex items-center gap-1">
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round"
                                               d="M5 13l4 4L19 7" /></svg>
                Quick Actions
            </h2>
            <div class="space-y-1">
                @can('receive phones')
                    <a href="{{ route('phones.receive.form') }}"
                       class="flex items-center justify-center gap-2 w-full text-sm bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 px-3 rounded transition duration-200">
                        <i class="fas fa-download"></i> Receive Phones
                    </a>
                @endcan

                @can('create sales')
                    <a href="{{ route('sales.create') }}"
                       class="flex items-center justify-center gap-2 w-full text-sm bg-green-600 hover:bg-green-700 text-white py-1.5 px-3 rounded transition duration-200">
                        <i class="fas fa-dollar-sign"></i> Record Sale
                    </a>
                @endcan

                @can('create expenses')
                    <a href="{{ route('expenses.create') }}"
                       class="flex items-center justify-center gap-2 w-full text-sm bg-red-500 hover:bg-red-600 text-white py-1.5 px-3 rounded transition duration-200">
                        <i class="fas fa-receipt"></i> Record Expense
                    </a>
                @endcan
            </div>
        </div>
        <div class="lg:col-span-2 p-2 bg-white rounded-md shadow border border-gray-200">
            <h2 class="text-xs font-semibold text-gray-800 mb-1 flex items-center gap-1">
                <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8v4l3 3M12 6a9 9 0 100 18 9 9 0 000-18z"/>
                </svg>
                Recent Activity
            </h2>
            <ul class="space-y-0.5 text-[11px] text-gray-700">
                @forelse ($recentActivities as $activity)
                    <li class="px-2 py-0.5 flex justify-between items-center hover:bg-gray-100 rounded transition">
                        <a href="{{ $activity['link'] }}" class="text-blue-600 hover:underline truncate w-3/4">
                            {{ $activity['description'] }}
                        </a>
                        <span class="text-[10px] text-gray-400 text-right w-1/4 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                        </span>
                    </li>
                @empty
                    <li class="text-gray-500 text-xs px-2">No recent activity to display.</li>
                @endforelse
            </ul>

            <div class="mt-1 text-xs px-2">
                {{ $recentActivities->links('pagination::tailwind') }}
            </div>
        </div>
    </div>
    @can('view stock reports')
        <div class="p-4 bg-white rounded-lg shadow overflow-x-auto">
            <h2 class="font-semibold mb-4">Low Stock Products Overview</h2>
            @if ($lowStockProducts->isEmpty())
                <p class="text-center text-gray-600">No products are currently low in stock.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="text-left p-2">Product</th>
                        <th class="text-left p-2">Brand</th>
                        <th class="text-left p-2">Stock</th>
                        <th class="text-left p-2">Threshold</th>
                        <th class="text-left p-2">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($lowStockProducts as $item)
                        <tr>
                            <td class="p-2">{{ $item->model }} ({{ $item->color }})</td>
                            <td class="p-2">{{ $item->brand->name ?? 'N/A' }}</td>
                            <td class="p-2">{{ $item->current_stock }} units</td>
                            <td class="p-2">{{ $item->low_stock_threshold }} units</td>
                            <td class="p-2 text-red-600">Critical</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endcan
@endsection

@push('scripts')
    <script>
        const salesChartLabels = @json($salesChartLabels);
        const salesChartData = @json($salesChartData);
        const inventoryChartLabels = @json($inventoryChartLabels);
        const inventoryChartData = @json($inventoryChartData);

        new Chart(document.getElementById('salesChart'), {
            type: 'line',
            data: {
                labels: salesChartLabels,
                datasets: [{
                    label: 'Sales ($)',
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
                            text: 'Sales Amount ($)'
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
                                return context.dataset.label + ': $' + context.parsed.y.toFixed(2);
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
