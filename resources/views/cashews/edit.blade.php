@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Edit Cashew Stock
        </h1>

        @if (isset($errors) && $errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <strong class="font-bold">Validation Error</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cashews.update', $cashew->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="product_id" class="block text-gray-700 text-sm font-bold mb-2">Product</label>
                    <select name="product_id" id="product_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id', $cashew->product_id) == $product->id)>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="unit" class="block text-gray-700 text-sm font-bold mb-2">Unit</label>
                    <input type="text" name="unit" id="unit" required
                           value="{{ old('unit', $cashew->unit) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity</label>
                    <input type="number" name="quantity" id="quantity" min="0" required
                           value="{{ old('quantity', $cashew->quantity) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="low_stock_threshold" class="block text-gray-700 text-sm font-bold mb-2">Low Stock Threshold</label>
                    <input type="number" name="low_stock_threshold" id="low_stock_threshold" min="0" required
                           value="{{ old('low_stock_threshold', $cashew->low_stock_threshold) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="unit_price" class="block text-gray-700 text-sm font-bold mb-2">Buying Price (TZS)</label>
                    <input type="number" step="0.01" name="unit_price" id="unit_price" min="0" required
                           value="{{ old('unit_price', $cashew->unit_price) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="selling_price" class="block text-gray-700 text-sm font-bold mb-2">Selling Price (TZS)</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" min="0" required
                           value="{{ old('selling_price', $cashew->selling_price) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                    <select name="status" id="status" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach (['available', 'reserved', 'sold'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $cashew->status) === $status)>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="received_at" class="block text-gray-700 text-sm font-bold mb-2">Received At</label>
                    <input type="date" name="received_at" id="received_at"
                           value="{{ old('received_at', optional($cashew->received_at)->format('Y-m-d')) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="condition" class="block text-gray-700 text-sm font-bold mb-2">Condition</label>
                    <input type="text" name="condition" id="condition"
                           value="{{ old('condition', $cashew->condition) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>

                <div>
                    <label for="batch_number" class="block text-gray-700 text-sm font-bold mb-2">Batch Number</label>
                    <input type="text" name="batch_number" id="batch_number"
                           value="{{ old('batch_number', $cashew->batch_number) }}"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition duration-300 shadow">
                    Update Stock
                </button>

                <a href="{{ route('cashews.index') }}"
                   class="font-bold text-sm text-gray-500 hover:text-gray-800">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
