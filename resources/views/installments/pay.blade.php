@extends('layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <style>

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #e5e7eb;
        }
        .detail-item:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #4b5563;
        }
        .detail-value {
            color: #1f2937;
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Record Installment Payment</h1>

    <!-- Success/Error Messages -->
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
            <strong class="font-bold">Validation Error!</strong>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">Installment Plan #{{ $installmentPlan->id }} Details</h2>
        <div class="detail-item">
            <span class="detail-label">Customer Name:</span>
            <span class="detail-value">{{ $installmentPlan->sale->customer_name }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Associated Sale ID:</span>
            <span class="detail-value">
                    <a href="{{ route('sales.show', $installmentPlan->sale->id) }}" class="text-indigo-600 hover:text-indigo-900">
                        {{ $installmentPlan->sale->id }}
                    </a>
                </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Phone(s) Sold:</span>
            <span class="detail-value">
                    @foreach ($installmentPlan->sale->saleItems as $item)
                    {{ $item->phone->brand->name }} {{ $item->phone->model }} (IMEI: {{ $item->phone->imei }})<br>
                @endforeach
                </span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Sale Amount:</span>
            <span class="detail-value">${{ number_format($installmentPlan->sale->final_amount, 2) }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Total Paid So Far:</span>
            <span class="detail-value text-green-600">${{ number_format($totalPaid, 2) }}</span>
        </div>
        <div class="detail-item font-bold text-lg">
            <span class="detail-label">Remaining Balance:</span>
            <span class="detail-value text-red-600">${{ number_format($remainingAmount, 2) }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Amount Per Installment:</span>
            <span class="detail-value">${{ number_format($installmentPlan->installment_amount, 2) }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Next Payment Due:</span>
            <span class="detail-value">{{ $installmentPlan->next_payment_date ? $installmentPlan->next_payment_date->format('Y-m-d') : 'N/A (Completed)' }}</span>
        </div>
        <div class="detail-item">
            <span class="detail-label">Plan Status:</span>
            <span class="detail-value">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        @if($installmentPlan->status == 'active') bg-green-100 text-green-800
                        @elseif($installmentPlan->status == 'completed') bg-blue-100 text-blue-800
                        @elseif($installmentPlan->status == 'defaulted') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($installmentPlan->status) }}
                    </span>
                </span>
        </div>
    </div>

    @if ($installmentPlan->status == 'active')
        <form action="{{ route('installments.pay.store', $installmentPlan->id) }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="amount_paid" class="block text-gray-700 text-sm font-bold mb-2">Amount to Pay ($):</label>
                <input type="number" step="0.01" name="amount_paid" id="amount_paid" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('amount_paid') border-red-500 @enderror" value="{{ old('amount_paid', min($installmentPlan->installment_amount, $remainingAmount)) }}" min="0.01" max="{{ $remainingAmount }}">
                @error('amount_paid')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="payment_date" class="block text-gray-700 text-sm font-bold mb-2">Payment Date (Optional):</label>
                <input type="date" name="payment_date" id="payment_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('payment_date') border-red-500 @enderror" value="{{ old('payment_date', date('Y-m-d')) }}">
                @error('payment_date')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Record Payment
                </button>
                <a href="{{ route('installments.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Back to Installment Plans
                </a>
            </div>
        </form>
    @else
        <div class="p-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Info:</strong>
            <span class="block sm:inline">This installment plan is {{ ucfirst($installmentPlan->status) }}. No further payments can be recorded.</span>
        </div>
        <div class="flex justify-end mt-8">
            <a href="{{ route('installments.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                Back to Installment Plans
            </a>
        </div>
    @endif
@endsection
