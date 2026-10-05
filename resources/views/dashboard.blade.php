{{-- @extends('layouts.app')
@section('title', 'Business Dashboard')
@section('content')
<div class="mx-auto max-w-7xl space-y-8">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Business overview</p><h1 class="mt-1 text-3xl font-bold text-slate-800">Garage dashboard</h1><p class="mt-1 text-sm text-slate-500">Sales, inventory, and workshop operations in one place.</p></div><div class="flex gap-3"><a href="{{ route('sales.create') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700">New sale</a><a href="{{ route('motor-services.create') }}" class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white">New service job</a></div></div>
<div class="row">
    <div class = "col-md-6">
<section class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5 sm:p-6"><div class="mb-5 flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-emerald-700">Retail business</p><h2 class="text-xl font-semibold text-slate-800">Sales & inventory</h2></div><a href="{{ route('sales.index') }}" class="text-sm font-semibold text-emerald-700">View sales →</a></div><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Sales this month</p><p class="mt-1 text-2xl font-bold">Tsh {{ number_format($monthlySales,2) }}</p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Net profit</p><p class="mt-1 text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-700':'text-red-700' }}">Tsh {{ number_format($netProfit,2) }}</p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Available stock</p><p class="mt-1 text-2xl font-bold">{{ number_format($totalPhones->sum('quantity')) }} <span class="text-sm font-medium text-slate-500">units</span></p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Credit/installment due</p><p class="mt-1 text-2xl font-bold text-amber-700">Tsh {{ number_format($pendingInstallmentsAmount,2) }}</p></div></div></section>
    </div>
    <div class = "col-md-6">
        <section class="rounded-2xl border border-orange-100 bg-orange-50/50 p-5 sm:p-6"><div class="mb-5 flex items-center justify-between"><div><p class="text-xs font-bold uppercase tracking-wider text-orange-700">Workshop</p><h2 class="text-xl font-semibold text-slate-800">Motor services</h2></div><a href="{{ route('motor-services.index') }}" class="text-sm font-semibold text-orange-700">All service jobs →</a></div><div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Open jobs</p><p class="mt-1 text-2xl font-bold">{{ $openServiceJobs }}</p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Waiting for parts</p><p class="mt-1 text-2xl font-bold text-amber-700">{{ $jobsWaitingParts }}</p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Collections this month</p><p class="mt-1 text-2xl font-bold text-emerald-700">Tsh {{ number_format($serviceRevenueThisMonth,2) }}</p></div><div class="rounded-xl bg-white p-4 shadow-sm"><p class="text-sm text-slate-500">Service balance due</p><p class="mt-1 text-2xl font-bold text-red-700">Tsh {{ number_format($serviceOutstanding,2) }}</p></div></div></section>
    </div>
</div>




    <div class="grid gap-6 lg:grid-cols-2"><section class="rounded-xl bg-white p-5 shadow-sm"><div class="mb-4 flex justify-between"><h2 class="font-semibold text-slate-800">Recent service jobs</h2><a href="{{ route('motor-services.create') }}" class="text-sm font-semibold text-orange-700">Add job</a></div><div class="table-scroll"><table class="min-w-full text-sm"><thead class="border-b text-left text-xs uppercase text-slate-500"><tr><th class="p-2">Job</th><th class="p-2">Vehicle / customer</th><th class="p-2">Status</th><th class="p-2 text-right">Balance</th></tr></thead><tbody>@forelse($recentServiceJobs as $job)<tr class="border-b last:border-0"><td class="p-2"><a class="font-semibold text-orange-700" href="{{ route('motor-services.show',$job) }}">{{ $job->job_number }}</a></td><td class="p-2">{{ $job->vehicle->registration_number }}<br><span class="text-xs text-slate-500">{{ $job->vehicle->customer_name }}</span></td><td class="p-2">{{ str_replace('_',' ',ucfirst($job->status)) }}</td><td class="p-2 text-right">Tsh {{ number_format($job->total_amount-$job->amount_paid,2) }}</td></tr>@empty<tr><td colspan="4" class="p-5 text-center text-slate-500">No service jobs yet.</td></tr>@endforelse</tbody></table></div></section>
    <section class="rounded-xl bg-white p-5 shadow-sm"><div class="mb-4 flex justify-between"><h2 class="font-semibold text-slate-800">Recent retail activity</h2><a href="{{ route('sales.index') }}" class="text-sm font-semibold text-emerald-700">View sales</a></div><ul class="divide-y">@forelse($recentActivities as $activity)<li class="flex items-center justify-between gap-4 py-3"><a href="{{ $activity['link'] }}" class="min-w-0 truncate text-sm text-slate-700 hover:text-orange-700">{{ $activity['description'] }}</a><span class="shrink-0 text-xs text-slate-400">{{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</span></li>@empty<li class="py-5 text-center text-sm text-slate-500">No recent activity.</li>@endforelse</ul><div class="mt-3">{{ $recentActivities->links('pagination::tailwind') }}</div></section></div>

    @if($lowStockProducts->isNotEmpty())<section class="rounded-xl border border-red-100 bg-white p-5 shadow-sm"><div class="mb-3 flex justify-between"><h2 class="font-semibold text-slate-800">Low stock alerts</h2><a href="{{ route('reports.stock') }}" class="text-sm font-semibold text-red-700">Stock report</a></div><div class="flex flex-wrap gap-2">@foreach($lowStockProducts as $item)<span class="rounded-full bg-red-50 px-3 py-1 text-sm text-red-700">{{ $item->brand->name ?? 'N/A' }} {{ $item->model }}: {{ $item->current_stock }} left</span>@endforeach</div></section>@endif
</div>
@endsection --}}


@extends('layouts.app')

@section('title', 'Business Dashboard')

@section('content')
<div class="mx-auto max-w-7xl space-y-8">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">
                Business overview
            </p>

            <h1 class="mt-1 text-3xl font-bold text-slate-800">
                Garage dashboard
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Sales, inventory, and workshop operations in one place.
            </p>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('sales.create') }}"
               class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                New sale
            </a>

            <a href="{{ route('motor-services.create') }}"
               class="rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700">
                New service job
            </a>
        </div>
    </div>


    {{-- Sales & Motor Services --}}
    <div class="grid gap-6 md:grid-cols-2">
        {{-- Motor Services --}}
        <section class="rounded-2xl border border-orange-100 bg-orange-50/50 p-5 sm:p-6">

            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-orange-700">
                        Workshop
                    </p>

                    <h2 class="text-xl font-semibold text-slate-800">
                        Motor services
                    </h2>
                </div>

                <a href="{{ route('motor-services.index') }}"
                   class="shrink-0 text-sm font-semibold text-orange-700 hover:text-orange-800">
                    All service jobs →
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">

                {{-- Open Jobs --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Open jobs
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        {{ $openServiceJobs }}
                    </p>
                </div>

                {{-- Waiting For Parts --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Waiting for parts
                    </p>

                    <p class="mt-1 text-2xl font-bold text-amber-700">
                        {{ $jobsWaitingParts }}
                    </p>
                </div>

                {{-- Service Revenue --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Collections this month
                    </p>

                    <p class="mt-1 text-2xl font-bold text-emerald-700">
                        Tsh {{ number_format($serviceRevenueThisMonth, 2) }}
                    </p>
                </div>

                {{-- Service Balance --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Service balance due
                    </p>

                    <p class="mt-1 text-2xl font-bold text-red-700">
                        Tsh {{ number_format($serviceOutstanding, 2) }}
                    </p>
                </div>

            </div>
        </section>
   {{-- Sales & Inventory --}}
        <section class="rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5 sm:p-6">

            <div class="mb-5 flex items-center justify-between gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-700">
                        Retail business
                    </p>

                    <h2 class="text-xl font-semibold text-slate-800">
                        Sales & inventory
                    </h2>
                </div>

                <a href="{{ route('sales.index') }}"
                   class="shrink-0 text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                    View sales →
                </a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">

                {{-- Sales This Month --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Sales this month
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        Tsh {{ number_format($monthlySales, 2) }}
                    </p>
                </div>

                {{-- Net Profit --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Net profit
                    </p>

                    <p class="mt-1 text-2xl font-bold {{ $netProfit >= 0 ? 'text-emerald-700' : 'text-red-700' }}">
                        Tsh {{ number_format($netProfit, 2) }}
                    </p>
                </div>

                {{-- Available Stock --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Available stock
                    </p>

                    <p class="mt-1 text-2xl font-bold text-slate-800">
                        {{ number_format($totalPhones->sum('quantity')) }}
                        <span class="text-sm font-medium text-slate-500">
                            units
                        </span>
                    </p>
                </div>

                {{-- Credit / Installment --}}
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <p class="text-sm text-slate-500">
                        Credit/installment due
                    </p>

                    <p class="mt-1 text-2xl font-bold text-amber-700">
                        Tsh {{ number_format($pendingInstallmentsAmount, 2) }}
                    </p>
                </div>

            </div>
        </section>
    </div>


    {{-- Recent Activity --}}
    <div class="grid gap-6 lg:grid-cols-2">

        {{-- Recent Service Jobs --}}
        <section class="rounded-xl bg-white p-5 shadow-sm">

            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="font-semibold text-slate-800">
                    Recent service jobs
                </h2>

                <a href="{{ route('motor-services.create') }}"
                   class="text-sm font-semibold text-orange-700 hover:text-orange-800">
                    Add job
                </a>
            </div>

            <div class="table-scroll overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="border-b text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="p-2">Job</th>
                            <th class="p-2">Vehicle / customer</th>
                            <th class="p-2">Status</th>
                            <th class="p-2 text-right">Balance</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($recentServiceJobs as $job)
                            <tr class="border-b last:border-0">

                                <td class="p-2">
                                    <a href="{{ route('motor-services.show', $job) }}"
                                       class="font-semibold text-orange-700 hover:text-orange-800">
                                        {{ $job->job_number }}
                                    </a>
                                </td>

                                <td class="p-2">
                                    {{ $job->vehicle->registration_number }}

                                    <br>

                                    <span class="text-xs text-slate-500">
                                        {{ $job->vehicle->customer_name }}
                                    </span>
                                </td>

                                <td class="p-2">
                                    {{ str_replace('_', ' ', ucfirst($job->status)) }}
                                </td>

                                <td class="p-2 text-right whitespace-nowrap">
                                    Tsh {{ number_format($job->total_amount - $job->amount_paid, 2) }}
                                </td>

                            </tr>
                        @empty

                            <tr>
                                <td colspan="4"
                                    class="p-5 text-center text-slate-500">
                                    No service jobs yet.
                                </td>
                            </tr>

                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>


        {{-- Recent Retail Activity --}}
        <section class="rounded-xl bg-white p-5 shadow-sm">

            <div class="mb-4 flex items-center justify-between gap-4">
                <h2 class="font-semibold text-slate-800">
                    Recent retail activity
                </h2>

                <a href="{{ route('sales.index') }}"
                   class="text-sm font-semibold text-emerald-700 hover:text-emerald-800">
                    View sales
                </a>
            </div>

            <ul class="divide-y">

                @forelse($recentActivities as $activity)

                    <li class="flex items-center justify-between gap-4 py-3">

                        <a href="{{ $activity['link'] }}"
                           class="min-w-0 truncate text-sm text-slate-700 hover:text-orange-700">
                            {{ $activity['description'] }}
                        </a>

                        <span class="shrink-0 text-xs text-slate-400">
                            {{ \Carbon\Carbon::parse($activity['date'])->diffForHumans() }}
                        </span>

                    </li>

                @empty

                    <li class="py-5 text-center text-sm text-slate-500">
                        No recent activity.
                    </li>

                @endforelse

            </ul>

            <div class="mt-3">
                {{ $recentActivities->links('pagination::tailwind') }}
            </div>

        </section>

    </div>


    {{-- Low Stock Alerts --}}
    @if($lowStockProducts->isNotEmpty())

        <section class="rounded-xl border border-red-100 bg-white p-5 shadow-sm">

            <div class="mb-3 flex items-center justify-between gap-4">
                <h2 class="font-semibold text-slate-800">
                    Low stock alerts
                </h2>

                <a href="{{ route('reports.stock') }}"
                   class="text-sm font-semibold text-red-700 hover:text-red-800">
                    Stock report
                </a>
            </div>

            <div class="flex flex-wrap gap-2">

                @foreach($lowStockProducts as $item)

                    <span class="rounded-full bg-red-50 px-3 py-1 text-sm text-red-700">
                        {{ $item->brand->name ?? 'N/A' }}
                        {{ $item->model }}:
                        {{ $item->current_stock }} left
                    </span>

                @endforeach

            </div>

        </section>

    @endif

</div>
@endsection
