@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">General Business Report</h1>

        <!-- Filter Form -->
        <form action="{{ route('reports.general') }}" method="GET" class="bg-white rounded-lg shadow p-6 mb-8 flex flex-col md:flex-row items-end space-y-4 md:space-y-0 md:space-x-4">
            <div class="w-full md:w-auto flex-grow">
                <label for="start_date" class="block text-gray-700 font-semibold mb-2">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full md:w-auto flex-grow">
                <label for="end_date" class="block text-gray-700 font-semibold mb-2">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-full md:w-auto">
                <button type="submit" class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors">
                    Apply Filters
                </button>
            </div>
            <div class="w-full md:w-auto">
                <a href="{{ route('reports.download', ['report' => 'general', 'start_date' => request('start_date'), 'end_date' => request('end_date')]) }}"
                   class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-lg transition-colors text-center block">
                    Download PDF
                </a>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Revenue</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalRevenue'], 2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Expenses</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalExpenses'], 2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Net Profit</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['netProfit'], 2) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Total Sales Count</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalSalesCount']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Medicines in Stock</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalMedicinesInStock']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Medicines Sold</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalSoldMedicines']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-sm text-gray-500">Active Installments</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($summary['totalActiveInstallments']) }}</div>
            </div>
        </div>

        <!-- Trend Data -->
{{--        <div class="bg-white rounded-lg shadow p-6">--}}
{{--            <h3 class="text-xl font-semibold text-gray-800 mb-4">Sales & Expenses Trend ({{ \Carbon\Carbon::parse(request('start_date', now()->subMonths(6)))->format('M Y') }} - {{ \Carbon\Carbon::parse(request('end_date', now()))->format('M Y') }})</h3>--}}
{{--            <p class="text-sm text-gray-600">--}}
{{--                This section would typically contain a chart (e.g., using a library like Chart.js) to visualize the trend data. The data below is provided for this purpose.--}}
{{--            </p>--}}
{{--            <div class="mt-4 p-4 bg-gray-100 rounded-lg">--}}
{{--            <pre class="overflow-auto text-sm text-gray-700">--}}
{{--                // Chart Labels--}}
{{--                Trend Labels: {{ json_encode($trendData['trendLabels']) }}--}}

{{--                // Sales Data--}}
{{--                Sales Data: {{ json_encode($trendData['salesData']) }}--}}

{{--                // Expenses Data--}}
{{--                Expenses Data: {{ json_encode($trendData['expensesData']) }}--}}
{{--            </pre>--}}
{{--            </div>--}}
{{--        </div>--}}

    </div>
@endsection
