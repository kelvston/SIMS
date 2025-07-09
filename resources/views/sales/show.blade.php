@extends('layouts.app')

@section('content')
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -70%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sale Details #{{ $sale->id }}</h1>

    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Customer Information</h2>
        <div class="detail-item">
            <span class="detail-label">Customer Name:</span>
            <span class="detail-value">{{ $sale->customer_name }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Customer Phone:</span>
            <span class="detail-value">{{ $sale->customer_phone ?? 'N/A' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Sale Date:</span>
            <span class="detail-value">{{ $sale->sale_date->format('Y-m-d H:i') }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Sale Type:</span>
            <span class="detail-value">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($sale->is_installment) bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ $sale->is_installment ? 'Installment' : 'Full Payment' }}
                    </span>
                </span>
        </div>
    </div>

    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Financial Summary</h2>
        <div class="detail-item">
            <span class="detail-label">Total Amount (Before Discount):</span>
            <span class="detail-value">${{ number_format($sale->total_amount, 2) }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Discount Applied:</span>
            <span class="detail-value">${{ number_format($sale->discount_amount, 2) }}</span>
        </div>
        <div class="detail-item font-bold text-lg">
            <span class="detail-label">Final Amount:</span>
            <span class="detail-value">${{ number_format($sale->final_amount, 2) }}</span>
        </div>
    </div>

    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Phones Sold</h2>
        @if ($sale->saleItems->isEmpty())
            <p class="text-gray-600">No phones associated with this sale.</p>
        @else
            <ul class="list-disc list-inside space-y-2">
                @foreach ($sale->saleItems as $item)
                    <li class="text-gray-700">
                        <strong>{{ $item->phone->brand->name }} {{ $item->phone->model }}</strong> ({{ $item->phone->color }}, {{ $item->phone->storage_capacity }}) - IMEI: {{ $item->phone->imei }} - Sold Price: ${{ number_format($item->unit_price, 2) }}
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if ($sale->is_installment && $sale->installmentPlan)
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Installment Plan Details</h2>
            <div class="detail-item">
                <span class="detail-label">Total Installments:</span>
                <span class="detail-value">{{ $sale->installmentPlan->total_installments }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Amount Per Installment:</span>
                <span class="detail-value">${{ number_format($sale->installmentPlan->installment_amount, 2) }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Installment Start Date:</span>
                <span class="detail-value">{{ $sale->installmentPlan->start_date->format('Y-m-d') }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Next Payment Date:</span>
                <span class="detail-value">{{ $sale->installmentPlan->next_payment_date ? $sale->installmentPlan->next_payment_date->format('Y-m-d') : 'N/A' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Plan Status:</span>
                <span class="detail-value">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($sale->installmentPlan->status == 'active') bg-green-100 text-green-800
                            @elseif($sale->installmentPlan->status == 'completed') bg-blue-100 text-blue-800
                            @elseif($sale->installmentPlan->status == 'defaulted') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($sale->installmentPlan->status) }}
                        </span>
                    </span>
            </div>

            <h3 class="text-xl font-semibold text-gray-600 mt-6 mb-3">Payment History</h3>
            @if ($sale->installmentPlan->installmentPayments->isEmpty())
                <p class="text-gray-600">No payments recorded yet for this installment plan.</p>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($sale->installmentPlan->installmentPayments->sortBy('payment_date') as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payment->amount_paid, 2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Button to record a new payment --}}
            @can('record installment payments')
                @if ($sale->installmentPlan->status == 'active')
                    <div class="flex justify-end mt-6">
                        <a href="{{ route('installments.pay.form', $sale->installmentPlan->id) }}" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-lg">
                            Record New Payment
                        </a>
                    </div>
                @endif
            @endcan
        </div>
    @endif

    <div class="flex justify-end mt-8">
        <a href="{{ route('sales.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
            Back to All Sales
        </a>
    </div>
@endsection
