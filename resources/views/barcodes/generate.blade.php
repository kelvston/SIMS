{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl max-w-2xl">--}}
{{--        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Generate Barcodes</h1>--}}

{{--        <!-- The form will submit to a 'barcodes.generate' route -->--}}
{{--        <form action="{{ route('barcodes.generate') }}" method="POST">--}}
{{--            @csrf--}}

{{--            <!-- Product Name Input -->--}}
{{--            <div class="mb-5">--}}
{{--                <label for="product_name" class="block text-sm font-medium text-gray-700 mb-2">Product Name or SKU</label>--}}
{{--                <input type="text" id="product_name" name="product_name" required--}}
{{--                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"--}}
{{--                       placeholder="e.g., T-Shirt, SKU-12345">--}}
{{--            </div>--}}

{{--            <!-- Quantity Input -->--}}
{{--            <div class="mb-5">--}}
{{--                <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">Number of Barcodes</label>--}}
{{--                <input type="number" id="quantity" name="quantity" required min="1" max="100"--}}
{{--                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"--}}
{{--                       placeholder="e.g., 20">--}}
{{--            </div>--}}

{{--            <!-- Starting Number/Prefix Input (optional) -->--}}
{{--            <div class="mb-5">--}}
{{--                <label for="prefix" class="block text-sm font-medium text-gray-700 mb-2">Starting Prefix (Optional)</label>--}}
{{--                <input type="text" id="prefix" name="prefix"--}}
{{--                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"--}}
{{--                       placeholder="e.g., PROD-">--}}
{{--            </div>--}}

{{--            <!-- Submit Button -->--}}
{{--            <div class="flex justify-center">--}}
{{--                <button type="submit"--}}
{{--                        class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-300 ease-in-out transform hover:scale-105">--}}
{{--                    Generate Barcodes--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}
{{--@endsection--}}

@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-xl max-w-2xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Generate Barcodes</h1>

        <!-- The form will submit to the 'barcodes.generate' route -->
        <form action="{{ route('barcodes.generate') }}" method="POST">-
            @csrf

            <!-- Starting Number Input -->
            <div class="mb-5">
                <label for="start_number" class="block text-sm font-medium text-gray-700 mb-2">Starting Number</label>
                <input type="number" id="start_number" name="start_number" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                       placeholder="e.g., 100">
            </div>

            <!-- Ending Number Input -->
            <div class="mb-5">
                <label for="end_number" class="block text-sm font-medium text-gray-700 mb-2">Ending Number</label>
                <input type="number" id="end_number" name="end_number" required min="1"
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out"
                       placeholder="e.g., 120">
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-3 bg-indigo-600 text-white font-semibold rounded-md shadow-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-300 ease-in-out transform hover:scale-105">
                    Generate Barcodes
                </button>
            </div>
        </form>
    </div>
@endsection
