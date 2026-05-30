@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Profit & Loss Report</h1>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form action="{{ route('reports.profit_loss') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex items-end space-x-2">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if(request()->has('start_date') || request()->has('end_date'))
                        <a href="{{ route('reports.profit_loss') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                            <i class="fas fa-undo-alt mr-2"></i>Reset
                        </a>
                    @endif
                    <a href="{{ route('reports.profit_loss', array_merge(request()->query(), ['download' => 'true'])) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Download CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <!-- Summary Cards (Circle Design) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8 justify-center text-center">
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500">Revenue</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalRevenue, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-red-500">
                <p class="text-sm font-medium text-gray-500">COGS</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalCostOfGoodsSold, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-yellow-500">
                <p class="text-sm font-medium text-gray-500">Gross Profit</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($grossProfit, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-purple-500">
                <p class="text-sm font-medium text-gray-500">Expenses</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalExpenses, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-emerald-500">
                <p class="text-sm font-medium text-gray-500">Net Profit</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($netProfit, 2) }}</p>
            </div>
        </div>


        <!-- P&L Chart -->


        <!-- Detailed Transactions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Sales Transactions</h2>
                </div>
                <div class="overflow-x-auto" style="max-height: 400px;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->customer_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($sale->final_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No sales transactions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Expense Transactions</h2>
                </div>
                <div class="overflow-x-auto" style="max-height: 400px;">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $expense->description }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($expense->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">No expense transactions found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/chart.min.js') }}"></script>
    <script>

    </script>
@endsection
