@extends('layouts.app')

@section('content')
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Stock Report</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-blue-700 text-sm font-semibold">Total Stocked Items (Units)</p>
            <p class="text-2xl font-bold text-blue-900">{{ $totalStockItems }}</p>
        </div>
        <div class="bg-red-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-red-700 text-sm font-semibold">Items Below Low Stock Threshold</p>
            <p class="text-2xl font-bold text-red-900">{{ $lowStockCount }}</p>
        </div>
    </div>

    @if ($stockLevels->isEmpty())
        <p class="text-center text-gray-600">No stock levels recorded.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Threshold</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($stockLevels as $stock)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->brand->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->model }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->color }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->current_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->low_stock_threshold }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($stock->current_stock <= $stock->low_stock_threshold) bg-red-100 text-red-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $stock->current_stock <= $stock->low_stock_threshold ? 'Low Stock' : 'Sufficient' }}
                                    </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->last_updated_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $stockLevels->links('pagination::tailwind') }}
        </div>
    @endif
    <div class="flex justify-end mt-8">
        <a href="{{ url('/') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
            Back to Dashboard
        </a>
    </div>
@endsection
