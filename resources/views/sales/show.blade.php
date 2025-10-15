@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sale Details #{{ $sale->id }}</h1>

        {{-- Customer Info --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Customer Information</h2>
            <div class="detail-item"><span class="detail-label">Customer Name:</span> <span class="detail-value">{{ $sale->customer_name }}</span></div>
            <div class="detail-item"><span class="detail-label">Customer Phone:</span> <span class="detail-value">{{ $sale->customer_phone ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Sale Date:</span> <span class="detail-value">{{ $sale->sale_date->format('Y-m-d H:i') }}</span></div>
            <div class="detail-item"><span class="detail-label">Sale Type:</span>
                <span class="detail-value">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    @if($sale->is_installment) bg-yellow-100 text-yellow-800
                    @elseif($sale->amount_due > 0) bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800 @endif">
                    @if($sale->is_installment)
                        Installment
                    @elseif($sale->amount_due > 0)
                        Credit Sale
                    @else
                        Full Payment
                    @endif
                </span>
            </span>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Financial Summary</h2>
            <div class="detail-item"><span class="detail-label">Total Amount (Before Discount):</span> <span class="detail-value">{{ number_format($sale->total_amount, 2) }}</span></div>
            <div class="detail-item"><span class="detail-label">Discount Applied:</span> <span class="detail-value">{{ number_format($sale->discount_amount, 2) }}</span></div>
            <div class="detail-item font-bold text-lg"><span class="detail-label">Final Amount:</span> <span class="detail-value">{{ number_format($sale->final_amount, 2) }}</span></div>
        </div>

        {{-- Items Sold Section --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Items Sold</h2>

            {{-- Phones Sold --}}
            @php
                $phonesSold = $sale->saleItems->filter(fn($item) => $item->phone !== null);
            @endphp
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Phones Sold</h3>
            @if ($phonesSold->isEmpty())
                <p class="text-gray-600 mb-4">No phones associated with this sale.</p>
            @else
                <ul class="list-disc list-inside space-y-2 mb-4">
                    @foreach ($phonesSold as $item)
                        <li class="text-gray-700">
                            <strong>{{ $item->phone->brand->name }} {{ $item->phone->model }}</strong>
                            ({{ $item->phone->color }}, {{ $item->phone->storage_capacity }}) -
                            IMEI: {{ $item->phone->imei }} -
                            Sold Price: {{ number_format($item->unit_price, 2) }}
                        </li>
                    @endforeach
                </ul>
            @endif

            {{-- Accessories Sold --}}
            @php
                $cosmeticsSold = $sale->saleItems->filter(fn($item) => $item->accessory !== null);
            @endphp
            <h3 class="text-xl font-semibold text-gray-600 mb-2">Accessories Sold</h3>
            @if ($cosmeticsSold->isEmpty())
                <p class="text-gray-600">No cosmetics associated with this sale.</p>
            @else
                <ul class="list-disc list-inside space-y-2">
                    @foreach ($cosmeticsSold as $item)
                        <li class="text-gray-700">
                            <strong>{{ $item->accessory->name }}</strong>
                            (Quantity: {{ $item->quantity }}) -
                            Sold Price: {{ number_format($item->unit_price, 2) }} each
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Payment Status and Installment Plan --}}
        @if ($sale->is_installment)
            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Installment Plan Details</h2>
                <div class="detail-item"><span class="detail-label">Total Installments:</span> <span class="detail-value">{{ $sale->installment->total_installments }}</span></div>
                <div class="detail-item"><span class="detail-label">Amount Per Installment:</span> <span class="detail-value">{{ number_format($sale->installment->installment_amount, 2) }}</span></div>
                <div class="detail-item"><span class="detail-label">Installment Start Date:</span> <span class="detail-value">{{ $sale->installment->start_date->format('Y-m-d') }}</span></div>
                <div class="detail-item"><span class="detail-label">Next Payment Date:</span> <span class="detail-value">{{ $sale->installment->next_payment_date ? $sale->installment->next_payment_date->format('Y-m-d') : 'N/A' }}</span></div>
                <div class="detail-item"><span class="detail-label">Plan Status:</span>
                    <span class="detail-value">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($sale->installment->status == 'active') bg-green-100 text-green-800
                            @elseif($sale->installment->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($sale->installment->status == 'defaulted') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($sale->installment->status) }}
                        </span>
                    </span>
                </div>

                {{-- Payment History Table --}}
                <h3 class="text-xl font-semibold text-gray-600 mt-6 mb-3">Payment History</h3>
                @if ($sale->installment->installmentPayments->isEmpty())
                    <p class="text-gray-600">No payments recorded yet for this installment plan.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($sale->installment->installmentPayments->sortBy('payment_date') as $payment)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ number_format($payment->amount_paid, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Add Payment Button --}}
                @can('record installment payments')
                    @if ($sale->installment->status == 'active')
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('installments.pay.form', $sale->installment->id) }}"
                               class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-lg">
                                Record New Payment
                            </a>
                        </div>
                    @endif
                @endcan
            </div>
        @else
            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Payment Status</h2>
                <div class="detail-item"><span class="detail-label">Amount Paid:</span> <span class="detail-value">{{ number_format($sale->amount_paid, 2) }}</span></div>
                <div class="detail-item"><span class="detail-label">Amount Due:</span> <span class="detail-value">{{ number_format($sale->amount_due, 2) }}</span></div>
                @if ($sale->amount_due > 0)
                    @can('record payments')
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('sales.pay.form', $sale->id) }}"
                               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-lg">
                                Record New Payment
                            </a>
                        </div>
                    @endcan
                @endif
            </div>
        @endif

        <div class="flex justify-end mt-8">
            <a href="{{ route('sales.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                Back to All Sales
            </a>
        </div>
    </div>
@endsection
