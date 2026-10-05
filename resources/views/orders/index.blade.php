@extends('layouts.app')

@section('title', 'Orders')
@section('subtitle', 'Manage customer orders and invoices.')

@section('content')
<div class="container mx-auto bg-white p-5 sm:p-8 rounded-xl shadow-md">
    <div class="mb-7 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div><p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Customer requests</p><h1 class="mt-1 text-2xl font-semibold text-slate-800">Orders</h1></div>
        @can('create sales')<a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700">New order</a>@endcan
    </div>
    @if(session('success'))<div class="mb-4 rounded bg-green-100 border border-green-300 text-green-800 p-3">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="mb-4 rounded bg-red-100 border border-red-300 text-red-800 p-3">{{ session('error') }}</div>@endif
    @if(session('warning'))<div class="mb-4 rounded bg-amber-100 border border-amber-300 text-amber-800 p-3">{{ session('warning') }}</div>@endif
    <div class="table-scroll"><table class="responsive-table min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50"><tr><th class="p-3 text-left">Invoice</th><th class="p-3 text-left">Customer</th><th class="p-3 text-left">Items</th><th class="p-3 text-left">Total</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Date</th><th class="p-3"></th></tr></thead>
        <tbody class="divide-y divide-gray-200">
        @forelse($orders as $order)<tr>
            <td class="p-3 font-medium">{{ $order->invoice_number }}</td><td class="p-3">{{ $order->customer_name }}</td><td class="p-3">{{ $order->items_count }}</td><td class="p-3">Tsh {{ number_format($order->total_amount, 2) }}</td>
            <td class="p-3"><span class="capitalize px-2 py-1 rounded bg-gray-100">{{ $order->status }}</span></td><td class="p-3">{{ $order->order_date->format('Y-m-d') }}</td>
            <td class="p-3 whitespace-nowrap space-y-2">
                <a class="text-indigo-600 hover:underline" href="{{ route('orders.show', $order) }}">View</a>
                <a class="ml-3 text-green-600 hover:underline" href="{{ route('orders.invoice', $order) }}">Invoice</a>
                @can('create sales')
                    @if(!($order->status === 'completed' && $order->sale_exists))<form method="POST" action="{{ route('orders.status', $order) }}" class="flex gap-1">@csrf @method('PATCH')
                        <select name="status" class="border rounded text-sm p-1">
                            @if($order->status === 'completed')<option value="confirmed">Reopen for sale</option>
                            @else @foreach(['pending','confirmed','cancelled'] as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>@endforeach @endif
                        </select>
                        <button class="text-sm text-blue-700 hover:underline">Update</button>
                    </form>@endif
                    @if(!in_array($order->status, ['completed','cancelled']))
                        <form method="POST" action="{{ route('orders.reserve', $order) }}" class="inline">@csrf<button class="text-sm text-amber-700 hover:underline">{{ $order->reserved_at ? 'Refresh reservation' : 'Reserve stock' }}</button></form>
                        @if($order->reserved_at)<a class="ml-3 text-sm text-green-700 hover:underline" href="{{ route('orders.create-sale', $order) }}">Create sale</a>@endif
                    @endif
                @endcan
            </td>
        </tr>@empty<tr><td colspan="7" class="p-6 text-center text-gray-500">No orders have been created.</td></tr>@endforelse
        </tbody>
    </table></div>
    <div class="mt-5">{{ $orders->links('pagination::tailwind') }}</div>
</div>
@endsection
