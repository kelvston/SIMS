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

<div class="container mx-auto bg-white p-5 sm:p-8 rounded-xl shadow-md">
    <div class="mb-7"><p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Financial reporting</p><h1 class="mt-1 text-2xl font-semibold text-slate-800">Profit &amp; loss</h1><p class="mt-1 text-sm text-slate-500">Measure revenue, cost of goods, expenses and net performance.</p></div>

    <!-- Date Filter Form -->
    <form action="{{ route('reports.profit_loss') }}" method="GET" class="mb-7 p-4 border border-slate-200 bg-slate-50 rounded-xl flex flex-wrap items-end gap-4">
        <div class="flex items-center gap-2">
            <label for="start_date" class="text-gray-700 text-sm font-bold">Start Date:</label>
            <input type="date" name="start_date" id="start_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate }}">
        </div>
        <div class="flex items-center gap-2">
            <label for="end_date" class="text-gray-700 text-sm font-bold">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate }}">
        </div>
        <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm">
            Apply Filter
        </button>
        <a href="{{ route('reports.profit_loss') }}" class="border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 font-semibold py-2.5 px-4 rounded-lg transition">
            Clear Filter
        </a>
    </form>

    <div class="mb-5 flex justify-end"><a class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white" href="{{ route('reports.excel', array_merge(['report' => 'profit-loss'], request()->query())) }}">Download Excel</a></div>
    <div class="p-5 sm:p-6 bg-slate-50 rounded-xl border border-slate-200">
        <h2 class="text-lg font-semibold text-slate-800 mb-4">Financial summary</h2>

        <div class="report-summary-item">
            <span class="report-label">Total Revenue:</span>
            <span class="report-value">Tsh {{ number_format($totalRevenue, 2) }}</span>
        </div>
        <div class="report-summary-item">
            <span class="report-label">Total Cost of Goods Sold (COGS):</span>
            <span class="report-value">Tsh {{ number_format($totalCostOfGoodsSold, 2) }}</span>
        </div>
        <div class="report-summary-item text-lg {{ $grossProfit >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Gross Profit/Loss:</span>
            <span class="report-value">Tsh {{ number_format($grossProfit, 2) }}</span>
        </div>
        <div class="report-summary-item text-lg {{ $grossProfitMarginPercentage >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Gross Profit Margin:</span>
            <span class="report-value">{{ number_format($grossProfitMarginPercentage, 2) }}%</span>
        </div>
        <div class="report-summary-item">
            <span class="report-label">Accessory Revenue:</span>
            <span class="report-value">Tsh {{ number_format($accessoryRevenue, 2) }}</span>
        </div>
        <div class="report-summary-item">
            <span class="report-label">Accessory COGS:</span>
            <span class="report-value">Tsh {{ number_format($accessoryCostOfGoodsSold, 2) }}</span>
        </div>
        <div class="report-summary-item {{ $accessoryGrossProfit >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Accessory Profit/Loss:</span>
            <span class="report-value">Tsh {{ number_format($accessoryGrossProfit, 2) }}</span>
        </div>
        <div class="report-summary-item">
            <span class="report-label">Total Expenses:</span>
            <span class="report-value">Tsh {{ number_format($totalExpenses, 2) }}</span>
        </div>
        <div class="report-summary-item text-lg {{ $netProfit >= 0 ? 'positive' : 'negative' }}">
            <span class="report-label">Net Profit/Loss:</span>
            <span class="report-value">Tsh {{ number_format($netProfit, 2) }}</span>
        </div>
    </div>

    <div class="flex justify-end mt-8">
        <a href="{{ url('/') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
            Back to Dashboard
        </a>
    </div>
@endsection
