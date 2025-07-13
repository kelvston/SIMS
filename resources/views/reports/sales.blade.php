@extends('layouts.app')

@section('content')
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-50%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Sales Report</h1>


    <form action="{{ route('reports.sales') }}" method="GET" class="mb-6 p-4 bg-gray-50 rounded-lg shadow-sm flex flex-wrap items-center justify-center gap-4">
        <div class="flex items-center gap-2">
            <label for="start_date" class="text-gray-700 text-sm font-bold">Start Date:</label>
            <input type="date" name="start_date" id="start_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $startDate }}">
        </div>
        <div class="flex items-center gap-2">
            <label for="end_date" class="text-gray-700 text-sm font-bold">End Date:</label>
            <input type="date" name="end_date" id="end_date" class="shadow-sm border rounded py-2 px-3 text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ $endDate }}">
        </div>
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
            Apply Filter
        </button>
        <a href="{{ route('reports.sales') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
            Clear Filter
        </a>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-blue-700 text-sm font-semibold">Total Sales Amount</p>
            <p class="text-2xl font-bold text-blue-900">${{ number_format($totalSalesAmount, 2) }}</p>
        </div>
        <div class="bg-yellow-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-yellow-700 text-sm font-semibold">Total Discount Given</p>
            <p class="text-2xl font-bold text-yellow-900">${{ number_format($totalDiscountAmount, 2) }}</p>
        </div>
        <div class="bg-purple-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-purple-700 text-sm font-semibold">Installment Sales</p>
            <p class="text-2xl font-bold text-purple-900">{{ $totalInstallmentSales }}</p>
        </div>
        <div class="bg-green-100 p-5 rounded-lg shadow-md text-center">
            <p class="text-green-700 text-sm font-semibold">Full Payment Sales</p>
            <p class="text-2xl font-bold text-green-900">{{ $totalFullPaymentSales }}</p>
        </div>
    </div>

    @if ($sales->isEmpty())
        <p class="text-center text-gray-600">No sales found for the selected period.</p>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phones Sold</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Amount</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($sales as $sale)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->customer_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <ul class="list-disc list-inside">
                                @foreach ($sale->saleItems as $item)
                                    <li>{{ $item->phone->brand->name }} {{ $item->phone->model }}</li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($sale->final_amount, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($sale->is_installment) bg-yellow-100 text-yellow-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $sale->is_installment ? 'Installment' : 'Full Payment' }}
                                    </span>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $sales->appends(request()->query())->links('pagination::tailwind') }}
        </div>
    @endif
    <div class="flex justify-end mt-8">
        <a href="{{ url('/') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-6 rounded-full transition duration-300 ease-in-out shadow-md">
            Back to Dashboard
        </a>
    </div>
@endsection
