@extends('layouts.app')

@section('title', 'All Sales')
@section('subtitle', 'View all recorded sales transactions.')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md relative"> <!-- Added relative -->
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" /> <!-- Centered watermark -->

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">All Sales</h1>

        @can('create sales')
            <div class="flex justify-end mb-4">
                <a href="{{ route('sales.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Create New Sale
                </a>
            </div>
        @endcan

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Warning!</strong>
                <span class="block sm:inline">{{ session('warning') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($creditReminderSales->isNotEmpty())
            <div class="bg-amber-50 border border-amber-300 text-amber-900 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Credit sale reminders:</strong>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($creditReminderSales as $creditSale)
                        <li>
                            Sale #{{ $creditSale->id }} for {{ $creditSale->customer_name }}
                            has Tsh {{ number_format($creditSale->amount_due, 2) }} due on {{ $creditSale->credit_due_date->format('Y-m-d') }}.
                            <a href="{{ route('sales.show', $creditSale->id) }}" class="font-semibold underline">View</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($sales->isEmpty())
            <p class="text-center text-gray-600">No sales recorded yet. Start by creating a new sale!</p>
        @else
            <div class="table-scroll sm:rounded-lg sm:border sm:border-gray-200 sm:shadow-sm">
                <table class="responsive-table min-w-full divide-y divide-gray-200 table-auto"> <!-- Added table-auto -->
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Sold</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($sales as $sale)
                        <tr class="{{ $sale->is_voided ? 'bg-red-50' : '' }}">
                            <td data-label="Sale ID" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sale->id }}</td>
                            <td data-label="Customer Name" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sale->customer_name }}</td>
                            <td data-label="Items Sold" class="px-4 py-3 text-sm text-gray-900">
                                <ul class="list-disc list-inside">
                                    @foreach ($sale->saleItems as $item)
                                        @if($item->phone)
                                            <li>{{ $item->phone->brand->name ?? 'N/A' }} {{ $item->phone->model }} {{ $item->phone->storage_capacity }} (IMEI: {{ $item->phone->imei }})</li>
                                        @elseif($item->product)
                                            <li>{{ $item->product->name }} x {{ $item->quantity }}</li>
                                        @else
                                            <li>Item removed</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td data-label="Final Amount" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">Tsh {{ number_format($sale->final_amount, 2) }}</td>
                            <td data-label="Sale Date" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                            <td data-label="Type" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($sale->is_credit) bg-purple-100 text-purple-800
                                    @elseif($sale->is_installment) bg-yellow-100 text-yellow-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    {{ $sale->sale_type_label }}
                                </span>
                                @if($sale->is_credit && $sale->credit_due_date)
                                    <div class="text-xs mt-1
                                        @if($sale->credit_reminder_status === 'overdue') text-red-700
                                        @elseif($sale->credit_reminder_status === 'due_soon') text-amber-700
                                        @else text-gray-500 @endif">
                                        Due {{ $sale->credit_due_date->format('Y-m-d') }}
                                        @if((float) $sale->amount_due > 0)
                                            - Tsh {{ number_format($sale->amount_due, 2) }}
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td data-label="Status" class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                @if($sale->is_voided)
                                    <div class="space-y-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Voided</span>
                                        <div class="text-xs text-gray-600">
                                            {{ optional($sale->voided_at)->format('Y-m-d H:i') }}
                                            @if($sale->voidedBy)
                                                by {{ $sale->voidedBy->name }}
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                @endif
                            </td>
                            <td data-label="Actions" class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
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
    </div>
@endsection
