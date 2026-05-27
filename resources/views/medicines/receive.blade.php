@extends('layouts.app')

@section('content')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6;
        }

        .modal {
            background-color: rgba(0, 0, 0, 0.5);
            transition: opacity 0.3s ease-in-out;
        }

        .modal-content {
            transform: translateY(-20px);
            transition: transform 0.3s ease-in-out;
        }

        .modal:not(.hidden) .modal-content {
            transform: translateY(0);
        }

        /* Custom searchable select styles */
        .custom-select {
            position: relative;
            width: 100%;
        }

        .select-selected {
            background-color: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 8px 12px;
            cursor: pointer;
            user-select: none;
        }

        .select-selected:after {
            position: absolute;
            content: "";
            top: 18px;
            right: 10px;
            width: 0;
            height: 0;
            border: 6px solid transparent;
            border-color: #6b7280 transparent transparent transparent;
        }

        .select-selected.select-arrow-active:after {
            border-color: transparent transparent #6b7280 transparent;
            top: 12px;
        }

        .select-items {
            position: absolute;
            background-color: white;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 99;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            max-height: 250px;
            overflow-y: auto;
            display: none;
        }

        .search-box {
            padding: 8px;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 0;
            background: white;
        }

        .search-box input {
            width: 100%;
            padding: 6px 10px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 14px;
        }

        .select-items div {
            padding: 8px 12px;
            cursor: pointer;
            user-select: none;
        }

        .select-items div:hover {
            background-color: #f3f4f6;
        }

        .same-as-selected {
            background-color: #e5e7eb;
        }
    </style>

    <div class="container mx-auto bg-white p-8 rounded-xl shadow-2xl mt-10 relative max-w-6xl">

        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">
            Receive Product Inventory
        </h1>

        <p class="text-center text-gray-500 mb-8">
            Add products and quantities to inventory stock.
        </p>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Validation Error!</strong>

                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('cashews.receive.store') }}" method="POST" id="main-form">
            @csrf

            <div class="p-6 border rounded-lg bg-white shadow-sm">

                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-semibold text-gray-700">
                         Products
                    </h2>

                    <button type="button"
                            id="add-cashew-btn"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                        + Add Product
                    </button>
                </div>

{{--                <p class="text-sm text-gray-500 mb-6">--}}
{{--                    Add products and quantities. Added products will appear below.--}}

