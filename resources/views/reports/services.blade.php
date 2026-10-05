@extends('layouts.app')

@section('title', 'Service Report')

@section('content')
<div class="mx-auto max-w-7xl rounded-xl bg-white p-5 shadow sm:p-8">
    <div class="mb-7 flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Workshop reporting</p>
            <h1 class="mt-1 text-2xl font-semibold text-slate-800">Service report</h1>
            <p class="mt-1 text-sm text-slate-500">Track service jobs, billing, collections, and balances.</p>
        </div>
        <a class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('reports.excel', array_merge(['report' => 'services'], request()->query())) }}">Download report (.xlsx)</a>
    </div>

    <form method="GET" class="mb-7 flex flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <label class="text-sm font-medium text-slate-700">From<input type="date" name="start_date" value="{{ $startDate }}" class="mt-1 block rounded-lg border-slate-300"></label>
        <label class="text-sm font-medium text-slate-700">To<input type="date" name="end_date" value="{{ $endDate }}" class="mt-1 block rounded-lg border-slate-300"></label>
        <label class="text-sm font-medium text-slate-700">Status<select name="status" class="mt-1 block rounded-lg border-slate-300"><option value="">All statuses</option>@foreach(['received','diagnosing','in_progress','waiting_parts','completed','cancelled'] as $option)<option value="{{ $option }}" @selected($status === $option)>{{ ucwords(str_replace('_', ' ', $option)) }}</option>@endforeach</select></label>
        <button class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white">Apply filters</button>
        <a href="{{ route('reports.services') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">Reset</a>
    </form>

    <div class="mb-7 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Service jobs</p><p class="mt-1 text-2xl font-bold">{{ number_format($totals->jobs) }}</p></div>
        <div class="rounded-xl bg-slate-50 p-4"><p class="text-sm text-slate-500">Amount billed</p><p class="mt-1 text-2xl font-bold">Tsh {{ number_format($totals->billed, 2) }}</p></div>
        <div class="rounded-xl bg-emerald-50 p-4"><p class="text-sm text-emerald-700">Amount collected</p><p class="mt-1 text-2xl font-bold text-emerald-700">Tsh {{ number_format($totals->collected, 2) }}</p></div>
        <div class="rounded-xl bg-amber-50 p-4"><p class="text-sm text-amber-700">Outstanding balance</p><p class="mt-1 text-2xl font-bold text-amber-700">Tsh {{ number_format($totals->billed - $totals->collected, 2) }}</p></div>
    </div>

    <div class="table-scroll"><table class="responsive-table min-w-full"><thead><tr><th class="p-3 text-left">Job</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Vehicle / customer</th><th class="p-3 text-left">Mechanic</th><th class="p-3 text-left">Status</th><th class="p-3 text-right">Billed</th><th class="p-3 text-right">Paid</th><th class="p-3 text-right">Balance</th></tr></thead><tbody>@forelse($services as $service)<tr class="border-t hover:bg-slate-50"><td class="p-3"><a href="{{ route('motor-services.show', $service) }}" class="font-semibold text-orange-700">{{ $service->job_number }}</a></td><td class="p-3">{{ $service->service_date->format('d M Y') }}</td><td class="p-3">{{ $service->vehicle?->registration_number }}<br><span class="text-xs text-slate-500">{{ $service->vehicle?->customer_name }}</span></td><td class="p-3">{{ $service->mechanic?->name ?? 'Unassigned' }}</td><td class="p-3">{{ ucwords(str_replace('_', ' ', $service->status)) }}</td><td class="p-3 text-right">Tsh {{ number_format($service->total_amount, 2) }}</td><td class="p-3 text-right">Tsh {{ number_format($service->amount_paid, 2) }}</td><td class="p-3 text-right font-semibold">Tsh {{ number_format($service->total_amount - $service->amount_paid, 2) }}</td></tr>@empty<tr><td colspan="8" class="p-7 text-center text-slate-500">No service jobs found for the selected period.</td></tr>@endforelse</tbody></table></div>
    <div class="mt-5">{{ $services->links('pagination::tailwind') }}</div>
</div>
@endsection
