@extends('layouts.app')

@section('title', 'All Sales')
@section('subtitle', 'View all recorded sales transactions.')

@section('content')
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md relative">

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">All Sales</h1>

        @can('create sales')
            <div class="flex justify-end mb-4">
                <a href="{{ route('sales.return') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Return sale
                </a>

                <a href="{{ route('sales.create') }}" class="bg-gray-700 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    Create New Sale
                </a>
{{--                <a href="{{ route('sales.print') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">--}}
{{--                    Print Receipt--}}
{{--                </a>--}}
            </div>
        @endcan

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($sales->isEmpty())
            <p class="text-center text-gray-600">No sales recorded yet. Start by creating a new sale!</p>
        @else
            <div class="mb-4 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                <!-- DataTables custom search input -->
                <div class="w-full md:w-1/3">
                    <input type="search" id="customSearchInput" class="form-input w-full rounded-md shadow-sm border border-gray-300 p-2" placeholder="Search sales...">
                </div>

                <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
                    <!-- Column Visibility Dropdown -->
                    <div class="relative inline-block text-left">
                        <button type="button" id="columnToggleBtn" class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Columns
                            <svg class="-mr-1 ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="columnToggleMenu" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 divide-y divide-gray-100 hidden z-10" role="menu" aria-orientation="vertical" aria-labelledby="columnToggleBtn">
                            <div class="py-1" role="none">
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="0">
                                    <span class="ml-2">Sale ID</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="1">
                                    <span class="ml-2">Customer Name</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="2">
                                    <span class="ml-2">Items Sold</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="3">
                                    <span class="ml-2">Final Amount</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="4">
                                    <span class="ml-2">Amount Paid</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="5">
                                    <span class="ml-2">Amount Due</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="6">
                                    <span class="ml-2">Sale Date</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="7">
                                    <span class="ml-2">Type</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="8">
                                    <span class="ml-2">Actions</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Download Button -->
                    <button id="downloadBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out shadow-md">
                        Download CSV
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table id="salesTable" class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items Sold</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($sales as $sale)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->customer_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <ul class="list-disc list-inside">
                                    @foreach ($sale->saleItems as $item)
                                        @if ($item->cashews)
                                            <li>{{ $item->cashews->product->name ?? 'N/A' }} (Qty: {{ $item->quantity }})</li>
                                        @else
                                            <li>Unknown Item (Qty: {{ $item->quantity }})</li>
                                        @endif
                                    @endforeach
                                </ul>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->final_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->amount_paid, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->amount_due, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($sale->is_installment) bg-yellow-100 text-yellow-800
                                    @elseif($sale->amount_due > 0) bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    @if($sale->is_installment)
                                        Installment
                                    @elseif($sale->amount_due > 0)
                                        Credit Sale
                                    @else
                                        Full Payment
                                    @endif
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('sales.show', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition-colors duration-200">View</a>
                                    <a href="{{ route('sales.print', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold text-sm transition-colors duration-200">receipt</a>
                                    @if ($sale->amount_due > 0 && !$sale->is_installment)
                                        <a href="{{ route('sales.pay.form', $sale->id) }}" class="text-green-600 hover:text-green-900 font-semibold text-sm transition-colors duration-200">Pay</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <!-- Pagination will be handled by DataTables so we can remove the blade pagination -->
            </div>
        @endif
    </div>

    @push('scripts')
        <!-- DataTables CDN -->
{{--        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>--}}
{{--        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">--}}
{{--        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>--}}
{{--        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">--}}
{{--        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>--}}
{{--        <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>--}}
{{--        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>--}}
{{--        <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.colVis.min.js"></script>--}}

        <script src="{{ asset('vendor/datatables/jquery-3.5.1.min.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/jquery.dataTables.css') }}">
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/buttons.dataTables.min.css') }}">
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/jszip.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/buttons.html5.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/buttons.colVis.min.js') }}"></script>

        <script>
            $(document).ready(function () {
                const table = $('#salesTable').DataTable({
                    // Disable DataTables' default search and pagination
                    paging: false,
                    searching: true,
                    info: false,
                    responsive: true,
                    buttons: [
                        {
                            extend: 'csvHtml5',
                            text: 'Download CSV',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7] // Columns to export
                            }
                        },
                        {
                            extend: 'colvis',
                            text: 'Columns'
                        }
                    ]
                });

                // Custom search box functionality
                $('#customSearchInput').on('keyup', function () {
                    table.search(this.value).draw();
                });

                // Column visibility toggle dropdown functionality
                $('#columnToggleBtn').on('click', function() {
                    $('#columnToggleMenu').toggleClass('hidden');
                });
                $(document).on('click', function(e) {
                    if (!$(e.target).closest('#columnToggleBtn, #columnToggleMenu').length) {
                        $('#columnToggleMenu').addClass('hidden');
                    }
                });

                $('.column-toggle').on('change', function () {
                    const columnIdx = $(this).data('column');
                    const column = table.column(columnIdx);
                    column.visible(!column.visible());
                });

                // Custom download button functionality
                $('#downloadBtn').on('click', function() {
                    table.button('.buttons-csv').trigger();
                });
            });
        </script>
    @endpush
@endsection
