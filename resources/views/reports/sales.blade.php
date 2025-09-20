{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    <div class="container mx-auto p-4 md:p-4">--}}
{{--        <div class="container mx-auto p-4 rounded-xl shadow-2xl mt-10 relative max-w-6xl">--}}
{{--            <h1 class="text-3xl font-bold mb-6 text-gray-800">Sales Report</h1>--}}

{{--            <div class="bg-white rounded-xl shadow-lg p-2 mb-8">--}}
{{--                <form action="{{ route('reports.sales') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 items-end">--}}
{{--                    <div>--}}
{{--                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>--}}
{{--                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">--}}
{{--                    </div>--}}
{{--                    <div>--}}
{{--                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>--}}
{{--                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">--}}
{{--                    </div>--}}
{{--                    <div class="flex items-end space-x-2">--}}
{{--                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200">--}}
{{--                            <i class="fas fa-filter mr-2"></i>Filter--}}
{{--                        </button>--}}
{{--                        @if(request()->has('start_date') || request()->has('end_date'))--}}
{{--                            <a href="{{ route('reports.sales') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200">--}}
{{--                                <i class="fas fa-undo-alt mr-2"></i>Reset--}}
{{--                            </a>--}}
{{--                        @endif--}}
{{--                        <a href="{{ route('reports.sales', array_merge(request()->query(), ['download' => 'true'])) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200">--}}
{{--                            <i class="fas fa-download mr-2"></i>Download CSV--}}
{{--                        </a>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}

{{--            <div class="bg-white rounded-xl shadow-lg overflow-hidden">--}}
{{--                <div class="px-6 py-4 border-b border-gray-200">--}}
{{--                    <h2 class="text-xl font-semibold text-gray-800">Detailed Sales Transactions</h2>--}}
{{--                </div>--}}
{{--                <div class="overflow-x-auto">--}}
{{--                    <table class="min-w-full divide-y divide-gray-200">--}}
{{--                        <thead class="bg-gray-50">--}}
{{--                        <tr>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Price</th>--}}
{{--                            <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Cost</th>--}}
{{--                            <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>--}}
{{--                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>--}}
{{--                        </tr>--}}
{{--                        </thead>--}}
{{--                        <tbody class="bg-white divide-y divide-gray-200">--}}
{{--                        @forelse($sales as $sale)--}}
{{--                            @foreach($sale->saleItems as $item)--}}
{{--                                <tr>--}}
{{--                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->id }}</td>--}}
{{--                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</td>--}}
{{--                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->customer_name }}</td>--}}
{{--                                    <td class="px-2 py-4 text-sm text-gray-900">--}}
{{--                                        @if($item->phone)--}}
{{--                                            <div class="font-medium text-gray-900">{{ $item->phone->brand->name ?? 'N/A' }} {{ $item->phone->model }}</div>--}}
{{--                                            <div class="text-gray-500">IMEI: {{ $item->phone->imei }}</div>--}}
{{--                                        @elseif($item->accessory)--}}
{{--                                            {{ $item->accessory->name }}--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($item->price, 2) }}</td>--}}
{{--                                    <td class="hidden md:table-cell px-2 py-4 whitespace-nowrap text-sm text-gray-500">${{ number_format($item->phone->purchase_price ?? $item->accessory->cost_price ?? 0, 2) }}</td>--}}
{{--                                    <td class="hidden md:table-cell px-2 py-4 whitespace-nowrap text-sm font-semibold @if(($item->price - ($item->phone->purchase_price ?? $item->accessory->cost_price ?? 0)) > 0) text-green-600 @else text-red-600 @endif">--}}
{{--                                        ${{ number_format($item->price - ($item->phone->purchase_price ?? $item->accessory->cost_price ?? 0), 2) }}--}}
{{--                                    </td>--}}
{{--                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">--}}
{{--                                        @if($sale->is_installment)--}}
{{--                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Installment</span>--}}
{{--                                        @else--}}
{{--                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Full Payment</span>--}}
{{--                                        @endif--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                            @endforeach--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="8" class="px-2 py-4 text-center text-sm text-gray-500">No sales found for the selected date range.</td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                        </tbody>--}}
{{--                    </table>--}}
{{--                </div>--}}
{{--                <div class="p-4">--}}
{{--                    {{ $sales->appends(request()->query())->links() }}--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--@endsection--}}


@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <div class="container mx-auto p-8 rounded-xl shadow-2xl mt-10 relative max-w-6xl">
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Sales Report</h1>

            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <form action="{{ route('reports.sales') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    <div class="col-span-1">
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-1">
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div class="col-span-1 sm:col-span-2 lg:col-span-2 flex flex-wrap gap-2 items-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 w-full sm:w-auto">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        @if(request()->has('start_date') || request()->has('end_date'))
                            <a href="{{ route('reports.sales') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 w-full sm:w-auto">
                                <i class="fas fa-undo-alt mr-2"></i>Reset
                            </a>
                        @endif
                        <a href="{{ route('reports.sales', array_merge(request()->query(), ['download' => 'true'])) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-2 rounded-lg shadow-md transition-colors duration-200 w-full sm:w-auto">
                            <i class="fas fa-download mr-2"></i>Download CSV
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Detailed Sales Transactions</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Price</th>
                            <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Cost</th>
                            <th scope="col" class="hidden md:table-cell px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Type</th>
                            <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Option</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sales as $sale)
                            @foreach($sale->saleItems as $item)
                                <tr>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-900">{{ $sale->id }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->customer_name }}</td>
                                    <td class="px-2 py-4 text-sm text-gray-900">
                                        @if($item->phone)
                                            <div class="font-medium text-gray-900">{{ $item->phone->brand->name ?? 'N/A' }} {{ $item->phone->model }}</div>
                                            <div class="text-gray-500">IMEI: {{ $item->phone->imei }}</div>
                                        @elseif($item->accessory)
                                            {{ $item->accessory->name }}
                                        @endif
                                    </td>
                                   <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ number_format($item->unit_price, 2) }}
                                    </td>

                                    <td class="hidden md:table-cell px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($item->phone)
                                            {{ number_format(($item->phone->purchase_price * $item->quantity) ?? 0, 2) }}
                                        @elseif($item->accessory)
                                            {{ number_format(($item->accessory->purchase_price * $item->quantity) ?? 0, 2) }}
                                        @endif
                                    </td>

                                    {{-- Profit Column --}}
                                    <td class="hidden md:table-cell px-2 py-4 whitespace-nowrap text-sm 
                                        {{ 
                                            ($item->phone && (($item->unit_price - $item->phone->purchase_price) * $item->quantity) > 0) || 
                                            ($item->accessory && (($item->unit_price - $item->accessory->cost_price) * $item->quantity) > 0) 
                                                ? 'text-green-600 font-semibold' 
                                                : 'text-red-600 font-semibold' 
                                        }}">
                                        @if($item->phone)
                                            {{ number_format((($item->unit_price - $item->phone->purchase_price) * $item->quantity) ?? 0, 2) }}
                                        @elseif($item->accessory)
                                            {{ number_format((($item->unit_price - $item->accessory->cost_price) * $item->quantity) ?? 0, 2) }}
                                        @endif
</td>

                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($sale->is_installment)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Installment</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Full Payment</span>
                                        @endif
                                    </td>
                                    <td class="px-2 py-4 whitespace-nowrap text-sm text-gray-500">{{ $sale->payment_option }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="8" class="px-2 py-4 text-center text-sm text-gray-500">No sales found for the selected date range.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4">
                    {{ $sales->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
@endsection
