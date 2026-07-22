@extends('layouts.app')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -50%);" />
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">All Inventory</h1>

{{--        @can('receive medicines')--}}
{{--            <div class="flex justify-end mb-8">--}}
{{--                <a href="{{ route('medicines.receive.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded-full transition duration-300 ease-in-out shadow-md">--}}
{{--                    Receive New Inventory--}}
{{--                </a>--}}
{{--                <a href="{{ route('reports.detailed-stock') }}" class="bg-gray-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded-full transition duration-300 ease-in-out shadow-md">--}}
{{--                    Stock Adjustment--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        @endcan--}}
        @can('receive medicines')
            <div class="flex justify-end mb-8 space-x-2">
                <a href="{{ route('medicines.receive.form') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    <i class="fas fa-box-open mr-2"></i> Receive New Inventory
                </a>
                <a href="{{ route('reports.detailed-stock') }}" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    <i class="fas fa-warehouse mr-2"></i> Stock Adjustment
                </a>
                <a href="{{ route('barcodes.generate.form') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                    <i class="fas fa-barcode mr-2"></i> Generate Barcodes
                </a>
            </div>
        @endcan

{{--        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">--}}
            <!-- Medicines Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Products</h2>
                <!-- Controls for Medicines Table -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                    <div class="relative w-full sm:w-1/2">
                        <input type="text" id="medicine-search-input" placeholder="Search medicines..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <div class="relative inline-block text-left">
                            <button id="medicine-columns-toggle" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-columns-2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M12 3v18"/></svg>
                                Columns
                            </button>
                            <div id="medicine-columns-menu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="medicine-columns-toggle">

                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="1" checked>
                                        <span class="ml-2">Product</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="5" checked>
                                        <span class="ml-2">Selling Price</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="6" checked>
                                        <span class="ml-2">Status</span>
                                    </label>

                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="7" checked>
                                        <span class="ml-2">Stock Origin</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="8" checked>
                                        <span class="ml-2">Description</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button id="download-medicines-pdf" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                            PDF
                        </button>
                    </div>
                </div>

                @if ($medicines->isEmpty())
                    <p class="text-center text-gray-600">No medicines found in inventory. Start by receiving new medicines!</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table id="medicines-table" class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                            <tr>
{{--                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="barcode">Barcode</th>--}}
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="product">Product</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="price">Selling Price</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="status">Status</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="stock_origin">Stock Origin</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="description">Description</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($medicines as $medicine)
                                <tr data-row>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="product">{{ optional($medicine->product)->name }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="price">{{ number_format($medicine->selling_price, 2) }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="status">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($medicine->status == 'available') bg-green-100 text-green-800
                                            @elseif($medicine->status == 'sold') bg-red-100 text-red-800
                                            @elseif($medicine->status == 'under_installment') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $medicine->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="stock_origin">{{ ($medicine->stock_origin)}}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="description">{{ ($medicine->description)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $medicines->links('pagination::tailwind') }}
                    </div>
                @endif
{{--            </div>--}}

            <!-- Accessories Section -->
            <div>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Accessories</h2>
                <!-- Controls for Accessories Table -->
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                    <div class="relative w-full sm:w-1/2">
                        <input type="text" id="accessory-search-input" placeholder="Search cosmetics..." class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto justify-end">
                        <div class="relative inline-block text-left">
                            <button id="accessory-columns-toggle" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-columns-2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M12 3v18"/></svg>
                                Columns
                            </button>
                            <div id="accessory-columns-menu" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="accessory-columns-toggle">
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="0" checked>
                                        <span class="ml-2">Name</span>
                                    </label>
{{--                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">--}}
{{--                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="1" checked>--}}
{{--                                        <span class="ml-2">Brand</span>--}}
{{--                                    </label>--}}
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="1" checked>
                                        <span class="ml-2">Unit</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="2" checked>
                                        <span class="ml-2">Purchase Price</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="3" checked>
                                        <span class="ml-2">Selling Price</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="4" checked>
                                        <span class="ml-2">Quantity</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="5" checked>
                                        <span class="ml-2">Status</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="6" checked>
                                        <span class="ml-2">Description</span>
                                    </label>
                                    <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <input type="checkbox" class="form-checkbox text-blue-600" data-column-index="7" checked>
                                        <span class="ml-2">Stock Origin</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button id="download-cosmetics-pdf" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-file-text"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                            PDF
                        </button>
                    </div>
                </div>

                @if ($cosmetics->isEmpty())
                    <p class="text-center text-gray-600">No cosmetics found in inventory.</p>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table id="cosmetics-table" class="min-w-full divide-y divide-gray-200 table-auto">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="name">Name</th>
{{--                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="product">Brand</th>--}}
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="unit">Unit</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="purchase_price">Purchase Price</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="selling_price">Selling Price</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="quantity">Quantity</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="status">Status</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="description">Description</th>
                                <th scope="col" class="px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" data-column="stock_origin">Stock Origin</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($cosmetics as $accessory)
                                <tr data-row>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="name">{{ $accessory->name }}</td>
{{--                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="product">{{ optional($accessory->product)->name ?? 'N/A' }}</td>--}}
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="unit">{{ $accessory->unit }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="purchase_price">{{ number_format($accessory->purchase_price, 2) }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="selling_price">{{ number_format($accessory->selling_price, 2) }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="quantity">{{ $accessory->quantity }}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="status">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($accessory->status == 'in_stock') bg-green-100 text-green-800
                                            @elseif($accessory->status == 'out_of_stock') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst(str_replace('_', ' ', $accessory->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="description">{{ ($accessory->description)}}</td>
                                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-900" data-column="stock_origin">{{ ($accessory->stock_origin)}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $cosmetics->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>

        </div> <!-- End of main grid container -->
    </div>

    <!-- jspdf and jspdf-autotable for client-side PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to handle search, column visibility, and PDF download for a given table
            function setupTableFeatures(tableId, searchInputId, columnsToggleId, columnsMenuId, downloadPdfButtonId) {
                const table = document.getElementById(tableId);
                const searchInput = document.getElementById(searchInputId);
                const columnsToggle = document.getElementById(columnsToggleId);
                const columnsMenu = document.getElementById(columnsMenuId);
                const downloadPdfButton = document.getElementById(downloadPdfButtonId);

                // --- Search Functionality ---
                searchInput.addEventListener('keyup', function() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        row.style.display = rowText.includes(searchTerm) ? '' : 'none';
                    });
                });

                // --- Column Visibility Toggle ---
                columnsToggle.addEventListener('click', function() {
                    columnsMenu.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!columnsToggle.contains(event.target) && !columnsMenu.contains(event.target)) {
                        columnsMenu.classList.add('hidden');
                    }
                });

                columnsMenu.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const columnIndex = this.getAttribute('data-column-index');
                        const headers = table.querySelectorAll('th');
                        const rows = table.querySelectorAll('tbody tr');

                        if (this.checked) {
                            headers[columnIndex].style.display = '';
                            rows.forEach(row => {
                                row.children[columnIndex].style.display = '';
                            });
                        } else {
                            headers[columnIndex].style.display = 'none';
                            rows.forEach(row => {
                                row.children[columnIndex].style.display = 'none';
                            });
                        }
                    });
                });

                // --- PDF Download Functionality ---
                downloadPdfButton.addEventListener('click', function() {
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();

                    // Get only the visible headers
                    const visibleHeaders = [];
                    const headers = table.querySelectorAll('thead th');
                    headers.forEach((header, index) => {
                        if (header.style.display !== 'none') {
                            visibleHeaders.push(header.textContent.trim());
                        }
                    });

                    // Get only the visible row data
                    const visibleData = [];
                    const rows = table.querySelectorAll('tbody tr');
                    rows.forEach(row => {
                        if (row.style.display !== 'none') {
                            const rowData = [];
                            row.querySelectorAll('td').forEach((cell, index) => {
                                if (headers[index].style.display !== 'none') {
                                    rowData.push(cell.textContent.trim());
                                }
                            });
                            visibleData.push(rowData);
                        }
                    });

                    doc.text(tableId.toUpperCase().replace('-', ' ') + ' Inventory Report', 14, 15);
                    doc.autoTable({
                        head: [visibleHeaders],
                        body: visibleData,
                        startY: 25,
                    });
                    doc.save(tableId + '-inventory.pdf');
                });
            }

            // Setup the features for both tables
            setupTableFeatures('medicines-table', 'medicine-search-input', 'medicine-columns-toggle', 'medicine-columns-menu', 'download-medicines-pdf');
            setupTableFeatures('cosmetics-table', 'accessory-search-input', 'accessory-columns-toggle', 'accessory-columns-menu', 'download-cosmetics-pdf');
        });
    </script>
@endsection
