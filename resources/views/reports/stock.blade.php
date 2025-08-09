@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Stock Report</h1>

        <!-- Filter Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <form action="{{ route('reports.stock') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                <div class="w-full md:w-auto">
                    <label for="brand_id" class="block text-sm font-medium text-gray-700">Filter by Brand (Phones)</label>
                    <select name="brand_id" id="brand_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Brands</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" @if(request('brand_id') == $brand->id) selected @endif>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end space-x-2 w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                    @if(request()->has('brand_id'))
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
                <p class="text-sm font-medium text-gray-500 text-center">Phones</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalPhoneItems) }}</p>
            </div>
            <div class="flex flex-col items-center justify-center bg-white rounded-full h-40 w-40 mx-auto shadow-lg border-4 border-blue-500">
                <p class="text-sm font-medium text-gray-500 text-center">Accessories</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($totalAccessoryItems) }}</p>
            </div>
        </div>


        <!-- Two-column layout for tables -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Phones Stock Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Phone Stock Levels</h2>
                    <input type="text" id="phoneSearchInput" onkeyup="renderTable('phoneTable')" placeholder="Search for phones..." class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 table-auto" id="phoneTable">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 0)">Brand <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 1)">Model <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 2)">Quantity <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 3)">Cost Value <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 4)">Selling Price <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('phoneTable', 5)">Profit <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($phoneStock as $phone)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $phone->brand->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $phone->model }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">{{ $phone->quantity }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $phone->total_purchase_price }}">{{ number_format($phone->total_purchase_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $phone->total_selling_price }}">{{ number_format($phone->total_selling_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $phone->total_selling_price - $phone->total_purchase_price }}">{{ number_format($phone->total_selling_price - $phone->total_purchase_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    <button onclick="editQuantity('phoneTable', this.closest('tr'))" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">No phone stock found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="phonePagination" class="px-6 py-3 border-t border-gray-200"></div>
            </div>

            <!-- Accessories Stock Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-800">Accessory Stock Levels</h2>
                    <input type="text" id="accessorySearchInput" onkeyup="renderTable('accessoryTable')" placeholder="Search for accessories..." class="px-3 py-2 rounded-md border border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full divide-y divide-gray-200 table-auto" id="accessoryTable">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('accessoryTable', 0)">Item <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('accessoryTable', 1)">Quantity <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('accessoryTable', 2)">Cost/Item <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('accessoryTable', 3)">Total Value <i class="fas fa-sort ml-1"></i></th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer sortable" onclick="sortTable('accessoryTable', 4)">Profit <i class="fas fa-sort ml-1"></i></th>
{{--                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>--}}
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($accessoryStock as $accessory)
                            <tr data-id="{{ $accessory->id }}">
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $accessory->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $accessory->quantity }}">{{ number_format($accessory->quantity) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $accessory->total_purchase_price }}">{{ number_format($accessory->total_purchase_price, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ $accessory->total_purchase_price * $accessory->quantity }}">{{ number_format($accessory->total_purchase_price * $accessory->quantity, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-500" data-value="{{ ($accessory->total_selling_price - $accessory->total_purchase_price) * $accessory->quantity }}">{{ number_format(($accessory->total_selling_price - $accessory->total_purchase_price) * $accessory->quantity, 2) }}</td>
{{--                                <td class="px-4 py-3 text-sm text-gray-500">--}}
{{--                                    <button onclick="editQuantity('accessoryTable', this.closest('tr'))" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">--}}
{{--                                        <i class="fas fa-pencil-alt"></i>--}}
{{--                                    </button>--}}
{{--                                </td>--}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-center text-sm text-gray-500">No accessory stock found.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                <div id="accessoryPagination" class="px-6 py-3 border-t border-gray-200"></div>
            </div>
        </div>
    </div>

    <!-- Custom Modal for editing quantity and messages -->
    <div id="customModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center z-50">
        <div class="relative p-8 bg-white w-96 max-w-sm m-auto flex-col flex rounded-lg shadow-xl">
            <div id="modalContent">
                <h3 class="text-xl font-semibold text-gray-900 mb-4 text-center" id="modalTitle"></h3>
                <p class="text-sm text-gray-500 mb-4 text-center" id="modalMessage"></p>
                <!-- Form for backend submission -->
                <form id="stockAdjustmentForm" class="hidden">
                    @csrf
                    <input type="hidden" name="id" id="accessoryIdInput">
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

    <!-- Link to Font Awesome for sort icons -->
{{--    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>--}}

    <script>
        // Store current sort state for each table
        let sortState = {
            phoneTable: { column: -1, direction: 1 },
            accessoryTable: { column: -1, direction: 1 }
        };

        // Pagination and filter variables
        const itemsPerPage = 5;
        let phoneTableCurrentPage = 1;
        let accessoryTableCurrentPage = 1;
        let phoneTableAllRows = [];
        let accessoryTableAllRows = [];
        let currentEditingRow = null;

        function renderTable(tableId) {
            const table = document.getElementById(tableId);
            const inputId = tableId.replace('Table', 'SearchInput');
            const filterText = document.getElementById(inputId).value.toLowerCase();
            const allRows = tableId === 'phoneTable' ? phoneTableAllRows : accessoryTableAllRows;

            // Filter the rows based on the search input
            const filteredRows = allRows.filter(row => {
                const textContent = row.textContent || row.innerText;
                return textContent.toLowerCase().indexOf(filterText) > -1;
            });

            // Update pagination and display filtered results
            const currentPage = tableId === 'phoneTable' ? phoneTableCurrentPage : accessoryTableCurrentPage;
            const paginationContainer = document.getElementById(tableId.replace('Table', 'Pagination'));

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
                    if (tableId === 'phoneTable') phoneTableCurrentPage--;
                    else accessoryTableCurrentPage--;
                    renderTable(tableId);
                };

                const nextButton = document.createElement('button');
                nextButton.textContent = 'Next';
                nextButton.classList.add('px-3', 'py-1', 'rounded-md', 'bg-gray-200', 'hover:bg-gray-300', 'disabled:opacity-50');
                nextButton.disabled = currentPage === totalPages;
                nextButton.onclick = () => {
                    if (tableId === 'phoneTable') phoneTableCurrentPage++;
                    else accessoryTableCurrentPage++;
                    renderTable(tableId);
                };

                paginationContainer.appendChild(prevButton);
                paginationContainer.appendChild(nextButton);
            }
        }

        // Sort function
        function sortTable(tableId, column) {
            const table = document.getElementById(tableId);
            const allRows = tableId === 'phoneTable' ? phoneTableAllRows : accessoryTableAllRows;

            // Determine sort direction
            const isAscending = sortState[tableId].column !== column || sortState[tableId].direction === -1;
            sortState[tableId] = { column, direction: isAscending ? 1 : -1 };

            // Sort the rows array in-place
            allRows.sort((a, b) => {
                const cellA = a.cells[column];
                const cellB = b.cells[column];

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
            if (tableId === 'phoneTable') phoneTableCurrentPage = 1;
            else accessoryTableCurrentPage = 1;
            renderTable(tableId);
        }

        // Function to edit the quantity of an item using the new modal
        function editQuantity(tableId, rowElement) {
            currentEditingRow = rowElement; // Store the row being edited
            const modal = document.getElementById('customModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const stockAdjustmentForm = document.getElementById('stockAdjustmentForm');
            const modalButtons = document.getElementById('modalButtons');
            const newQuantityInput = document.getElementById('newQuantityInput');
            const adjustmentComment = document.getElementById('adjustmentComment');
            const accessoryIdInput = document.getElementById('accessoryIdInput');
            const modalError = document.getElementById('modalError');

            // Reset modal state
            modalError.classList.add('hidden');
            modalMessage.classList.remove('hidden');
            stockAdjustmentForm.classList.add('hidden');

            if (tableId === 'accessoryTable') {
                const quantityCell = rowElement.cells[1];
                const currentQuantity = parseInt(quantityCell.dataset.value, 10);
                const itemName = rowElement.cells[0].textContent;
                const accessoryId = rowElement.dataset.id;

                modalTitle.textContent = `Edit Quantity for ${itemName}`;
                modalMessage.textContent = 'Please enter the new quantity and a reason for the change.';
                newQuantityInput.value = currentQuantity;
                adjustmentComment.value = '';
                accessoryIdInput.value = accessoryId;
                stockAdjustmentForm.classList.remove('hidden');

                // Set buttons for editing
                modalButtons.innerHTML = `
                    <button type="button" onclick="cancelEdit()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" onclick="saveQuantity()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        Save
                    </button>
                `;
            } else if (tableId === 'phoneTable') {
                modalTitle.textContent = 'Action Not Applicable';
                modalMessage.textContent = 'Quantity editing is not applicable to phones. Use the IMEI column to manage individual items.';
                stockAdjustmentForm.classList.add('hidden');

                // Set buttons for a simple message
                modalButtons.innerHTML = `
                    <button type="button" onclick="cancelEdit()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors duration-200">
                        Close
                    </button>
                `;
            }
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
            const accessoryId = document.getElementById('accessoryIdInput').value;

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
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        id: accessoryId,
                        new_quantity: newQuantity,
                        comment: comment
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    // Find the table row and update the values
                    const row = document.querySelector(`tr[data-id="${accessoryId}"]`);
                    if (row) {
                        const quantityCell = row.cells[1];
                        const oldQuantity = parseInt(quantityCell.dataset.value, 10);

                        quantityCell.dataset.value = newQuantity;
                        quantityCell.textContent = newQuantity;

                        // Recalculate other values
                        const costPerItem = parseFloat(row.cells[2].dataset.value);
                        const oldProfit = parseFloat(row.cells[4].dataset.value);
                        const sellingPricePerItem = (oldProfit / (oldQuantity > 0 ? oldQuantity : 1)) + costPerItem;

                        const totalCostCell = row.cells[3];
                        const newTotalCost = costPerItem * newQuantity;
                        totalCostCell.dataset.value = newTotalCost;
                        totalCostCell.textContent = '$' + newTotalCost.toFixed(2);

                        const profitCell = row.cells[4];
                        const newProfit = (sellingPricePerItem - costPerItem) * newQuantity;
                        profitCell.dataset.value = newProfit;
                        profitCell.textContent = '$' + newProfit.toFixed(2);

                        renderTable('accessoryTable');
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
            const phoneTable = document.getElementById('phoneTable');
            const accessoryTable = document.getElementById('accessoryTable');
            phoneTableAllRows = Array.from(phoneTable.tBodies[0].rows);
            accessoryTableAllRows = Array.from(accessoryTable.tBodies[0].rows);

            renderTable('phoneTable');
            renderTable('accessoryTable');
        };
    </script>
@endsection
