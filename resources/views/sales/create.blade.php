
@extends('layouts.app')

@section('title', 'Sales Overview')
@section('subtitle', 'Manage all sales transactions.')

@section('content')
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Sale</h1>

        <!-- Success/Error Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Validation Error!</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sales.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-md mb-6 relative">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 text-sm font-bold mb-2">Customer Name (Optional):</label>
                    <input type="text" name="customer_name" id="customer_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_name') border-red-500 @enderror" value="{{ old('customer_name') }}" placeholder="Customer's Full Name">
                    @error('customer_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_cashew" class="block text-gray-700 text-sm font-bold mb-2">Customer Cashew (Optional):</label>
                    <input type="text" name="customer_cashew" id="customer_cashew" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_cashew') border-red-500 @enderror" value="{{ old('customer_cashew') }}" placeholder="e.g., +2557XXXXXXXX">
                    @error('customer_cashew')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mb-6">
                <label for="customer_email" class="block text-gray-700 text-sm font-bold mb-2">Customer Email (Optional):</label>
                <input type="email" name="customer_email" id="customer_email"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_email') border-red-500 @enderror"
                       value="{{ old('customer_email') }}" placeholder="customer@example.com">
                @error('customer_email')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Cashews to Sell Section -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Product to Sell:</label>
                    <div class="relative">
                        <input type="text" id="cashewSearchInput" placeholder="Search by BARCODE or model..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div id="cashew-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                    <div id="cashew-barcode-inputs" class="mt-2">
                        <!-- Dynamic cashew inputs will be added here -->
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="discount_amount" class="block text-gray-700 text-sm font-bold mb-2">Discount Amount ($):</label>
                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('discount_amount') border-red-500 @enderror" value="{{ old('discount_amount', 0) }}" min="0">
                    @error('discount_amount')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="mb-6">
                    <input type="hidden" name="is_installment" value="0">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_installment" id="is_installment" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_installment') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Is Installment Sale?</span>
                    </label>
                    <!-- Hidden input to handle "no selection" case -->
                    <input type="hidden" name="payment_option" value="0">

                    <div class="flex items-center space-x-6">
                        <!-- Cash -->
                        <span class="ml-2 text-gray-700">Payment Option</span>
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_option" value="1"
                                   class="form-radio h-5 w-5 text-blue-600"
                                {{ old('payment_option') == 1 ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Cash</span>
                        </label>

                        <!-- Bank -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_option" value="2"
                                   class="form-radio h-5 w-5 text-blue-600"
                                {{ old('payment_option') == 2 ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Bank</span>
                        </label>

                        <!-- Cashew -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_option" value="3"
                                   class="form-radio h-5 w-5 text-blue-600"
                                {{ old('payment_option') == 3 ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Cashew</span>
                        </label>
                    </div>

                </div>
                <div class="mb-6">
                    <input type="hidden" name="credit_sale" value="0">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="credit_sale" id="credit_sale" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('credit_sale') ? 'checked' : '' }}>
                        <span class="ml-2 text-gray-700">Credit Sale?</span>
                    </label>
                </div>
            </div>
            <div id="installment-details" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 {{ old('is_installment') ? '' : 'hidden' }}">
                <div>
                    <label for="total_installments" class="block text-gray-700 text-sm font-bold mb-2">Total Installments:</label>
                    <input type="number" name="total_installments" id="total_installments" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('total_installments') border-red-500 @enderror" value="{{ old('total_installments') }}" min="1">
                    @error('total_installments')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="installment_amount" class="block text-gray-700 text-sm font-bold mb-2">Installment Amount ($ per installment):</label>
                    <input type="number" step="0.01" name="installment_amount" id="installment_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('installment_amount') border-red-500 @enderror" value="{{ old('installment_amount') }}" min="0.01">
                    @error('installment_amount')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Installment Start Date:</label>
                    <input type="date" name="start_date" id="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('start_date') border-red-500 @enderror" value="{{ old('start_date', date('Y-m-d')) }}">
                    @error('start_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Record Sale
                </button>
                <a href="{{ route('sales.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    View All Sales
                </a>
            </div>
        </form>
    </div>


    <script>
        // Pass available Cashewsdata to JavaScript
        const availableCashews = @json($availableCashews);

        const form = document.querySelector('form');
        const cashewSearchInput = document.getElementById('cashewSearchInput');
        const cashewSearchResults = document.getElementById('cashew-search-results');
        const cashewImeiInputs = document.getElementById('cashew-barcode-inputs');

        // --- BARCODE SCANNER IMPLEMENTATION ---
        let barcode = '';
        let lastKeyTime = Date.now();
        const barcodeInterval = 50; // Max time between keypresses (ms)

        document.addEventListener('keydown', function(event) {
            // Ignore Enter key from submitting form
            if (event.key === 'Enter') {
                event.preventDefault();

                // If we have a collected barcode, process it
                if (barcode.length > 0) {
                    processScannedBarcode(barcode);
                    barcode = '';
                }
                return;
            }

            // Check if this is part of a barcode scan (quick successive keypresses)
            const currentTime = Date.now();
            if (currentTime - lastKeyTime > barcodeInterval) {
                barcode = ''; // Reset if too much time between keys
            }
            lastKeyTime = currentTime;

            // Add to barcode (ignore modifier keys)
            if (event.key.length === 1 && !event.ctrlKey && !event.altKey && !event.metaKey) {
                barcode += event.key;
            }
        });

        function processScannedBarcode(code) {
            const cashew = availableCashews.find(p => p.barcode && p.barcode === code);

            if (cashew) {
                addCashewInput(cashew);
                showToast(`Added by barcode: ${cashew.product_name}`, 'success');
                return;
            }

            showToast(`No barcode match. Try search instead.`, 'error');
        }

        // --- AUTO-SEARCH FUNCTIONS ---
        // For Cashews
        cashewSearchInput.addEventListener('input', (event) => {
            const searchTerm = (event.target.value || '').toLowerCase();

            if (searchTerm.length > 0) {
                const filteredCashews = availableCashews.filter(cashew => {
                    const name = (cashew.product_name || '').toLowerCase();
                    const barcode = (cashew.barcode || '').toLowerCase();

                    return name.includes(searchTerm) || barcode.includes(searchTerm);
                });

                renderCashewResults(filteredCashews);
            } else {
                cashewSearchResults.classList.add('hidden');
            }
        });

        function renderCashewResults(results) {
            cashewSearchResults.innerHTML = '';

            results.forEach(cashew => {
                const div = document.createElement('div');
                div.className = 'p-2 cursor-pointer hover:bg-gray-200';

                const barcodeText = cashew.barcode ? `(${cashew.barcode})` : '';

                div.textContent =
                    `${cashew.product_name} ${barcodeText} - Tsh ${cashew.selling_price} (${cashew.available_quantity} available)`;

                div.dataset.id = cashew.id;

                cashewSearchResults.appendChild(div);
            });

            cashewSearchResults.classList.remove('hidden');
        }


        // --- ADD ITEM FUNCTIONS ---
        function addCashewInput(cashew) {
            const key = cashew.id ?? cashew.barcode;

            if (document.querySelector(`[data-key="${key}"]`)) {
                showToast('This product is already added', 'warning');
                return;
            }

            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 cashew-item-group mb-2';
            div.dataset.key = key;

            // ✅ IMPORTANT: store price here
            div.dataset.price = cashew.selling_price || 0;
            div.dataset.available = cashew.available_quantity || 0;

            div.innerHTML = `
        <input type="hidden" name="cashew_ids[]" value="${cashew.id ?? ''}">

        <input type="text"
            value="${cashew.product_name} ${cashew.barcode ? '(' + cashew.barcode + ')' : ''} - Tsh ${cashew.selling_price} (${cashew.available_quantity || 0} available)"
            readonly
            class="shadow appearance-none border rounded w-full py-2 px-3 bg-gray-100">

        <input type="number"
            name="quantities[]"
            min="1"
            max="${cashew.available_quantity || 1}"
            value="1"
            class="qty w-20 border rounded px-2 py-2 text-center">

<!--        <div class="mt-4 p-2 border-2 border-green-500 bg-green-50 text-green-700 text-sm font-bold rounded-lg shadow">-->
<!--            Total: Tsh <span id="totalAmount">0</span>-->
<!--        </div>-->
            <div class="mt-4 p-2 border-2 border-green-500 bg-green-50 text-green-700 text-sm font-bold rounded-lg shadow">
                Total: Tsh <span class="rowTotal">0</span>
            </div>

        <button type="button"
            onclick="removeInput(this)"
            class="bg-red-500 text-white px-4 py-2 rounded">
            Remove
        </button>
    `;

            cashewImeiInputs.appendChild(div);

            // ✅ call AFTER adding element
            calculateTotal();
        }

        document.addEventListener('input', function (e) {
            if (e.target.name === 'quantities[]') {
                calculateTotal();
            }
        });

        // function calculateTotal() {
        //     let total = 0;
        //
        //     document.querySelectorAll('.cashew-item-group').forEach(row => {
        //         const price = parseFloat(row.dataset.price || 0);
        //         const qtyInput = row.querySelector('input[name="quantities[]"]');
        //
        //         const qty = parseInt(qtyInput?.value || 0);
        //
        //         total += price * qty;
        //     });
        //
        //     document.getElementById('totalAmount').textContent = total.toFixed(2);
        // }

        function calculateTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.cashew-item-group').forEach(row => {
                const price = parseFloat(row.dataset.price || 0);
                const available = parseInt(row.dataset.available || 0);
                const qtyInput = row.querySelector('input[name="quantities[]"]');
                let qty = parseInt(qtyInput?.value || 0);
                if (available > 0 && qty > available) {
                    qty = available;
                    qtyInput.value = available;
                }
                const rowTotal = price * qty;
                grandTotal += rowTotal;
                // Update this specific row's total span
                row.querySelector('.rowTotal').textContent = rowTotal.toFixed(2);
            });
        }

        // --- UTILITY FUNCTIONS ---

        function removeInput(button) {
            button.closest('.cashew-item-group').remove();
            calculateTotal();
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-4 py-2 rounded shadow-lg text-white ${
                type === 'success' ? 'bg-green-500' :
                    type === 'error' ? 'bg-red-500' : 'bg-yellow-500'
            }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // --- FORM SUBMISSION CONTROL ---
        form.addEventListener('submit', function(event) {
            // Only allow submission from the actual submit button
            if (event.submitter && event.submitter.type === 'submit') {
                return; // Allow submission
            }
            event.preventDefault(); // Block all other submission attempts
        });

        // Handle selection from search results
        cashewSearchResults.addEventListener('click', (event) => {
            const item = event.target.closest('div');
            if (!item) return;

            const id = item.dataset.id;
            const selected = availableCashews.find(c => c.id == id);

            if (selected) {
                addCashewInput(selected);
                cashewSearchInput.value = '';
                cashewSearchResults.classList.add('hidden');
            }
        });


        // Close search results when clicking outside
        document.addEventListener('click', (event) => {
            if (!cashewSearchInput.contains(event.target) && !cashewSearchResults.contains(event.target)) {
                cashewSearchResults.classList.add('hidden');
            }
        });

        // Toggle installment details visibility
        document.getElementById('is_installment').addEventListener('change', function() {
            const installmentDetails = document.getElementById('installment-details');
            if (this.checked) {
                installmentDetails.classList.remove('hidden');
            } else {
                installmentDetails.classList.add('hidden');
            }
        });
    </script>
@endsection
