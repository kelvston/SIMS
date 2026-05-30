@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Stock Report</h1>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form action="{{ route('reports.stock') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="product_id" class="block text-sm font-medium text-gray-700">Filter by Product</label>
                    <select name="product_id" id="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" @if(request('product_id') == $product->id) selected @endif>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end space-x-2 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if(request()->has('product_id'))
                        <a href="{{ route('reports.stock') }}" class="w-full md:w-auto bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                            <i class="fas fa-undo-alt mr-2"></i>Reset
                        </a>
                    @endif
                    <a href="{{ route('reports.stock', array_merge(request()->query(), ['download' => 'true'])) }}" class="w-full md:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-download mr-2"></i>Download CSV
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <!-- Stock Summary Cards (Circle Design) -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-8 justify-center text-center">
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-indigo-500">
                <p class="text-sm font-medium text-gray-500 text-center">Stock Items</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalStockItems) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-emerald-500">
                <p class="text-sm font-medium text-gray-500 text-center">Stock Value</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalStockValue, 2) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-orange-500">
                <p class="text-sm font-medium text-gray-500 text-center">Low Stock</p>
                <p class="mt-2 text-xl font-bold text-red-600">{{ number_format($lowStockCount) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-purple-500">
                <p class="text-sm font-medium text-gray-500 text-center">Products</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalMedicineItems) }}</p>
            </div>

        </div>


        <!-- Two-column layout for tables -->
{{--        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">--}}
            <!-- Medicines Stock Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Product Stock Levels</h2>
                    <input type="text" id="medicineSearchInput" onkeyup="renderTable('medicineTable')" placeholder="Search products..." class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 table-auto" id="medicineTable">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 0)">Product <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 1)">Size / Color <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 2)">Quantity <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 3)">Cost Value <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 4)">Selling Price <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 5)">Profit <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 6)">% profit <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('medicineTable', 7)">Received By <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($medicineStock as $medicine)
                            <tr data-id="{{ $medicine->id }}">
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $medicine->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    @if($medicine->productSizes->isNotEmpty())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($medicine->productSizes as $variant)
                                                <span class="inline-flex rounded-full bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">
                                                    {{ $variant->size }} / {{ $variant->color }}: {{ $variant->quantity }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $medicine->quantity }}">{{ $medicine->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $medicine->unit_price * $medicine->quantity }}">{{ number_format($medicine->unit_price * $medicine->quantity, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $medicine->selling_price * $medicine->quantity  }}">{{ number_format($medicine->selling_price * $medicine->quantity , 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $medicine->selling_price * $medicine->quantity  - $medicine->unit_price * $medicine->quantity }}">{{ number_format($medicine->selling_price * $medicine->quantity  - $medicine->unit_price * $medicine->quantity, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $medicine->unit_price > 0 ? (($medicine->selling_price - $medicine->unit_price) / $medicine->unit_price) * 100 : 0 }}">
                                    {{ $medicine->unit_price > 0 ? number_format((($medicine->selling_price - $medicine->unit_price) / $medicine->unit_price) * 100, 1) : '0.0' }}%
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $medicine->receivedBy->name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <button onclick="editQuantity('medicineTable', this.closest('tr'), '{{ $medicine->id }}')" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-3 text-center text-sm text-gray-500">No product stock found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="medicinePagination" class="px-6 py-3 border-t border-gray-200"></div>
            </div>
<hr>
<hr>
<hr>
<hr>
            <!-- Cosmetics Stock Table -->

        </div>
{{--    </div>--}}

    <!-- Custom Modal for editing quantity and messages -->
    <div id="customModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center z-50">
        <div class="relative p-8 bg-white w-96 max-w-sm m-auto flex-col flex rounded-lg shadow-xl">
            <div id="modalContent">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center" id="modalTitle"></h3>
                <p class="text-sm text-gray-500 mb-4 text-center" id="modalMessage"></p>
                <!-- Form for backend submission -->
                <form id="stockAdjustmentForm" class="hidden">
                    @csrf
                    <input type="hidden" name="id" id="stockItemIdInput">
                    <div class="mb-4">
                        <label for="newQuantityInput" class="block text-sm font-medium text-gray-700">New Quantity</label>
                        <input type="number" name="new_quantity" id="newQuantityInput" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Enter new quantity">
                        <p id="modalError" class="text-red-500 text-xs mt-2 hidden">Quantity must be a non-negative number.</p>
                    </div>
                    <div class="mb-4">
                        <label for="adjustmentComment" class="block text-sm font-medium text-gray-700">Reason for Adjustment</label>
                        <textarea name="comment" id="adjustmentComment" rows="3" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., received new shipment, item damaged"></textarea>
                    </div>
                </form>
            </div>
            <div id="modalButtons" class="flex justify-end space-x-4 mt-6">
                <!-- Buttons will be added dynamically by JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Store current sort state for each table
        let sortState = {
            medicineTable: { column: -1, direction: 1 }
        };

        // Pagination and filter variables
        const itemsPerPage = 5;
        let medicineTableCurrentPage = 1;
        let medicineTableAllRows = [];
        let currentEditingRow = null;

        function renderTable(tableId) {
            const table = document.getElementById(tableId);
            const inputId = tableId.replace('Table', 'SearchInput');
            const searchInput = document.getElementById(inputId);
            const paginationContainer = document.getElementById(tableId.replace('Table', 'Pagination'));

            if (!table || !table.tBodies.length || !searchInput || !paginationContainer) {
                return;
            }

            const filterText = searchInput.value.toLowerCase();
            const allRows = medicineTableAllRows;

            // Filter the rows based on the search input
            const filteredRows = allRows.filter(row => {
                const textContent = row.textContent || row.innerText;
                return textContent.toLowerCase().indexOf(filterText) > -1;
            });

            // Update pagination and display filtered results
            const currentPage = medicineTableCurrentPage;

            const totalRows = filteredRows.length;
            const totalPages = Math.ceil(totalRows / itemsPerPage);

            // Hide all rows in the original table body
            Array.from(table.tBodies[0].rows).forEach(row => row.style.display = 'none');

            // Display only the rows for the current page
            const start = (currentPage - 1) * itemsPerPage;
            const end = start + itemsPerPage;
            filteredRows.slice(start, end).forEach(row => row.style.display = '');

            // Update pagination controls
            paginationContainer.innerHTML = '';
            if (totalPages > 1) {
                const prevButton = document.createElement('button');
                prevButton.textContent = 'Previous';
                prevButton.classList.add('px-3', 'py-1', 'rounded-md', 'mr-2', 'bg-gray-200', 'hover:bg-gray-300', 'disabled:opacity-50');
                prevButton.disabled = currentPage === 1;
                prevButton.onclick = () => {
                    medicineTableCurrentPage--;
                    renderTable(tableId);
                };

                const nextButton = document.createElement('button');
                nextButton.textContent = 'Next';
                nextButton.classList.add('px-3', 'py-1', 'rounded-md', 'bg-gray-200', 'hover:bg-gray-300', 'disabled:opacity-50');
                nextButton.disabled = currentPage === totalPages;
                nextButton.onclick = () => {
                    medicineTableCurrentPage++;
                    renderTable(tableId);
                };

                paginationContainer.appendChild(prevButton);
                paginationContainer.appendChild(nextButton);
            }
        }

        // Sort function
        function sortTable(tableId, column) {
            const table = document.getElementById(tableId);
            if (!table || !sortState[tableId]) {
                return;
            }

            const allRows = medicineTableAllRows;

            // Determine sort direction
            const isAscending = sortState[tableId].column !== column || sortState[tableId].direction === -1;
            sortState[tableId] = { column, direction: isAscending ? 1 : -1 };

            // Sort the rows array in-place
            allRows.sort((a, b) => {
                const cellA = a.cells[column];
                const cellB = b.cells[column];
                if (!cellA || !cellB) {
                    return 0;
                }

                // Check for a data-value attribute, otherwise use text content
                const valA = cellA.dataset.value ? parseFloat(cellA.dataset.value) : cellA.innerText.trim();
                const valB = cellB.dataset.value ? parseFloat(cellB.dataset.value) : cellB.innerText.trim();

                const isNumeric = !isNaN(valA) && !isNaN(valB);

                if (isNumeric) {
                    return (valA - valB) * sortState[tableId].direction;
                } else {
                    return valA.localeCompare(valB) * sortState[tableId].direction;
                }
            });

            // Re-render the table with the newly sorted data
            medicineTableCurrentPage = 1;
            renderTable(tableId);
        }

        function editQuantity(tableId, rowElement, rowId = null) {
            currentEditingRow = rowElement;
            const modal = document.getElementById('customModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const stockAdjustmentForm = document.getElementById('stockAdjustmentForm');
            const modalButtons = document.getElementById('modalButtons');
            const newQuantityInput = document.getElementById('newQuantityInput');
            const adjustmentComment = document.getElementById('adjustmentComment');
            const stockItemIdInput = document.getElementById('stockItemIdInput');
            const modalError = document.getElementById('modalError');

            // Reset modal state
            modalError.classList.add('hidden');
            modalMessage.classList.remove('hidden');
            stockAdjustmentForm.classList.add('hidden');

            const quantityCell = rowElement.cells[2];
            const currentQuantity = parseInt(quantityCell.textContent.trim(), 10);
            const itemName = rowElement.cells[0].textContent.trim();
            const itemId = rowId || rowElement.dataset.id;

            modalTitle.textContent = `Edit Quantity for ${itemName}`;
            modalMessage.textContent = 'Please enter the new quantity and a reason for the change.';
            newQuantityInput.value = currentQuantity;
            adjustmentComment.value = '';
            stockItemIdInput.value = itemId;
            stockAdjustmentForm.classList.remove('hidden');

            modalButtons.innerHTML = `
        <button type="button" onclick="cancelEdit()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
            Cancel
        </button>
        <button type="button" onclick="saveQuantity()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
            Save
        </button>
    `;

            modal.classList.remove('hidden');
        }
        // Function to save the new quantity and comment to the backend
        async function saveQuantity() {
            const newQuantityInput = document.getElementById('newQuantityInput');
            const adjustmentComment = document.getElementById('adjustmentComment');
            const modalError = document.getElementById('modalError');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalButtons = document.getElementById('modalButtons');
            const stockAdjustmentForm = document.getElementById('stockAdjustmentForm');

            const newQuantity = parseInt(newQuantityInput.value, 10);
            const comment = adjustmentComment.value;
            const stockItemId = document.getElementById('stockItemIdInput').value;

            // Simple client-side validation
            if (isNaN(newQuantity) || newQuantity < 0) {
                modalError.classList.remove('hidden');
                return;
            }

            modalError.classList.add('hidden');
            modalTitle.textContent = 'Updating...';
            modalMessage.textContent = 'Please wait while the stock is being updated.';
            stockAdjustmentForm.classList.add('hidden');
            modalButtons.innerHTML = ''; // Hide buttons during submission

            try {
                const response = await fetch('/reports/stock/update-quantity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        // 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')

                    },
                    body: JSON.stringify({
                        id: stockItemId,
                        new_quantity: newQuantity,
                        comment: comment
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    // Find the table row and update the values
                    const row = document.querySelector(`#medicineTable tr[data-id="${stockItemId}"]`);
                    if (row) {
                        const quantityCell = row.cells[2];
                        const oldQuantity = parseInt(quantityCell.dataset.value || quantityCell.textContent.trim(), 10);
                        const oldTotalCost = parseFloat(row.cells[3].dataset.value || 0);
                        const oldTotalSelling = parseFloat(row.cells[4].dataset.value || 0);
                        const costPerItem = oldQuantity > 0 ? oldTotalCost / oldQuantity : 0;
                        const sellingPricePerItem = oldQuantity > 0 ? oldTotalSelling / oldQuantity : 0;

                        quantityCell.dataset.value = newQuantity;
                        quantityCell.textContent = newQuantity;

                        const totalCostCell = row.cells[3];
                        const newTotalCost = costPerItem * newQuantity;
                        totalCostCell.dataset.value = newTotalCost;
                        totalCostCell.textContent = newTotalCost.toFixed(2);

                        const totalSellingCell = row.cells[4];
                        const newTotalSelling = sellingPricePerItem * newQuantity;
                        totalSellingCell.dataset.value = newTotalSelling;
                        totalSellingCell.textContent = newTotalSelling.toFixed(2);

                        const profitCell = row.cells[5];
                        const newProfit = newTotalSelling - newTotalCost;
                        profitCell.dataset.value = newProfit;
                        profitCell.textContent = newProfit.toFixed(2);

                        const profitPercentCell = row.cells[6];
                        const newProfitPercent = costPerItem > 0 ? ((sellingPricePerItem - costPerItem) / costPerItem) * 100 : 0;
                        profitPercentCell.dataset.value = newProfitPercent;
                        profitPercentCell.textContent = `${newProfitPercent.toFixed(1)}%`;

                        renderTable('medicineTable');
                    }

                    modalTitle.textContent = 'Success!';
                    modalMessage.innerHTML = result.message;
                    modalButtons.innerHTML = `
                        <button type="button" onclick="cancelEdit()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                            Close
                        </button>
                    `;
                } else {
                    modalTitle.textContent = 'Error!';
                    modalMessage.textContent = result.message || 'An unexpected error occurred.';
                    modalButtons.innerHTML = `
                        <button type="button" onclick="cancelEdit()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                            Close
                        </button>
                    `;
                }
            } catch (error) {
                console.error('Error during fetch:', error);
                modalTitle.textContent = 'Error!';
                modalMessage.textContent = 'Failed to connect to the server. Please try again.';
                modalButtons.innerHTML = `
                    <button type="button" onclick="cancelEdit()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        Close
                    </button>
                `;
            }
        }

        // Function to cancel the edit and close the modal
        function cancelEdit() {
            const modal = document.getElementById('customModal');
            modal.classList.add('hidden');
            currentEditingRow = null;
        }

        // Initial setup on window load
        window.onload = function() {
            const medicineTable = document.getElementById('medicineTable');
            if (medicineTable && medicineTable.tBodies.length) {
                medicineTableAllRows = Array.from(medicineTable.tBodies[0].rows);
            }

            renderTable('medicineTable');
        };
    </script>
@endsection
