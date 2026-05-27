@extends('layouts.app')

@section('title', 'Stock Adjustments Report')
@section('subtitle', 'View all stock adjustments history.')

@section('content')
    <div class="container mx-auto bg-white p-6 rounded-lg shadow-md relative">
        {{-- Optional watermark --}}
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-10 w-96 z-0"
             style="transform: translate(-50%, -50%);" />

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Stock Adjustments Report</h1>

        @if ($stock_adjustment->isEmpty())
            <p class="text-center text-gray-600">No stock adjustments found.</p>
        @else
            <div class="mb-4 flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0">
                <div class="w-full md:w-1/3">
                    <input type="search" id="customSearchInput" class="form-input w-full rounded-md shadow-sm border border-gray-300 p-2" placeholder="Search adjustments...">
                </div>
                <div class="flex items-center space-x-2 w-full md:w-auto justify-end">
                    <button id="downloadBtn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-md transition duration-300 ease-in-out shadow-md">
                        Download CSV
                    </button>
                </div>
            </div>

            <div class="overflow-x-hidden rounded-lg border border-gray-200 shadow-sm">
                <table id="adjustmentsTable" class="w-full table-auto divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 w-28 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase">Adjusted By</th>
                        <th class="px-4 py-3 w-48 text-left text-xs font-medium text-gray-500 uppercase break-words">Email</th>
                        <th class="px-4 py-3 w-32 text-left text-xs font-medium text-gray-500 uppercase whitespace-nowrap">Phone Number</th>
                        <th class="px-4 py-3 w-48 text-left text-xs font-medium text-gray-500 uppercase">Accessory / Phone</th>
                        <th class="px-4 py-3 w-28 text-left text-xs font-medium text-gray-500 uppercase">Old Quantity</th>
                        <th class="px-4 py-3 w-28 text-left text-xs font-medium text-gray-500 uppercase">New Quantity</th>
                        <th class="px-4 py-3 w-40 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                        <th class="px-4 py-3 w-28 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($stock_adjustment as $adjustment)
                        @php
                            if ($adjustment->phone_id) {
                                $status = 'removed';
                            } elseif ($adjustment->accessory_id) {
                                if ($adjustment->old_quantity < $adjustment->new_quantity) {
                                    $status = 'added';
                                } elseif ($adjustment->old_quantity > $adjustment->new_quantity) {
                                    $status = 'reduced';
                                } else {
                                    $status = '';
                                }
                            } else {
                                $status = '';
                            }

                            $badgeClasses = [
                                'added' => 'bg-green-100 text-green-800',
                                'reduced' => 'bg-yellow-100 text-yellow-800',
                                'removed' => 'bg-red-100 text-red-800',
                                '' => 'bg-gray-100 text-gray-600',
                            ];
                            $badgeClass = $badgeClasses[$status] ?? $badgeClasses[''];
                        @endphp
                        <tr>
                            <td class="px-4 py-3 text-sm">{{ \Carbon\Carbon::parse($adjustment->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adjustment->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm break-words">{{ $adjustment->email ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm whitespace-nowrap">{{ $adjustment->phone_number ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($adjustment->accessory)
                                    {{ $adjustment->accessory }}
                                @elseif($adjustment->model)
                                    {{ $adjustment->model }} (IMEI: {{ $adjustment->imei ?? '-' }})
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm">{{ $adjustment->old_quantity }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adjustment->new_quantity }}</td>
                            <td class="px-4 py-3 text-sm">{{ $adjustment->comment }}</td>
                            <td class="px-4 py-3 text-sm font-semibold">
                            <span class="inline-block px-3 py-1 text-xs font-semibold rounded-full {{ $badgeClass }}">
                                {{ ucfirst($status ?: 'N/A') }}
                            </span>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
        <link rel="stylesheet" href="{{ asset('vendor/datatables/jquery.dataTables.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/datatables/buttons.dataTables.min.css') }}">
        <script src="{{ asset('vendor/datatables/jquery-3.5.1.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/jquery.dataTables.js') }}"></script>
        <script src="{{ asset('vendor/datatables/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/jszip.min.js') }}"></script>
        <script src="{{ asset('vendor/datatables/buttons.html5.min.js') }}"></script>

        <script>
            $(document).ready(function () {
                const table = $('#adjustmentsTable').DataTable({
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
