@extends('layouts.app')

@section('title', 'Customers Report')
@section('subtitle', 'View all sales with an outstanding balance.')

@section('content')
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Customers Report</h1>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($customers->isEmpty())
            <p class="text-center text-gray-600">No customers found.</p>
        @else
            <div class="mb-4 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                <div class="w-full md:w-1/3">
                    <input type="search" id="customSearchInput" class="form-input w-full rounded-md shadow-sm border border-gray-300 p-2" placeholder="Search customers...">
                </div>

                <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
                    <button id="downloadBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out shadow-md">
                        Download CSV
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                <table id="customersTable" class="w-full table-fixed divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sales (date → items)</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($customers as $customer)
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ $customer->customer_name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $customer->customer_phone }}</td>
                            <td class="px-4 py-3 text-sm">{{ $customer->customer_email }}</td>
                            <td class="px-4 py-3 text-sm align-top">
                                <ul class="list-disc pl-5 space-y-2">
                                    @foreach ($customer->sales as $sale)
                                        <li>
                                            <div class="font-semibold text-sm">{{ $sale['date'] }} — Sale #{{ $sale['id'] }}</div>

                                            @if (!empty($sale['cashews']))
                                                <div class="text-xs">Phones: {{ implode(', ', $sale['cashews']) }}</div>
                                            @endif

                                            @if (!empty($sale['accessories']))
                                                <div class="text-xs">Accessories: {{ implode(', ', $sale['accessories']) }}</div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('styles')
        <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/jquery.dataTables.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('vendor/datatables/buttons.dataTables.min.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('vendor/datatables/jquery-3.5.1.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/jszip.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/buttons.html5.min.js') }}"></script>
        <script type="text/javascript" charset="utf8" src="{{ asset('vendor/datatables/buttons.colVis.min.js') }}"></script>

        <script>
            $(document).ready(function () {
                const table = $('#customersTable').DataTable({
                    paging: false,
                    searching: true,
                    info: false,
                    responsive: true,
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'csvHtml5',
                            text: 'Download CSV',
                            exportOptions: {
                                columns: ':visible'
                            }
                        }
                    ]
                });

                $('#customSearchInput').on('keyup', function () {
                    table.search(this.value).draw();
                });

                $('#downloadBtn').on('click', function() {
                    table.button('.buttons-csv').trigger();
                });
            });
        </script>
    @endpush
@endsection
