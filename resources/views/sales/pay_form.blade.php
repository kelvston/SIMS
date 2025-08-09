@extends('layouts.app')

@section('title', 'Record Payment')
@section('subtitle', 'Record a new payment for a credit sale.')

@section('content')
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -50%);" />

    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Record Payment for Sale #{{ $sale->id }}</h1>
    <h2 class="text-xl text-gray-600 mb-8 text-center">Customer: {{ $sale->customer_name }}</h2>

    <div class="p-6 bg-gray-50 rounded-lg border border-gray-200 mb-8">
        <div class="text-lg mb-2">
            <span class="font-bold text-gray-700">Final Amount:</span>
            <span class="text-gray-900">{{ number_format($sale->final_amount, 2) }}</span>
        </div>
        <div class="text-lg mb-2">
            <span class="font-bold text-gray-700">Amount Paid:</span>
            <span class="text-gray-900">{{ number_format($sale->amount_paid, 2) }}</span>
        </div>
        <div class="text-2xl font-extrabold text-red-600 mb-4">
            <span class="font-bold">Amount Due:</span>
            <span>{{ number_format($sale->amount_due, 2) }}</span>
        </div>
    </div>

    @if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Whoops!</strong>
        <span class="block sm:inline">There were some problems with your input.</span>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('sales.pay.store', $sale->id) }}" method="POST">
        @csrf

        <div class="mb-4">
            <label for="payment_amount" class="block text-gray-700 text-sm font-bold mb-2">Payment Amount:</label>
            <input type="number"
                   name="payment_amount"
                   id="payment_amount"
                   class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                   step="0.01"
                   min="0.01"
                   max="{{ $sale->amount_due }}"
                   value="{{ old('payment_amount', number_format($sale->amount_due, 2, '.', '')) }}"
                   required>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-lg">
                Record Payment
            </button>
            <a href="{{ route('sales.show', $sale->id) }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
