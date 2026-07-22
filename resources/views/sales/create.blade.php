
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
            <img src="{{ asset('images/watermark.png') }}"
                 alt="Watermark"
                 class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
                 style="transform: translate(-50%, -60%);" />
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 text-sm font-bold mb-2">Customer Name:</label>
                    <input type="text" name="customer_name" id="customer_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_name') border-red-500 @enderror" value="{{ old('customer_name') }}" placeholder="Customer's Full Name">
                    @error('customer_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="customer_medicine" class="block text-gray-700 text-sm font-bold mb-2">Customer Phone (Optional):</label>
                    <input type="text" name="customer_medicine" id="customer_medicine" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_medicine') border-red-500 @enderror" value="{{ old('customer_medicine') }}" placeholder="e.g., +2557XXXXXXXX">
                    @error('customer_medicine')
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
                <!-- Medicines to Sell Section -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Medicines to Sell (BARCODE):</label>
                    <div class="relative">
                        <input type="text" id="medicineSearchInput" placeholder="Search by BARCODE or model..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div id="medicine-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                    <div id="medicine-barcode-inputs" class="mt-2">
                        <!-- Dynamic medicine inputs will be added here -->
                    </div>
                </div>

                <!-- Cosmetics to Sell Section -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Cosmetics to Sell:</label>
                    <div class="relative">
                        <input type="text" id="cosmeticSearchInput" placeholder="Search by name or product..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div id="cosmetic-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                    <div id="cosmetic-inputs" class="mt-2">
                        <!-- Dynamic cosmetic inputs will be added here -->
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

                        <!-- Mobile Money -->
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="radio" name="payment_option" value="3"
                                   class="form-radio h-5 w-5 text-blue-600"
                                {{ old('payment_option') == 3 ? 'checked' : '' }}>
                            <span class="ml-2 text-gray-700">Mobile Money</span>
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
        // Pass available Medicines and cosmetics data to JavaScript
        const availableMedicines = @json($availableMedicines);
        const availableCosmetics = @json($availableCosmetics);

        const form = document.querySelector('form');
        const medicineSearchInput = document.getElementById('medicineSearchInput');
        const medicineSearchResults = document.getElementById('medicine-search-results');
        const medicineImeiInputs = document.getElementById('medicine-barcode-inputs');

        const cosmeticSearchInput = document.getElementById('cosmeticSearchInput');
        const cosmeticSearchResults = document.getElementById('cosmetic-search-results');
        const cosmeticInputs = document.getElementById('cosmetic-inputs');
        let cosmeticIndex = 0;

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
            // Check if we're focused on cosmetic search
            const isCosmeticFocused = document.activeElement === cosmeticSearchInput;
            const isMedicineFocused = document.activeElement === medicineSearchInput;

            // If focused on cosmetic search, only look for cosmetics
            if (isCosmeticFocused) {
                const cosmetic = availableCosmetics.find(a =>
                    a.barcode === code || a.id.toString() === code
                );
                if (cosmetic) {
                    addCosmeticInput(cosmetic);
                    showToast(`Added cosmetic: ${cosmetic.name}`, 'success');
                    return;
                }
                showToast(`No cosmetic found for barcode: ${code}`, 'error');
                return;
            }

            // If focused on medicine search, only look for Medicines
            if (isMedicineFocused) {
                const medicine = availableMedicines.find(p => p.barcode === code);
                if (medicine) {
                    addMedicineInput(medicine);
                    showToast(`Added medicine: (${medicine.barcode})`, 'success');
                    return;
                }
                showToast(`No medicine found for barcode: ${code}`, 'error');
                return;
            }

            // Default behavior when not focused on any search input
            // First try Medicines
            const medicine = availableMedicines.find(p => p.barcode === code);
            if (medicine) {
                addMedicineInput(medicine);
                showToast(`Added medicine: (${medicine.barcode})`, 'success');
                return;
            }

            // Then try cosmetics
            const cosmetic = availableCosmetics.find(a =>
                a.barcode === code || a.id.toString() === code
            );
            if (cosmetic) {
                addCosmeticInput(cosmetic);
                showToast(`Added cosmetic: ${cosmetic.name}`, 'success');
                return;
            }

            showToast(`No product found for barcode: ${code}`, 'error');
        }

        // --- AUTO-SEARCH FUNCTIONS ---
        // For Medicines
        medicineSearchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            if (searchTerm.length > 0) {
                const filteredMedicines = availableMedicines.filter(medicine => {
                    const productName = medicine.product ? medicine.product.name.toLowerCase() : '';
                    return medicine.barcode.toLowerCase().includes(searchTerm) ||
                        productName.includes(searchTerm);
                });
                renderMedicineResults(filteredMedicines);
            } else {
                medicineSearchResults.classList.add('hidden');
            }
        });

        // For Cosmetics (NEW - matches medicine search functionality)
        cosmeticSearchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            if (searchTerm.length > 0) {
                const filteredCosmetics = availableCosmetics.filter(cosmetic => {
                    const productName = cosmetic.product ? cosmetic.product.name.toLowerCase() : '';
                    return cosmetic.name.toLowerCase().includes(searchTerm) ||
                        productName.includes(searchTerm) ||
                        (cosmetic.barcode && cosmetic.barcode.toString().includes(searchTerm)) ||
                        (cosmetic.id && cosmetic.id.toString().includes(searchTerm));
                });
                renderCosmeticResults(filteredCosmetics);
            } else {
                cosmeticSearchResults.classList.add('hidden');
            }
        });

        function renderMedicineResults(results) {
            medicineSearchResults.innerHTML = '';
            if (results.length > 0) {
                results.forEach(medicine => {
                    const productName = medicine.product ? medicine.product.name : 'N/A';
                    const resultItem = document.createElement('div');
                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';
                    resultItem.textContent = `${medicine.barcode} - ${productName}) - $${parseFloat(medicine.selling_price).toFixed(2)}`;
                    resultItem.dataset.barcode = medicine.barcode;
                    medicineSearchResults.appendChild(resultItem);
                });
                medicineSearchResults.classList.remove('hidden');
            } else {
                medicineSearchResults.classList.add('hidden');
            }
        }

        function renderCosmeticResults(results) {
            cosmeticSearchResults.innerHTML = '';
            if (results.length > 0) {
                results.forEach(cosmetic => {
                    const productName = cosmetic.product ? cosmetic.product.name : 'N/A';
                    const resultItem = document.createElement('div');
                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';
                    resultItem.textContent = `${cosmetic.name} - ${productName} - $${parseFloat(cosmetic.selling_price).toFixed(2)}`;
                    resultItem.dataset.id = cosmetic.id;
                    cosmeticSearchResults.appendChild(resultItem);
                });
                cosmeticSearchResults.classList.remove('hidden');
            } else {
                cosmeticSearchResults.classList.add('hidden');
            }
        }

        // --- ADD ITEM FUNCTIONS ---
        function addMedicineInput(medicine) {
            // Check if already added
            if (document.querySelector(`input[name="medicine_barcodes[]"][value="${medicine.barcode}"]`)) {
                showToast('This medicine is already in the list', 'warning');
                return;
            }

            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 medicine-item-group mb-2';
            div.innerHTML = `
            <input type="hidden" name="medicine_barcodes[]" value="${medicine.barcode}">
            <input type="text" value="${medicine.barcode} - ${medicine.model}" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
            <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>
        `;
            medicineImeiInputs.appendChild(div);
        }

        function addCosmeticInput(cosmetic) {
            // Check if already added
            const existingCosmetics = document.querySelectorAll('input[name^="cosmetics"]');
            for (let i = 0; i < existingCosmetics.length; i++) {
                if (existingCosmetics[i].value === cosmetic.id.toString()) {
                    showToast(`Cosmetic ${cosmetic.name} is already in the list`, 'warning');
                    return;
                }
            }
            const div = document.createElement('div');
            const productName = cosmetic.product ? cosmetic.product.name : 'N/A';
            div.innerHTML = `
    <input type="hidden" name="cosmetics[${cosmeticIndex}][id]" value="${cosmetic.id}">
    <input type="text" value="${cosmetic.name} - ${productName} - $${parseFloat(cosmetic.selling_price).toFixed(2)}" readonly
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
    <button type="button" onclick="removeInput(this)"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
        Remove
    </button>
    <input type="number" name="cosmetics[${cosmeticIndex}][quantity]" value="1" min="1"
           class="w-20 text-center shadow appearance-none border rounded py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
`;

            cosmeticInputs.appendChild(div);
            cosmeticIndex++;
        }

        // --- UTILITY FUNCTIONS ---
        function removeInput(button) {
            button.closest('.medicine-item-group, .cosmetic-item-group').remove();
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
        medicineSearchResults.addEventListener('click', (event) => {
            const barcode = event.target.dataset.barcode;
            if (barcode) {
                const selectedMedicine = availableMedicines.find(p => p.barcode === barcode);
                if (selectedMedicine) {
                    addMedicineInput(selectedMedicine);
                    medicineSearchInput.value = '';
                    medicineSearchResults.classList.add('hidden');
                }
            }
        });

        cosmeticSearchResults.addEventListener('click', (event) => {
            const id = parseInt(event.target.dataset.id);
            if (id) {
                const selectedCosmetic = availableCosmetics.find(a => a.id === id);
                if (selectedCosmetic) {
                    addCosmeticInput(selectedCosmetic);
                    cosmeticSearchInput.value = '';
                    cosmeticSearchResults.classList.add('hidden');
                }
            }
        });

        // Close search results when clicking outside
        document.addEventListener('click', (event) => {
            if (!medicineSearchInput.contains(event.target) && !medicineSearchResults.contains(event.target)) {
                medicineSearchResults.classList.add('hidden');
            }
            if (!cosmeticSearchInput.contains(event.target) && !cosmeticSearchResults.contains(event.target)) {
                cosmeticSearchResults.classList.add('hidden');
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
