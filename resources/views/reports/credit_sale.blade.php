@extends('layouts.app')

@section('title', 'Credit Sales Report')
@section('subtitle', 'View all sales with an outstanding balance.')

@section('content')
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Credit Sales Report</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($creditSales->isEmpty())
            <p class="text-center text-gray-600">No credit sales with an outstanding balance found.</p>
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
                                    <span class="ml-2">Final Amount</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="3">
                                    <span class="ml-2">Amount Paid</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="4">
                                    <span class="ml-2">Amount Due</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="5">
                                    <span class="ml-2">Sale Date</span>
                                </label>
                                <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <input type="checkbox" checked class="column-toggle" data-column="6">
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
                <table id="creditSalesTable" class="w-full table-fixed divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale ID</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Final Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($creditSales as $sale)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->id }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->customer_name }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->final_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->amount_paid, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($sale->amount_due, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm font-medium">
                                <a href="{{ route('sales.show', $sale->id) }}" class="text-indigo-600 hover:text-indigo-900">View Details</a>
                                <a href="{{ route('sales.pay.form', $sale->id) }}" class="ml-4 text-green-600 hover:text-green-900">Record Payment</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
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
                const table = $('#creditSalesTable').DataTable({
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
                                columns: [0, 1, 2, 3, 4, 5] // Columns to export
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
