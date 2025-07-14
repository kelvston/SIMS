@extends('layouts.app')

@section('content')
    <style>
        .report-summary-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        .report-summary-item:last-child {
            border-bottom: none;
        }
        .report-label {
            font-weight: 600;
            color: #4b5563;
        }
        .report-value {
            font-weight: bold;
            color: #1f2937;
        }
        .positive {
            color: #10b981; /* Green */
        }
        .negative {
            color: #ef4444; /* Red */
        }
    </style>

<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Profit & Loss Report</h1>

    <form action="{{ route('reports.profit_loss') }}" method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm flex flex-wrap items-center justify-center gap-4">
        <div class="flex items-center gap-2">
            <label for="start_date" class="text-gray-700 text-sm font-bold">Start Date:</label>
            <input type="date" name="start_date" id="start_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate }}">
        </div>
        <div class="flex items-center gap-2">
            <label for="end_date" class="text-gray-700 text-sm font-bold">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate }}">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
            Apply Filter
        </button>
        <a href="{{ route('reports.profit_loss') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
            Clear Filter
        </a>
    </form>

    <div class="p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Summary</h2>

        <div class="report-summary-item">
            <span class="report-label">Total Revenue:</span>
            <span class="report-value">${{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="report-summary-item">
            <span class="report-label">Total Cost of Goods Sold (COGS):</span>
            <span class="report-value">${{ number_format($totalCostOfGoodsSold, 2) }}</span>
        </div>
        <div class="report-summary-item text-lg {{ $grossProfit >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Gross Profit/Loss:</span>
            <span class="report-value">${{ number_format($grossProfit, 2) }}</span>
        </div>
        <div class="report-summary-item text-lg {{ $grossProfitMarginPercentage >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Gross Profit Margin:</span>
            <span class="report-value">{{ number_format($grossProfitMarginPercentage, 2) }}%</span>
        </div>
    </div>

    <div class="flex justify-end mt-8">
        <a href="{{ url('/') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
            Back to Dashboard
        </a>
    </div>
@endsection
