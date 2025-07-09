@extends('layouts.app')


@section('title', 'Phone Management Dashboard')
@section('subtitle', 'Welcome back! Here\'s your business overview')

@section('content')
    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -90%);" />

        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Total Phones (Available)</h3>
            <p class="text-2xl font-bold">{{ number_format($totalPhones) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Monthly Sales ({{ \Carbon\Carbon::now()->format('M Y') }})</h3>
            <p class="text-2xl font-bold">${{ number_format($monthlySales, 2) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Pending Installments</h3>
            <p class="text-2xl font-bold">${{ number_format($pendingInstallmentsAmount, 2) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Profit Margin ({{ \Carbon\Carbon::now()->format('M Y') }})</h3>
            <p class="text-2xl font-bold {{ $profitMarginPercentage >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ number_format($profitMarginPercentage, 2) }}%</p>
        </div>
    </div>

    <!-- New Stats Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Total Yearly Sales ({{ \Carbon\Carbon::now()->format('Y') }})</h3>
            <p class="text-2xl font-bold">${{ number_format($totalYearlySales, 2) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Total Phones Sold</h3>
            <p class="text-2xl font-bold">{{ number_format($totalSoldPhones) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Active Installment Plans</h3>
            <p class="text-2xl font-bold">{{ number_format($totalActiveInstallments) }}</p>
        </div>
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-sm text-gray-500">Total Monthly Expenses ({{ \Carbon\Carbon::now()->format('M Y') }})</h3> {{-- NEW STAT --}}
            <p class="text-2xl font-bold">${{ number_format($totalMonthlyExpenses, 2) }}</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="p-4 bg-white rounded-lg shadow chart-wrapper">
            <h2 class="font-semibold mb-2">Sales Chart (Last 30 Days)</h2>
            <canvas id="salesChart"></canvas>
        </div>
        <div class="p-4 bg-white rounded-lg shadow chart-wrapper">
            <h2 class="font-semibold mb-2">Inventory Distribution by Brand</h2>
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <!-- Quick Actions and Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 p-4 bg-white rounded-lg shadow">
            <h2 class="font-semibold mb-4">Quick Actions</h2>
            <div class="space-y-2">
                @can('receive phones')
                    <a href="{{ route('phones.receive.form') }}" class="w-full block bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 transition duration-300 ease-in-out">Receive Phones</a>
                @endcan
                @can('create sales')
                    <a href="{{ route('sales.create') }}" class="w-full block bg-green-600 text-white py-2 rounded hover:bg-green-700 transition duration-300 ease-in-out">Record Sale</a>
                @endcan
                @can('view installments')
                    <a href="{{ route('installments.index') }}" class="w-full block bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600 transition duration-300 ease-in-out">View Installments</a>
                @endcan
                @can('create expenses') {{-- NEW: Quick Action for Expenses --}}
                <a href="{{ route('expenses.create') }}" class="w-full block bg-red-500 text-white py-2 rounded hover:bg-red-600 transition duration-300 ease-in-out">Record Expense</a>
                @endcan
                @canany(['view sales reports', 'view stock reports', 'view profit loss reports'])
                    <a href="{{ route('reports.sales') }}" class="w-full block bg-purple-600 text-white py-2 rounded hover:bg-purple-700 transition duration-300 ease-in-out">Generate Report</a>
                @endcanany
            </div>
        </div>
        <div class="lg:col-span-2 p-4 bg-white rounded-lg shadow">
            <h2 class="font-semibold mb-4">Recent Activity</h2>
            <ul class="space-y-2 text-sm">
                @forelse ($recentActivities as $activity)
                    <li>
                        <a href="{{ $activity['link'] }}" class="text-gray-700 hover:text-blue-600">
                            {{ $activity['description'] }} ({{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                            )
                        </a>
                    </li>
                @empty
                    <li>No recent activity to display.</li>
                @endforelse
            </ul>
        </div>
    </div>

    <!-- Table -->
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
