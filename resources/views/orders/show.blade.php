@extends('layouts.app')

@section('title', 'Order ' . $order->invoice_number)
@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
    @if(session('success'))<div class="mb-4 rounded bg-green-100 border border-green-300 text-green-800 p-3">{{ session('success') }}</div>@endif
    @if(session('warning'))<div class="mb-4 rounded bg-amber-100 border border-amber-300 text-amber-800 p-3">{{ session('warning') }}</div>@endif
    <div class="flex justify-between gap-4 mb-6"><div><h1 class="text-2xl font-bold">{{ $order->invoice_number }}</h1><p class="text-gray-600">Order date: {{ $order->order_date->format('d M Y') }}</p></div><a href="{{ route('orders.invoice', $order) }}" class="bg-green-600 text-white font-semibold px-4 py-2 rounded h-fit">Download invoice</a></div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6"><div><h2 class="font-semibold">Customer</h2><p>{{ $order->customer_name }}</p><p>{{ $order->customer_phone }}</p><p>{{ $order->customer_email }}</p></div><div><h2 class="font-semibold">Status</h2><p class="capitalize">{{ $order->status }}</p><p class="text-sm text-gray-500">Created by {{ $order->user?->name ?? 'System' }}</p></div></div>
    <table class="min-w-full border"><thead class="bg-gray-50"><tr><th class="p-3 text-left">Description</th><th class="p-3 text-right">Qty</th><th class="p-3 text-right">Unit price</th><th class="p-3 text-right">Amount</th></tr></thead><tbody>@foreach($order->items as $item)<tr class="border-t"><td class="p-3">{{ $item->description }}</td><td class="p-3 text-right">{{ $item->quantity }}</td><td class="p-3 text-right">Tsh {{ number_format($item->unit_price, 2) }}</td><td class="p-3 text-right">Tsh {{ number_format($item->line_total, 2) }}</td></tr>@endforeach</tbody><tfoot class="border-t font-semibold"><tr><td colspan="3" class="p-3 text-right">Subtotal</td><td class="p-3 text-right">Tsh {{ number_format($order->subtotal, 2) }}</td></tr><tr><td colspan="3" class="p-3 text-right">Discount</td><td class="p-3 text-right">Tsh {{ number_format($order->discount_amount, 2) }}</td></tr><tr><td colspan="3" class="p-3 text-right text-lg">Total</td><td class="p-3 text-right text-lg">Tsh {{ number_format($order->total_amount, 2) }}</td></tr></tfoot></table>
    @if($order->notes)<div class="mt-5"><h2 class="font-semibold">Notes</h2><p class="whitespace-pre-line">{{ $order->notes }}</p></div>@endif
</div>
@endsection
