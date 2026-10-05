@extends('layouts.app')

@section('content')
<div class="container mx-auto bg-white p-5 sm:p-8 rounded-xl shadow-md">
    <div class="mb-7 flex flex-wrap items-start justify-between gap-4"><div><p class="text-xs font-semibold uppercase tracking-[.16em] text-orange-600">Reporting</p><h1 class="mt-1 text-2xl font-semibold text-slate-800">Stock report</h1><p class="mt-1 text-sm text-slate-500">Track available stock and items requiring replenishment.</p></div><a class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700" href="{{ route('reports.excel', ['report' => 'stock']) }}">Download report (.xlsx)</a></div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="border border-blue-100 bg-blue-50 p-5 rounded-xl text-center">
            <p class="text-blue-700 text-sm font-semibold">Total Stocked Items (Units)</p>
            <p class="text-2xl font-bold text-blue-900">{{ $totalStockItems }}</p>
        </div>
        <div class="border border-red-100 bg-red-50 p-5 rounded-xl text-center">
            <p class="text-red-700 text-sm font-semibold">Items Below Low Stock Threshold</p>
            <p class="text-2xl font-bold text-red-900">{{ $lowStockCount }}</p>
        </div>
    </div>

    @if ($stockLevels->isEmpty())
        <p class="text-center text-gray-600">No stock levels recorded.</p>
    @else
        <div class="table-scroll sm:rounded-lg sm:border sm:border-gray-200 sm:shadow-sm">
            <table class="responsive-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Model</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Stock</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Low Stock Threshold</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($stockLevels as $stock)
                    <tr>
                        <td data-label="Brand" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->brand->name ?? 'N/A' }}</td>
                        <td data-label="Model" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->model }}</td>
                        <td data-label="Current Stock" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->phone->quantity }}</td>
                        <td data-label="Low Stock Threshold" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->low_stock_threshold }}</td>
                        <td data-label="Status" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($stock->current_stock <= $stock->low_stock_threshold) bg-red-100 text-red-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $stock->current_stock <= $stock->low_stock_threshold ? 'Low Stock' : 'Sufficient' }}
                                    </span>
                        </td>
                        <td data-label="Last Updated" class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stock->last_updated_at->format('Y-m-d H:i') }}</td>
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
</div>
@endsection
