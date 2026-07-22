@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative"> <!-- Added relative -->
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" /> <!-- Centered better -->

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sale Details #{{ $sale->id }}</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Please fix:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($sale->is_voided)
            <div class="mb-8 p-6 bg-red-50 rounded-lg border border-red-200">
                <h2 class="text-2xl font-semibold text-red-800 mb-4">Voided Sale</h2>
                <div class="detail-item"><span class="detail-label">Voided By:</span> <span class="detail-value">{{ $sale->voidedBy->name ?? 'Unknown user' }}</span></div>
                <div class="detail-item"><span class="detail-label">Voided At:</span> <span class="detail-value">{{ optional($sale->voided_at)->format('Y-m-d H:i') ?? 'N/A' }}</span></div>
                <div class="detail-item"><span class="detail-label">Reason:</span> <span class="detail-value">{{ $sale->void_reason ?? 'N/A' }}</span></div>
                <div class="detail-item"><span class="detail-label">Original Final Amount:</span> <span class="detail-value">Tsh {{ number_format($sale->original_final_amount ?? $sale->final_amount, 2) }}</span></div>
            </div>
        @endif

        {{-- Customer Info --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Customer Information</h2>
            <div class="detail-item"><span class="detail-label">Customer Name:</span> <span class="detail-value">{{ $sale->customer_name }}</span></div>
            <div class="detail-item"><span class="detail-label">Customer Phone:</span> <span class="detail-value">{{ $sale->customer_phone ?? 'N/A' }}</span></div>
            <div class="detail-item"><span class="detail-label">Sale Date:</span> <span class="detail-value">{{ $sale->sale_date->format('Y-m-d H:i') }}</span></div>
            <div class="detail-item"><span class="detail-label">Status:</span>
                <span class="detail-value">
                    @if($sale->is_voided)
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Voided</span>
                    @else
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                    @endif
                </span>
            </div>
            <div class="detail-item"><span class="detail-label">Sale Type:</span>
                <span class="detail-value">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                    @if($sale->is_credit) bg-purple-100 text-purple-800
                    @elseif($sale->is_installment) bg-yellow-100 text-yellow-800
                    @else bg-blue-100 text-blue-800 @endif">
                    {{ $sale->sale_type_label }}
                </span>
            </span>
            </div>
        </div>

        {{-- Financial Summary --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Financial Summary</h2>
            <div class="detail-item"><span class="detail-label">Total Amount (Before Discount):</span> <span class="detail-value">Tsh {{ number_format($sale->total_amount, 2) }}</span></div>
            <div class="detail-item"><span class="detail-label">Discount Applied:</span> <span class="detail-value">Tsh {{ number_format($sale->discount_amount, 2) }}</span></div>
            <div class="detail-item font-bold text-lg"><span class="detail-label">Final Amount:</span> <span class="detail-value">Tsh {{ number_format($sale->final_amount, 2) }}</span></div>
            <div class="detail-item"><span class="detail-label">Amount Paid:</span> <span class="detail-value">Tsh {{ number_format($sale->amount_paid, 2) }}</span></div>
            <div class="detail-item"><span class="detail-label">Amount Due:</span> <span class="detail-value">Tsh {{ number_format($sale->amount_due, 2) }}</span></div>
            @if($sale->is_credit)
                <div class="detail-item"><span class="detail-label">Credit Due Date:</span> <span class="detail-value">{{ optional($sale->credit_due_date)->format('Y-m-d') ?? 'N/A' }}</span></div>
                @if($sale->credit_reminder_status)
                    <div class="mt-4 p-4 rounded border
                        @if($sale->credit_reminder_status === 'overdue') bg-red-50 border-red-200 text-red-800
                        @else bg-amber-50 border-amber-200 text-amber-800 @endif">
                        {{ $sale->credit_reminder_status === 'overdue' ? 'This credit sale is overdue.' : 'This credit sale is nearing its due date.' }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Items Sold --}}
        <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Items Sold</h2>
            @if ($sale->saleItems->isEmpty())
                <p class="text-gray-600">No items associated with this sale.</p>
            @else
                <ul class="list-disc list-inside space-y-2">
                    @foreach ($sale->saleItems as $item)
                        <li class="text-gray-700">
                            @if($item->phone)
                                <strong>{{ $item->phone->brand->name ?? 'N/A' }} {{ $item->phone->model }}</strong>
                                ({{ $item->phone->color }}, {{ $item->phone->storage_capacity }}) -
                                IMEI: {{ $item->phone->imei }} -
                                Sold Price: Tsh {{ number_format($item->unit_price, 2) }}
                            @elseif($item->product)
                                <strong>{{ $item->product->name }}</strong>
                                Qty: {{ $item->quantity }} -
                                Unit Price: Tsh {{ number_format($item->unit_price, 2) }} -
                                Line Total: Tsh {{ number_format($item->unit_price * $item->quantity, 2) }}
                            @else
                                <strong>Item removed</strong> -
                                Sold Price: Tsh {{ number_format($item->unit_price, 2) }}
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        {{-- Installment Plan --}}
        @if ($sale->is_installment && $sale->installmentPlan)
            <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
                <h2 class="text-2xl font-semibold text-gray-700 mb-4">Installment Plan Details</h2>
                <div class="detail-item"><span class="detail-label">Total Installments:</span> <span class="detail-value">{{ $sale->installmentPlan->total_installments }}</span></div>
                <div class="detail-item"><span class="detail-label">Amount Per Installment:</span> <span class="detail-value">Tsh {{ number_format($sale->installmentPlan->installment_amount, 2) }}</span></div>
                <div class="detail-item"><span class="detail-label">Installment Start Date:</span> <span class="detail-value">{{ $sale->installmentPlan->start_date->format('Y-m-d') }}</span></div>
                <div class="detail-item"><span class="detail-label">Next Payment Date:</span> <span class="detail-value">{{ $sale->installmentPlan->next_payment_date ? $sale->installmentPlan->next_payment_date->format('Y-m-d') : 'N/A' }}</span></div>
                <div class="detail-item"><span class="detail-label">Plan Status:</span>
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

                {{-- Payment History Table --}}
                <h3 class="text-xl font-semibold text-gray-600 mt-6 mb-3">Payment History</h3>
                @if ($sale->installmentPlan->installmentPayments->isEmpty())
                    <p class="text-gray-600">No payments recorded yet for this installment plan.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200 table-auto"> <!-- table-auto added -->
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($sale->installmentPlan->installmentPayments->sortBy('payment_date') as $payment)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $payment->payment_date->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tsh {{ number_format($payment->amount_paid, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                {{-- Add Payment Button --}}
                @can('record installment payments')
                    @if (! $sale->is_voided && $sale->installmentPlan->status == 'active')
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('installments.pay.form', $sale->installmentPlan->id) }}"
                               class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-lg">
                                Record New Payment
                            </a>
                        </div>
                    @endif
                @endcan
            </div>
        @endif

        @can('delete sales')
            @if(! $sale->is_voided)
                <div class="mb-8 p-6 bg-red-50 rounded-lg border border-red-200">
                    <h2 class="text-2xl font-semibold text-red-800 mb-4">Void Sale</h2>
                    <p class="text-sm text-red-700 mb-4">Use this only for a mistaken sale. The sale stays visible for audit, while the items are returned to available stock.</p>
                    <form id="void-sale-form" action="{{ route('sales.void', $sale->id) }}" method="POST">
                        @csrf
                        <label for="void_reason" class="block text-gray-700 text-sm font-bold mb-2">Reason</label>
                        <textarea id="void_reason" name="void_reason" rows="3" required minlength="5" maxlength="1000" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('void_reason') }}</textarea>
                        <div class="flex justify-end mt-4">
                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                                Void Sale
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        @endcan

        <div class="flex justify-end mt-8">
            @if($sale->saleReceipt)
                <a href="{{ route('sales.receipt', $sale->id) }}"
                   class="mr-3 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                    Download Receipt
                </a>
            @endif
            <a href="{{ route('sales.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                Back to All Sales
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const voidSaleForm = document.getElementById('void-sale-form');

            if (!voidSaleForm) {
                return;
            }

            voidSaleForm.addEventListener('submit', (event) => {
                event.preventDefault();

                Swal.fire({
                    title: 'Void this sale?',
                    text: 'Void this sale and return its items to stock? This cannot be undone automatically.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, void sale',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    reverseButtons: true,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        voidSaleForm.submit();
                    }
                });
            });
        });
    </script>
@endpush