{{--                </p>--}}
                <p class="text-sm text-gray-500 mb-6">
                    Add products and quantities. Added products will appear below.
                    <a href="#" onclick="document.getElementById('bulk-upload-section').classList.toggle('hidden'); return false;"
                       class="text-blue-600 hover:underline ml-1">
                        Bulk upload from Excel
                    </a>
                    <a href="#" onclick="downloadSample(); return false;" class="text-xs text-blue-500 hover:underline ml-2">
                        Download sample Excel
                    </a>
                </p>
                <div id="bulk-upload-section" class="hidden mb-6 border border-dashed border-gray-300 rounded-lg p-4">
                    <p class="text-sm text-gray-500 mb-3">Upload an Excel file with a <code class="bg-gray-100 px-1 rounded">name</code> column.</p>

                    <input type="file" id="bulkFile" accept=".xlsx,.xls" class="block text-sm text-gray-500 mb-3
        file:mr-3 file:py-1 file:px-4 file:rounded file:border-0
        file:text-sm file:font-medium file:bg-blue-50 file:text-blue-600
        hover:file:bg-blue-100"/>

                    <div id="bulk-preview" class="hidden mb-3 text-sm text-gray-600"></div>

                    <button onclick="submitBulkUpload()" id="bulk-btn"
                            class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded hover:bg-blue-700 disabled:opacity-40" disabled>
                        Upload products
                    </button>

                    <div id="bulk-status" class="mt-2 text-sm hidden"></div>
                </div>


                <div id="cashew-list" class="space-y-4"></div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Receive  Stock
                </button>

                <a href="{{ route('cashews.index') }}"
                   class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    View All  Products
                </a>
            </div>

            <div id="hidden-inputs"></div>
        </form>
    </div>

    <!-- Modal -->
    <div id="cashew-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">

            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">

                <h3 class="text-xl font-bold mb-4">
                    Add/Edit New Product
                </h3>

                <form id="cashew-form">

                    <input type="hidden" id="cashew-index">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <div class="custom-select" id="custom-select-container">
                                <div class="select-selected" id="select-selected">-- Search and select a product --</div>
                                <div class="select-items" id="select-items">
                                    <div class="search-box">
                                        <input type="text" id="search-input" placeholder="Search products..." autocomplete="off">
                                    </div>
                                    <div id="options-list">
                                        @foreach($products as $id => $name)
                                            <div data-value="{{ $id }}" data-name="{{ $name }}">{{ $name }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" id="modal-product-id" name="product_id" required>
                            <input type="hidden" id="modal-product-name" name="name">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select id="modal-unit"
                                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                    required>
                                <option value="">Select unit...</option>
                                <option value="kg">Kilogram (kg)</option>
                                <option value="gram">Gram (g)</option>
                                <option value="box">Box</option>
                                <option value="package">Package</option>
                                <option value="bag">Bag</option>
                                <option value="carton">Carton</option>
                                <option value="piece">Piece</option>
                                <option value="ton">Ton</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Unit Price (TZS)
                            </label>
                            <input type="number"
                                   step="0.01"
                                   id="modal-unit-price"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Price per unit">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Selling Price (TZS)
                            </label>
                            <input type="number"
                                   step="0.01"
                                   id="modal-selling-price"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Selling price per unit">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Stock Origin
                            </label>
                            <input type="text"
                                   id="modal-stock-origin"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="e.g. Mtwara">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Condition
                            </label>
                            <select id="modal-condition"
                                    class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                                <option value="">Select condition...</option>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Batch Number
                            </label>
                            <input type="text"
                                   id="modal-batch-number"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Batch number">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Received At
                            </label>
                            <input type="date"
                                   id="modal-received-at"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   value="{{ date('Y-m-d') }}">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Quantity <span class="text-red-500">*</span>
                            </label>
                            <input type="number"
                                   id="modal-quantity"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Quantity"
                                   required>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Low Stock Threshold
                            </label>
                            <input type="number"
                                   id="modal-low-stock-threshold"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Low stock threshold"
                                   value="5"
                                   min="0">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                Description
                            </label>
                            <textarea id="modal-description"
                                      rows="3"
                                      class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                      placeholder="Additional description..."></textarea>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end space-x-2">

                        <button type="button"
                                id="close-modal-btn"
                                class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">
                            Cancel
                        </button>

                        <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">
                            Save
                        </button>

                    </div>

                </form>

            </div>
        </div>
    </div>
    <script src="{{ asset('assets/js/xlsx.full.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            let cashewsData = [];
            let cashewCounter = 0;

            const cashewModal = document.getElementById('cashew-modal');
            const addCashewBtn = document.getElementById('add-cashew-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const cashewForm = document.getElementById('cashew-form');
            const cashewList = document.getElementById('cashew-list');
            const hiddenInputs = document.getElementById('hidden-inputs');

            // Custom select dropdown functionality
            let selectSelected = document.getElementById('select-selected');
            let selectItems = document.getElementById('select-items');
            let searchInput = document.getElementById('search-input');
            let optionsList = document.getElementById('options-list');
            let modalProductId = document.getElementById('modal-product-id');
            let modalProductName = document.getElementById('modal-product-name');

            // Get all options
            let allOptions = Array.from(optionsList.querySelectorAll('div[data-value]'));

            // Toggle dropdown
            if (selectSelected) {
                selectSelected.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectItems.style.display = selectItems.style.display === 'block' ? 'none' : 'block';
                    if (selectItems.style.display === 'block') {
                        setTimeout(() => searchInput.focus(), 100);
                    }
                });
            }

            // Search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    let searchTerm = this.value.toLowerCase();
                    allOptions.forEach(option => {
                        let text = option.textContent.toLowerCase();
                        if (text.includes(searchTerm)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                });
            }

            // Select option
            function selectOption(element) {
                let productId = element.getAttribute('data-value');
                let productName = element.getAttribute('data-name') || element.textContent;
                selectSelected.textContent = productName;
                modalProductId.value = productId;
                modalProductName.value = productName;
                selectItems.style.display = 'none';

                // Remove selected class from all
                allOptions.forEach(opt => opt.classList.remove('same-as-selected'));
                element.classList.add('same-as-selected');
            }

            // Add click handlers to options
            function bindOptionClick() {
                document.querySelectorAll('#options-list div[data-value]').forEach(option => {
                    option.removeEventListener('click', option.clickHandler);
                    option.clickHandler = function() {
                        selectOption(this);
                    };
                    option.addEventListener('click', option.clickHandler);
                });
            }

            bindOptionClick();

            // Close dropdown when clicking outside
            document.addEventListener('click', function() {
                if (selectItems) {
                    selectItems.style.display = 'none';
                }
            });

            // Reset custom select
            function resetCustomSelect() {
                selectSelected.textContent = '-- Search and select a product --';
                modalProductId.value = '';
                modalProductName.value = '';
                if (searchInput) searchInput.value = '';
                allOptions.forEach(option => {
                    option.style.display = '';
                    option.classList.remove('same-as-selected');
                });
            }

            // Set custom select value
            function setCustomSelectValue(productId, productName) {
                if (!productId) {
                    resetCustomSelect();
                    return;
                }
                let matchingOption = allOptions.find(opt => opt.getAttribute('data-value') == productId);
                if (matchingOption) {
                    selectOption(matchingOption);
                } else if (productName) {
                    selectSelected.textContent = productName;
                    modalProductId.value = productId;
                    modalProductName.value = productName;
                }
            }

            addCashewBtn.addEventListener('click', () => {
                cashewForm.reset();
                document.getElementById('cashew-index').value = '';
                resetCustomSelect();
                document.getElementById('modal-received-at').value = '{{ date("Y-m-d") }}';
                document.getElementById('modal-low-stock-threshold').value = 5;
                cashewModal.classList.remove('hidden');
            });

            closeModalBtn.addEventListener('click', () => {
                cashewModal.classList.add('hidden');
            });

            cashewForm.addEventListener('submit', function (e) {

                e.preventDefault();

                const index = document.getElementById('cashew-index').value;

                const cashew = {
                    id: index ? cashewsData[index].id : cashewCounter++,
                    product_id: modalProductId.value,
                    name: modalProductName.value,
                    unit: document.getElementById('modal-unit').value,
                    unit_price: document.getElementById('modal-unit-price').value,
                    selling_price: document.getElementById('modal-selling-price').value,
                    quantity: document.getElementById('modal-quantity').value,
                    low_stock_threshold: document.getElementById('modal-low-stock-threshold').value || 5,
                    stock_origin: document.getElementById('modal-stock-origin').value,
                    condition: document.getElementById('modal-condition').value,
                    batch_number: document.getElementById('modal-batch-number').value,
                    received_at: document.getElementById('modal-received-at').value,
                    description: document.getElementById('modal-description').value,
                    status: 'available'
                };

                if (!cashew.product_id || !cashew.quantity) {
                    alert('Product name and quantity are required.');
                    return;
                }

                if (!cashew.unit) {
                    alert('Please select a unit.');
                    return;
                }

                if (index !== '') {
                    cashewsData[index] = cashew;
                } else {
                    cashewsData.push(cashew);
                }

                renderCashewList();
                cashewModal.classList.add('hidden');

            });

            function renderCashewList() {

                cashewList.innerHTML = '';
                hiddenInputs.innerHTML = '';

                cashewsData.forEach((cashew, index) => {

                    const card = document.createElement('div');

                    card.className = 'bg-gray-100 p-4 rounded-md shadow-sm';

                    card.innerHTML = `
                        <div class="flex justify-between items-center">

                            <div>
                                <h4 class="font-semibold text-lg">${escapeHtml(cashew.name)}</h4>

                                <p class="text-sm text-gray-600">
                                    Unit: ${escapeHtml(cashew.unit)}
                                </p>

                                <p class="text-sm text-gray-600">
                                    Origin: ${escapeHtml(cashew.stock_origin || 'N/A')}
                                </p>

                                <p class="text-sm text-gray-600">
                                    Batch: ${escapeHtml(cashew.batch_number || 'N/A')}
                                </p>

                                ${cashew.condition ? `<p class="text-sm text-gray-600">Condition: ${escapeHtml(cashew.condition)}</p>` : ''}
                            </div>

                            <div class="text-right">

                                <p class="text-lg font-bold">
                                    ${cashew.quantity} ${cashew.unit}(s)
                                </p>

                                <p class="text-sm text-gray-500">
                                    Unit Price: TZS ${parseFloat(cashew.unit_price || 0).toLocaleString()}
                                </p>

                                <p class="text-sm text-gray-500">
                                    Selling: TZS ${parseFloat(cashew.selling_price || 0).toLocaleString()}
                                </p>

                                <p class="text-sm font-semibold text-green-600">
                                    Total: TZS ${(parseFloat(cashew.unit_price || 0) * parseFloat(cashew.quantity || 0)).toLocaleString()}
                                </p>

                            </div>
                        </div>

                        <div class="flex justify-end mt-4 space-x-2">

                            <button type="button"
                                    class="edit-btn bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded"
                                    data-index="${index}">
                                Edit
                            </button>

                            <button type="button"
                                    class="delete-btn bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded"
                                    data-index="${index}">
                                Delete
                            </button>

                        </div>
                    `;

                    cashewList.appendChild(card);

                    // Add hidden inputs for form submission
                    const fields = ['product_id', 'name', 'unit', 'unit_price', 'selling_price', 'quantity', 'low_stock_threshold', 'stock_origin', 'condition', 'batch_number', 'received_at', 'description', 'status'];

                    fields.forEach(field => {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `cashews[${index}][${field}]`;
                        input.value = cashew[field] || '';
                        hiddenInputs.appendChild(input);
                    });

                });

                bindButtons();
            }

            // Helper function to escape HTML
            function escapeHtml(str) {
                if (!str) return '';
                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function bindButtons() {

                document.querySelectorAll('.edit-btn').forEach(button => {

                    button.addEventListener('click', function () {

                        const index = this.dataset.index;
                        const cashew = cashewsData[index];

                        document.getElementById('cashew-index').value = index;
                        setCustomSelectValue(cashew.product_id, cashew.name);
                        document.getElementById('modal-unit').value = cashew.unit || '';
                        document.getElementById('modal-unit-price').value = cashew.unit_price || '';
                        document.getElementById('modal-selling-price').value = cashew.selling_price || '';
                        document.getElementById('modal-quantity').value = cashew.quantity || '';
                        document.getElementById('modal-low-stock-threshold').value = cashew.low_stock_threshold || 5;
                        document.getElementById('modal-stock-origin').value = cashew.stock_origin || '';
                        document.getElementById('modal-condition').value = cashew.condition || '';
                        document.getElementById('modal-batch-number').value = cashew.batch_number || '';
                        document.getElementById('modal-received-at').value = cashew.received_at || '{{ date("Y-m-d") }}';
                        document.getElementById('modal-description').value = cashew.description || '';

                        cashewModal.classList.remove('hidden');

                    });

                });

                document.querySelectorAll('.delete-btn').forEach(button => {

                    button.addEventListener('click', function () {

                        const index = this.dataset.index;

                        if (confirm('Remove this cashew product?')) {

                            cashewsData.splice(index, 1);

                            renderCashewList();
                        }

                    });

                });

            }

        });
    </script>
    <script>
        let bulkProducts = [];

        document.getElementById('bulkFile').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                const wb = XLSX.read(e.target.result, { type: 'array' });
                const ws = wb.Sheets[wb.SheetNames[0]];
                const rows = XLSX.utils.sheet_to_json(ws, { defval: '' });

                bulkProducts = rows
                    .map(r => (r['name'] || r['Name'] || r['NAME'] || '').toString().trim())
                    .filter(n => n.length > 0);

                document.getElementById('bulk-preview').classList.remove('hidden');
                document.getElementById('bulk-preview').textContent = bulkProducts.length + ' products found: ' + bulkProducts.slice(0, 3).join(', ') + (bulkProducts.length > 3 ? '...' : '');
                document.getElementById('bulk-btn').disabled = bulkProducts.length === 0;
            };
            reader.readAsArrayBuffer(file);
        });

        async function submitBulkUpload() {
            const btn = document.getElementById('bulk-btn');
            const status = document.getElementById('bulk-status');
            btn.disabled = true;
            btn.textContent = 'Uploading...';
            status.className = 'mt-2 text-sm text-gray-500';
            status.classList.remove('hidden');
            status.textContent = 'Sending...';

            try {
                const res = await fetch('{{ route("products.bulk-upload") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ products: bulkProducts.map(n => ({ name: n })) })
                });
                const data = await res.json();
                if (res.ok) {
                    status.className = 'mt-2 text-sm text-green-600';
                    status.textContent = data.message || bulkProducts.length + ' products added.';
                    document.getElementById('bulkFile').value = '';
                    document.getElementById('bulk-preview').classList.add('hidden');
                    bulkProducts = [];
                } else {
                    status.className = 'mt-2 text-sm text-red-600';
                    status.textContent = data.message || 'Something went wrong.';
                    btn.disabled = false;
                    btn.textContent = 'Upload products';
                }
            } catch (err) {
                status.className = 'mt-2 text-sm text-red-600';
                status.textContent = 'Network error: ' + err.message;
                btn.disabled = false;
                btn.textContent = 'Upload products';
            }
        }
        function downloadSample() {
            const ws = XLSX.utils.aoa_to_sheet([
                ['name'],
                ['Product A'],
                ['Product B'],
                ['Product C'],
            ]);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Products');
            XLSX.writeFile(wb, 'bulk_upload_sample.xlsx');
        }
    </script>
@endsection
