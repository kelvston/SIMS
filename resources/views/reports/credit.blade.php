@extends('layouts.app')

@section('title', 'Credit Report')

@section('content')
<div class="container mx-auto rounded-xl bg-white p-5 shadow-md sm:p-8">
    <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Collections</p>
            <h1 class="mt-1 text-2xl font-semibold text-slate-800">Credit report</h1>
            <p class="mt-1 text-sm text-slate-500">Outstanding customer credit balances.</p>
        </div>
        <a class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('reports.excel', array_merge(['report' => 'credit'], request()->query())) }}">Download report (.xlsx)</a>
    </div>

    <form class="mb-5 flex flex-wrap items-end gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <label class="text-sm font-medium text-slate-700">From <input class="mt-1 block rounded-lg border-slate-300" type="date" name="start_date" value="{{ $startDate }}"></label>
        <label class="text-sm font-medium text-slate-700">To <input class="mt-1 block rounded-lg border-slate-300" type="date" name="end_date" value="{{ $endDate }}"></label>
        <button class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-orange-700">Apply</button>
    </form>

    <div class="mb-6 overflow-x-auto rounded-lg border border-slate-200">
        <dl class="flex min-w-max divide-x divide-slate-200 bg-slate-50">
            <div class="min-w-64 px-5 py-4"><dt class="text-sm text-slate-500">Total outstanding</dt><dd class="mt-1 text-xl font-bold text-slate-900">Tsh {{ number_format($outstanding, 2) }}</dd></div>
            <div class="min-w-64 px-5 py-4"><dt class="text-sm text-slate-500">Overdue balance</dt><dd class="mt-1 text-xl font-bold text-red-700">Tsh {{ number_format($overdue, 2) }}</dd></div>
            <div class="min-w-48 px-5 py-4"><dt class="text-sm text-slate-500">Open credit sales</dt><dd class="mt-1 text-xl font-bold text-slate-900">{{ $credits->count() }}</dd></div>
        </dl>
    </div>

    <div class="table-scroll rounded-lg border border-slate-200">
        <table class="responsive-table min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50"><tr><th class="p-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sale #</th><th class="p-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Customer</th><th class="p-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</th><th class="p-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Sale date</th><th class="p-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Due date</th><th class="p-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Balance</th></tr></thead>
            <tbody class="divide-y divide-slate-100 bg-white">@forelse($credits as $sale)<tr><td data-label="Sale" class="p-3 text-sm text-slate-700">#{{ $sale->id }}</td><td data-label="Customer" class="p-3 text-sm font-medium text-slate-900">{{ $sale->customer_name }}</td><td data-label="Phone" class="p-3 text-sm text-slate-700">{{ $sale->customer_phone ?: '—' }}</td><td data-label="Sale date" class="p-3 text-sm text-slate-700">{{ optional($sale->sale_date)->format('d M Y') }}</td><td data-label="Due date" class="p-3 text-sm {{ $sale->credit_due_date?->isBefore(today()) ? 'font-semibold text-red-700' : 'text-slate-700' }}">{{ optional($sale->credit_due_date)->format('d M Y') ?? 'N/A' }}</td><td data-label="Balance" class="p-3 text-right text-sm font-semibold text-slate-900">Tsh {{ number_format($sale->amount_due, 2) }}</td></tr>@empty<tr><td colspan="6" class="p-7 text-center text-sm text-slate-500">No outstanding credit in this period.</td></tr>@endforelse</tbody>
        </table>
    </div>
</div>
@endsection
