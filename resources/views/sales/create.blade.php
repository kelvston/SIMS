{{--@extends('layouts.app')--}}

{{--@section('title', 'Sales Overview')--}}
{{--@section('subtitle', 'Manage all sales transactions.')--}}

{{--@section('content')--}}
{{--    <div class="container mx-auto p-8">--}}
{{--        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Sale</h1>--}}

{{--        <!-- Success/Error Messages -->--}}
{{--        @if (session('success'))--}}
{{--            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">--}}
{{--                <strong class="font-bold">Success!</strong>--}}
{{--                <span class="block sm:inline">{{ session('success') }}</span>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        @if (session('error'))--}}
{{--            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">--}}
{{--                <strong class="font-bold">Error!</strong>--}}
{{--                <span class="block sm:inline">{{ session('error') }}</span>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        @if ($errors->any())--}}
{{--            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">--}}
{{--                <strong class="font-bold">Validation Error!</strong>--}}
{{--                <ul class="mt-2 list-disc list-inside">--}}
{{--                    @foreach ($errors->all() as $error)--}}
{{--                        <li>{{ $error }}</li>--}}
{{--                    @endforeach--}}
{{--                </ul>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        <form action="{{ route('sales.store') }}" method="POST" class="bg-white p-8 rounded-lg shadow-md mb-6 relative">--}}
{{--            @csrf--}}
{{--            <img src="{{ asset('images/watermark.png') }}"--}}
{{--                 alt="Watermark"--}}
{{--                 class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"--}}
{{--                 style="transform: translate(-50%, -60%);" />--}}
{{--            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">--}}
{{--                <div>--}}
{{--                    <label for="customer_name" class="block text-gray-700 text-sm font-bold mb-2">Customer Name:</label>--}}
{{--                    <input type="text" name="customer_name" id="customer_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_name') border-red-500 @enderror" value="{{ old('customer_name') }}" placeholder="Customer's Full Name">--}}
{{--                    @error('customer_name')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}

{{--                <div>--}}
{{--                    <label for="customer_phone" class="block text-gray-700 text-sm font-bold mb-2">Customer Phone (Optional):</label>--}}
{{--                    <input type="text" name="customer_phone" id="customer_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_phone') border-red-500 @enderror" value="{{ old('customer_phone') }}" placeholder="e.g., +2557XXXXXXXX">--}}
{{--                    @error('customer_phone')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="mb-6">--}}
{{--                <label for="customer_email" class="block text-gray-700 text-sm font-bold mb-2">Customer Email (Optional):</label>--}}
{{--                <input type="email" name="customer_email" id="customer_email"--}}
{{--                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_email') border-red-500 @enderror"--}}
{{--                       value="{{ old('customer_email') }}" placeholder="customer@example.com">--}}
{{--                @error('customer_email')--}}
{{--                <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                @enderror--}}
{{--            </div>--}}

{{--            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">--}}
{{--                <!-- Phones to Sell Section -->--}}
{{--                <div>--}}
{{--                    <label class="block text-gray-700 text-sm font-bold mb-2">Phones to Sell (IMEI):</label>--}}
{{--                    <div class="relative">--}}
{{--                        <input type="text" id="phoneSearchInput" placeholder="Search by IMEI or model..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
{{--                        <div id="phone-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>--}}
{{--                    </div>--}}
{{--                    <div id="phone-imei-inputs" class="mt-2">--}}
{{--                        <!-- Dynamic phone inputs will be added here -->--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Accessories to Sell Section -->--}}
{{--                <div>--}}
{{--                    <label class="block text-gray-700 text-sm font-bold mb-2">Accessories to Sell:</label>--}}
{{--                    <div class="relative">--}}
{{--                        <input type="text" id="accessorySearchInput" placeholder="Search by name or brand..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
{{--                        <div id="accessory-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>--}}
{{--                    </div>--}}
{{--                    <div id="accessory-inputs" class="mt-2">--}}
{{--                        <!-- Dynamic accessory inputs will be added here -->--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">--}}
{{--                <div>--}}
{{--                    <label for="discount_amount" class="block text-gray-700 text-sm font-bold mb-2">Discount Amount ($):</label>--}}
{{--                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('discount_amount') border-red-500 @enderror" value="{{ old('discount_amount', 0) }}" min="0">--}}
{{--                    @error('discount_amount')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row">--}}
{{--                        <div class="mb-6">--}}
{{--                            <input type="hidden" name="is_installment" value="0">--}}
{{--                            <label class="inline-flex items-center">--}}
{{--                                <input type="checkbox" name="is_installment" id="is_installment" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_installment') ? 'checked' : '' }}>--}}
{{--                                <span class="ml-2 text-gray-700">Is Installment Sale?</span>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--                        <div class="mb-6">--}}
{{--                            <input type="hidden" name="credit_sale" value="0">--}}
{{--                            <label class="inline-flex items-center">--}}
{{--                                <input type="checkbox" name="credit_sale" id="credit_sale" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('credit_sale') ? 'checked' : '' }}>--}}
{{--                                <span class="ml-2 text-gray-700">Credit Sale?</span>--}}
{{--                            </label>--}}
{{--                        </div>--}}
{{--            </div>--}}
{{--            <div id="installment-details" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 {{ old('is_installment') ? '' : 'hidden' }}">--}}
{{--                <div>--}}
{{--                    <label for="total_installments" class="block text-gray-700 text-sm font-bold mb-2">Total Installments:</label>--}}
{{--                    <input type="number" name="total_installments" id="total_installments" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('total_installments') border-red-500 @enderror" value="{{ old('total_installments') }}" min="1">--}}
{{--                    @error('total_installments')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <label for="installment_amount" class="block text-gray-700 text-sm font-bold mb-2">Installment Amount ($ per installment):</label>--}}
{{--                    <input type="number" step="0.01" name="installment_amount" id="installment_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('installment_amount') border-red-500 @enderror" value="{{ old('installment_amount') }}" min="0.01">--}}
{{--                    @error('installment_amount')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--                <div>--}}
{{--                    <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">Installment Start Date:</label>--}}
{{--                    <input type="date" name="start_date" id="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('start_date') border-red-500 @enderror" value="{{ old('start_date', date('Y-m-d')) }}">--}}
{{--                    @error('start_date')--}}
{{--                    <p class="text-red-500 text-xs italic">{{ $message }}</p>--}}
{{--                    @enderror--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="flex items-center justify-between mt-6">--}}
{{--                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">--}}
{{--                    Record Sale--}}
{{--                </button>--}}
{{--                <a href="{{ route('sales.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">--}}
{{--                    View All Sales--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </form>--}}
{{--    </div>--}}

{{--    <script>--}}
{{--        // Pass available phones and accessories data to JavaScript--}}
{{--        const availablePhones = @json($availablePhones);--}}
{{--        const availableAccessories = @json($availableAccessories);--}}

{{--        const phoneSearchInput = document.getElementById('phoneSearchInput');--}}
{{--        const phoneSearchResults = document.getElementById('phone-search-results');--}}
{{--        const phoneImeiInputs = document.getElementById('phone-imei-inputs');--}}
{{--        let phoneIndex = 0; // New counter for phone input names--}}

{{--        const accessorySearchInput = document.getElementById('accessorySearchInput');--}}
{{--        const accessorySearchResults = document.getElementById('accessory-search-results');--}}
{{--        const accessoryInputs = document.getElementById('accessory-inputs');--}}
{{--        let accessoryIndex = 0; // New counter for accessory input names--}}

{{--        /**--}}
{{--         * Renders phone search results as a clickable list.--}}
{{--         */--}}
{{--        function renderPhoneResults(results) {--}}
{{--            phoneSearchResults.innerHTML = '';--}}
{{--            if (results.length > 0) {--}}
{{--                results.forEach(phone => {--}}
{{--                    const brandName = phone.brand ? phone.brand.name : 'N/A';--}}
{{--                    const resultItem = document.createElement('div');--}}
{{--                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';--}}
{{--                    resultItem.textContent = `${phone.imei} - ${brandName} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}`;--}}
{{--                    resultItem.dataset.imei = phone.imei;--}}
{{--                    phoneSearchResults.appendChild(resultItem);--}}
{{--                });--}}
{{--                phoneSearchResults.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        }--}}

{{--        /**--}}
{{--         * Adds a new phone selection row to the form.--}}
{{--         * @param {Object} phone The phone object to add.--}}
{{--         */--}}
{{--        function addPhoneInput(phone) {--}}
{{--            // Check if the phone is already in the list--}}
{{--            const existingPhones = document.querySelectorAll('input[name="phone_imeis[]"]');--}}
{{--            for (let i = 0; i < existingPhones.length; i++) {--}}
{{--                if (existingPhones[i].value === phone.imei) {--}}
{{--                    showToast(`Phone with IMEI ${phone.imei} is already in the list.`, 'error');--}}
{{--                    return;--}}
{{--                }--}}
{{--            }--}}

{{--            const div = document.createElement('div');--}}
{{--            div.className = 'flex items-center gap-2 phone-item-group mb-2';--}}

{{--            const brandName = phone.brand ? phone.brand.name : 'N/A';--}}
{{--            div.innerHTML = `--}}
{{--                <input type="hidden" name="phone_imeis[]" value="${phone.imei}">--}}
{{--                <input type="text" value="${phone.imei} - ${brandName} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">--}}
{{--                <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>--}}
{{--            `;--}}
{{--            phoneImeiInputs.appendChild(div);--}}
{{--            showToast(`Phone ${phone.imei} added successfully.`, 'success');--}}
{{--        }--}}

{{--        /**--}}
{{--         * Renders accessory search results as a clickable list.--}}
{{--         */--}}
{{--        function renderAccessoryResults(results) {--}}
{{--            accessorySearchResults.innerHTML = '';--}}
{{--            if (results.length > 0) {--}}
{{--                results.forEach(accessory => {--}}
{{--                    const brandName = accessory.brand ? accessory.brand.name : 'N/A';--}}
{{--                    const resultItem = document.createElement('div');--}}
{{--                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';--}}
{{--                    resultItem.textContent = `${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}`;--}}
{{--                    resultItem.dataset.id = accessory.id;--}}
{{--                    accessorySearchResults.appendChild(resultItem);--}}
{{--                });--}}
{{--                accessorySearchResults.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        }--}}

{{--        /**--}}
{{--         * Adds a new accessory selection row to the form.--}}
{{--         * @param {Object} accessory The accessory object to add.--}}
{{--         */--}}
{{--        function addAccessoryInput(accessory) {--}}
{{--            // Check if the accessory is already in the list--}}
{{--            const existingAccessoryInput = document.querySelector(`input[name*="[id]"][value="${accessory.id}"]`);--}}
{{--            if (existingAccessoryInput) {--}}
{{--                // If it exists, just increment the quantity--}}
{{--                const quantityInput = existingAccessoryInput.parentNode.querySelector('input[name*="[quantity]"]');--}}
{{--                quantityInput.value = parseInt(quantityInput.value) + 1;--}}
{{--                showToast(`Quantity for ${accessory.name} incremented.`, 'success');--}}
{{--                return;--}}
{{--            }--}}

{{--            const div = document.createElement('div');--}}
{{--            div.className = 'flex items-center gap-2 accessory-item-group mb-2';--}}

{{--            const brandName = accessory.brand ? accessory.brand.name : 'N/A';--}}
{{--            div.innerHTML = `--}}
{{--                <input type="hidden" name="accessories[${accessoryIndex}][id]" value="${accessory.id}">--}}
{{--                <input type="text" value="${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}" readonly class="shadow appearance-none border rounded w-2/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">--}}
{{--                <input type="number" name="accessories[${accessoryIndex}][quantity]" min="1" value="1" class="shadow appearance-none border rounded w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Quantity">--}}
{{--                <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>--}}
{{--            `;--}}
{{--            accessoryInputs.appendChild(div);--}}
{{--            accessoryIndex++; // Increment the counter--}}
{{--            showToast(`Accessory ${accessory.name} added successfully.`, 'success');--}}
{{--        }--}}

{{--        // Live search for phones--}}
{{--        phoneSearchInput.addEventListener('input', (event) => {--}}
{{--            const searchTerm = event.target.value.toLowerCase();--}}
{{--            if (searchTerm.length > 0) {--}}
{{--                const filteredPhones = availablePhones.filter(phone => {--}}
{{--                    const brandName = phone.brand ? phone.brand.name.toLowerCase() : '';--}}
{{--                    return phone.imei.toLowerCase().includes(searchTerm) ||--}}
{{--                        brandName.includes(searchTerm) ||--}}
{{--                        phone.model.toLowerCase().includes(searchTerm) ||--}}
{{--                        phone.color.toLowerCase().includes(searchTerm);--}}
{{--                });--}}
{{--                renderPhoneResults(filteredPhones);--}}
{{--            } else {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Handle selection from phone search results--}}
{{--        phoneSearchResults.addEventListener('click', (event) => {--}}
{{--            const imei = event.target.dataset.imei;--}}
{{--            if (imei) {--}}
{{--                const selectedPhone = availablePhones.find(p => p.imei === imei);--}}
{{--                if (selectedPhone) {--}}
{{--                    addPhoneInput(selectedPhone);--}}
{{--                    phoneSearchInput.value = '';--}}
{{--                    phoneSearchResults.classList.add('hidden');--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        // Live search for accessories--}}
{{--        accessorySearchInput.addEventListener('input', (event) => {--}}
{{--            const searchTerm = event.target.value.toLowerCase();--}}
{{--            if (searchTerm.length > 0) {--}}
{{--                const filteredAccessories = availableAccessories.filter(accessory => {--}}
{{--                    const brandName = accessory.brand ? accessory.brand.name.toLowerCase() : '';--}}
{{--                    return accessory.name.toLowerCase().includes(searchTerm) ||--}}
{{--                        brandName.includes(searchTerm);--}}
{{--                });--}}
{{--                renderAccessoryResults(filteredAccessories);--}}
{{--            } else {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Handle selection from accessory search results--}}
{{--        accessorySearchResults.addEventListener('click', (event) => {--}}
{{--            const id = parseInt(event.target.dataset.id);--}}
{{--            if (id) {--}}
{{--                const selectedAccessory = availableAccessories.find(a => a.id === id);--}}
{{--                if (selectedAccessory) {--}}
{{--                    addAccessoryInput(selectedAccessory);--}}
{{--                    accessorySearchInput.value = '';--}}
{{--                    accessorySearchResults.classList.add('hidden');--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        /**--}}
{{--         * Removes the parent div of the clicked button.--}}
{{--         * @param {HTMLElement} button The remove button element.--}}
{{--         */--}}
{{--        function removeInput(button) {--}}
{{--            button.closest('.phone-item-group, .accessory-item-group').remove();--}}
{{--        }--}}

{{--        // Toggle installment details visibility--}}
{{--        document.getElementById('is_installment').addEventListener('change', function() {--}}
{{--            const installmentDetails = document.getElementById('installment-details');--}}
{{--            if (this.checked) {--}}
{{--                installmentDetails.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                installmentDetails.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Close search results when clicking outside--}}
{{--        document.addEventListener('click', (event) => {--}}
{{--            if (!phoneSearchInput.contains(event.target) && !phoneSearchResults.contains(event.target)) {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--            if (!accessorySearchInput.contains(event.target) && !accessorySearchResults.contains(event.target)) {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // --- NEW BARCODE SCANNER LOGIC WITH TOASTS -----}}
{{--        let barcode = '';--}}
{{--        let barcodeTimeout;--}}
{{--        const barcodeTimeoutDuration = 200; // Time in ms to wait for the next character--}}

{{--        document.addEventListener('keypress', (event) => {--}}
{{--            const activeElement = document.activeElement;--}}
{{--            if (activeElement.id === 'phoneSearchInput' || activeElement.id === 'accessorySearchInput') {--}}
{{--                return;--}}
{{--            }--}}

{{--            if (event.key === 'Enter' || event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}

{{--                if (barcode.length > 0) {--}}
{{--                    const foundPhone = availablePhones.find(p => p.imei.toLowerCase() === barcode.toLowerCase());--}}
{{--                    if (foundPhone) {--}}
{{--                        addPhoneInput(foundPhone);--}}
{{--                    } else {--}}
{{--                        const foundAccessory = availableAccessories.find(a => a.id.toString() === barcode);--}}
{{--                        if (foundAccessory) {--}}
{{--                            addAccessoryInput(foundAccessory);--}}
{{--                        } else {--}}
{{--                            showToast(`No product found for barcode: ${barcode}`, 'error');--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}

{{--                barcode = '';--}}
{{--            } else {--}}
{{--                barcode += event.key;--}}
{{--                clearTimeout(barcodeTimeout);--}}
{{--                barcodeTimeout = setTimeout(() => {--}}
{{--                    barcode = '';--}}
{{--                }, barcodeTimeoutDuration);--}}
{{--            }--}}
{{--        });--}}

{{--        /**--}}
{{--         * Displays a temporary toast notification.--}}
{{--         * @param {string} message The message to display.--}}
{{--         * @param {'success'|'error'} type The type of toast (determines color).--}}
{{--         */--}}
{{--        function showToast(message, type) {--}}
{{--            // Remove any existing toasts to prevent stacking--}}
{{--            const existingToasts = document.querySelectorAll('.toast');--}}
{{--            existingToasts.forEach(toast => toast.remove());--}}

{{--            const toastContainer = document.createElement('div');--}}
{{--            const toastClasses = type === 'success'--}}
{{--                ? 'bg-green-500 text-white'--}}
{{--                : 'bg-red-500 text-white';--}}

{{--            toastContainer.className = `toast fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transition-opacity duration-300 ease-in-out opacity-0 ${toastClasses}`;--}}
{{--            toastContainer.textContent = message;--}}

{{--            document.body.appendChild(toastContainer);--}}

{{--            // Animate the toast in and out--}}
{{--            setTimeout(() => {--}}
{{--                toastContainer.style.opacity = 1;--}}
{{--            }, 10);--}}

{{--            setTimeout(() => {--}}
{{--                toastContainer.style.opacity = 0;--}}
{{--                setTimeout(() => toastContainer.remove(), 500); // Remove after fade out--}}
{{--            }, 3000); // Display for 3 seconds--}}
{{--        }--}}
{{--    </script>--}}
{{--@endsection--}}



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
                    <label for="customer_phone" class="block text-gray-700 text-sm font-bold mb-2">Customer Phone (Optional):</label>
                    <input type="text" name="customer_phone" id="customer_phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_phone') border-red-500 @enderror" value="{{ old('customer_phone') }}" placeholder="e.g., +2557XXXXXXXX">
                    @error('customer_phone')
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
                <!-- Phones to Sell Section -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Phones to Sell (IMEI):</label>
                    <div class="relative">
                        <input type="text" id="phoneSearchInput" placeholder="Search by IMEI or model..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div id="phone-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                    <div id="phone-imei-inputs" class="mt-2">
                        <!-- Dynamic phone inputs will be added here -->
                    </div>
                </div>

                <!-- Accessories to Sell Section -->
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Accessories to Sell:</label>
                    <div class="relative">
                        <input type="text" id="accessorySearchInput" placeholder="Search by name or brand..." class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <div id="accessory-search-results" class="absolute z-10 w-full bg-white border border-gray-300 rounded mt-1 shadow-lg max-h-48 overflow-y-auto hidden"></div>
                    </div>
                    <div id="accessory-inputs" class="mt-2">
                        <!-- Dynamic accessory inputs will be added here -->
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
        // Pass available phones and accessories data to JavaScript
        const availablePhones = @json($availablePhones);
        const availableAccessories = @json($availableAccessories);

        const form = document.querySelector('form');
        const phoneSearchInput = document.getElementById('phoneSearchInput');
        const phoneSearchResults = document.getElementById('phone-search-results');
        const phoneImeiInputs = document.getElementById('phone-imei-inputs');

        const accessorySearchInput = document.getElementById('accessorySearchInput');
        const accessorySearchResults = document.getElementById('accessory-search-results');
        const accessoryInputs = document.getElementById('accessory-inputs');
        let accessoryIndex = 0;

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
            // Check if we're focused on accessory search
            const isAccessoryFocused = document.activeElement === accessorySearchInput;
            const isPhoneFocused = document.activeElement === phoneSearchInput;

            // If focused on accessory search, only look for accessories
            if (isAccessoryFocused) {
                const accessory = availableAccessories.find(a =>
                    a.barcode === code || a.id.toString() === code
                );
                if (accessory) {
                    addAccessoryInput(accessory);
                    showToast(`Added accessory: ${accessory.name}`, 'success');
                    return;
                }
                showToast(`No accessory found for barcode: ${code}`, 'error');
                return;
            }

            // If focused on phone search, only look for phones
            if (isPhoneFocused) {
                const phone = availablePhones.find(p => p.imei === code);
                if (phone) {
                    addPhoneInput(phone);
                    showToast(`Added phone: ${phone.model} (${phone.imei})`, 'success');
                    return;
                }
                showToast(`No phone found for barcode: ${code}`, 'error');
                return;
            }

            // Default behavior when not focused on any search input
            // First try phones
            const phone = availablePhones.find(p => p.imei === code);
            if (phone) {
                addPhoneInput(phone);
                showToast(`Added phone: ${phone.model} (${phone.imei})`, 'success');
                return;
            }

            // Then try accessories
            const accessory = availableAccessories.find(a =>
                a.barcode === code || a.id.toString() === code
            );
            if (accessory) {
                addAccessoryInput(accessory);
                showToast(`Added accessory: ${accessory.name}`, 'success');
                return;
            }

            showToast(`No product found for barcode: ${code}`, 'error');
        }

        // --- AUTO-SEARCH FUNCTIONS ---
        // For Phones
        phoneSearchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            if (searchTerm.length > 0) {
                const filteredPhones = availablePhones.filter(phone => {
                    const brandName = phone.brand ? phone.brand.name.toLowerCase() : '';
                    return phone.imei.toLowerCase().includes(searchTerm) ||
                        brandName.includes(searchTerm) ||
                        phone.model.toLowerCase().includes(searchTerm) ||
                        phone.color.toLowerCase().includes(searchTerm);
                });
                renderPhoneResults(filteredPhones);
            } else {
                phoneSearchResults.classList.add('hidden');
            }
        });

        // For Accessories (NEW - matches phone search functionality)
        accessorySearchInput.addEventListener('input', (event) => {
            const searchTerm = event.target.value.toLowerCase();
            if (searchTerm.length > 0) {
                const filteredAccessories = availableAccessories.filter(accessory => {
                    const brandName = accessory.brand ? accessory.brand.name.toLowerCase() : '';
                    return accessory.name.toLowerCase().includes(searchTerm) ||
                        brandName.includes(searchTerm) ||
                        (accessory.barcode && accessory.barcode.toString().includes(searchTerm)) ||
                        (accessory.id && accessory.id.toString().includes(searchTerm));
                });
                renderAccessoryResults(filteredAccessories);
            } else {
                accessorySearchResults.classList.add('hidden');
            }
        });

        function renderPhoneResults(results) {
            phoneSearchResults.innerHTML = '';
            if (results.length > 0) {
                results.forEach(phone => {
                    const brandName = phone.brand ? phone.brand.name : 'N/A';
                    const resultItem = document.createElement('div');
                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';
                    resultItem.textContent = `${phone.imei} - ${brandName} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}`;
                    resultItem.dataset.imei = phone.imei;
                    phoneSearchResults.appendChild(resultItem);
                });
                phoneSearchResults.classList.remove('hidden');
            } else {
                phoneSearchResults.classList.add('hidden');
            }
        }

        function renderAccessoryResults(results) {
            accessorySearchResults.innerHTML = '';
            if (results.length > 0) {
                results.forEach(accessory => {
                    const brandName = accessory.brand ? accessory.brand.name : 'N/A';
                    const resultItem = document.createElement('div');
                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';
                    resultItem.textContent = `${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}`;
                    resultItem.dataset.id = accessory.id;
                    accessorySearchResults.appendChild(resultItem);
                });
                accessorySearchResults.classList.remove('hidden');
            } else {
                accessorySearchResults.classList.add('hidden');
            }
        }

        // --- ADD ITEM FUNCTIONS ---
        function addPhoneInput(phone) {
            // Check if already added
            if (document.querySelector(`input[name="phone_imeis[]"][value="${phone.imei}"]`)) {
                showToast('This phone is already in the list', 'warning');
                return;
            }

            const div = document.createElement('div');
            div.className = 'flex items-center gap-2 phone-item-group mb-2';
            div.innerHTML = `
            <input type="hidden" name="phone_imeis[]" value="${phone.imei}">
            <input type="text" value="${phone.imei} - ${phone.model}" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
            <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>
        `;
            phoneImeiInputs.appendChild(div);
        }

        function addAccessoryInput(accessory) {
            // Check if already added
            const existingAccessories = document.querySelectorAll('input[name^="accessories"]');
            for (let i = 0; i < existingAccessories.length; i++) {
                if (existingAccessories[i].value === accessory.id.toString()) {
                    showToast(`Accessory ${accessory.name} is already in the list`, 'warning');
                    return;
                }
            }
            const div = document.createElement('div');
            const brandName = accessory.brand ? accessory.brand.name : 'N/A';
            div.innerHTML = `
    <input type="hidden" name="accessories[${accessoryIndex}][id]" value="${accessory.id}">
    <input type="text" value="${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}" readonly
           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">
    <button type="button" onclick="removeInput(this)"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
        Remove
    </button>
    <input type="number" name="accessories[${accessoryIndex}][quantity]" value="1" min="1"
           class="w-20 text-center shadow appearance-none border rounded py-2 px-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
`;

            accessoryInputs.appendChild(div);
            accessoryIndex++;
        }

        // --- UTILITY FUNCTIONS ---
        function removeInput(button) {
            button.closest('.phone-item-group, .accessory-item-group').remove();
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
        phoneSearchResults.addEventListener('click', (event) => {
            const imei = event.target.dataset.imei;
            if (imei) {
                const selectedPhone = availablePhones.find(p => p.imei === imei);
                if (selectedPhone) {
                    addPhoneInput(selectedPhone);
                    phoneSearchInput.value = '';
                    phoneSearchResults.classList.add('hidden');
                }
            }
        });

        accessorySearchResults.addEventListener('click', (event) => {
            const id = parseInt(event.target.dataset.id);
            if (id) {
                const selectedAccessory = availableAccessories.find(a => a.id === id);
                if (selectedAccessory) {
                    addAccessoryInput(selectedAccessory);
                    accessorySearchInput.value = '';
                    accessorySearchResults.classList.add('hidden');
                }
            }
        });

        // Close search results when clicking outside
        document.addEventListener('click', (event) => {
            if (!phoneSearchInput.contains(event.target) && !phoneSearchResults.contains(event.target)) {
                phoneSearchResults.classList.add('hidden');
            }
            if (!accessorySearchInput.contains(event.target) && !accessorySearchResults.contains(event.target)) {
                accessorySearchResults.classList.add('hidden');
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



{{--    <script>--}}
{{--        // Pass available phones and accessories data to JavaScript--}}
{{--        const availablePhones = @json($availablePhones);--}}
{{--        const availableAccessories = @json($availableAccessories);--}}

{{--        const phoneSearchInput = document.getElementById('phoneSearchInput');--}}
{{--        const phoneSearchResults = document.getElementById('phone-search-results');--}}
{{--        const phoneImeiInputs = document.getElementById('phone-imei-inputs');--}}
{{--        let phoneIndex = 0; // New counter for phone input names--}}

{{--        const accessorySearchInput = document.getElementById('accessorySearchInput');--}}
{{--        const accessorySearchResults = document.getElementById('accessory-search-results');--}}
{{--        const accessoryInputs = document.getElementById('accessory-inputs');--}}
{{--        let accessoryIndex = 0; // New counter for accessory input names--}}

{{--        /**--}}
{{--         * Renders phone search results as a clickable list.--}}
{{--         */--}}
{{--        function renderPhoneResults(results) {--}}
{{--            phoneSearchResults.innerHTML = '';--}}
{{--            if (results.length > 0) {--}}
{{--                results.forEach(phone => {--}}
{{--                    const brandName = phone.brand ? phone.brand.name : 'N/A';--}}
{{--                    const resultItem = document.createElement('div');--}}
{{--                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';--}}
{{--                    resultItem.textContent = `${phone.imei} - ${brandName} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}`;--}}
{{--                    resultItem.dataset.imei = phone.imei;--}}
{{--                    phoneSearchResults.appendChild(resultItem);--}}
{{--                });--}}
{{--                phoneSearchResults.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        }--}}

{{--        /**--}}
{{--         * Adds a new phone selection row to the form.--}}
{{--         * @param {Object} phone The phone object to add.--}}
{{--         */--}}
{{--        function addPhoneInput(phone) {--}}
{{--            // Check if the phone is already in the list--}}
{{--            const existingPhones = document.querySelectorAll('input[name="phone_imeis[]"]');--}}
{{--            for (let i = 0; i < existingPhones.length; i++) {--}}
{{--                if (existingPhones[i].value === phone.imei) {--}}
{{--                    showToast(`Phone with IMEI ${phone.imei} is already in the list.`, 'error');--}}
{{--                    return;--}}
{{--                }--}}
{{--            }--}}

{{--            const div = document.createElement('div');--}}
{{--            div.className = 'flex items-center gap-2 phone-item-group mb-2';--}}

{{--            const brandName = phone.brand ? phone.brand.name : 'N/A';--}}
{{--            div.innerHTML = `--}}
{{--                <input type="hidden" name="phone_imeis[]" value="${phone.imei}">--}}
{{--                <input type="text" value="${phone.imei} - ${brandName} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}" readonly class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">--}}
{{--                <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>--}}
{{--            `;--}}
{{--            phoneImeiInputs.appendChild(div);--}}
{{--            showToast(`Phone ${phone.imei} added successfully.`, 'success');--}}
{{--        }--}}

{{--        /**--}}
{{--         * Renders accessory search results as a clickable list.--}}
{{--         */--}}
{{--        function renderAccessoryResults(results) {--}}
{{--            accessorySearchResults.innerHTML = '';--}}
{{--            if (results.length > 0) {--}}
{{--                results.forEach(accessory => {--}}
{{--                    const brandName = accessory.brand ? accessory.brand.name : 'N/A';--}}
{{--                    const resultItem = document.createElement('div');--}}
{{--                    resultItem.className = 'p-2 cursor-pointer hover:bg-gray-200';--}}
{{--                    resultItem.textContent = `${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}`;--}}
{{--                    resultItem.dataset.id = accessory.id;--}}
{{--                    accessorySearchResults.appendChild(resultItem);--}}
{{--                });--}}
{{--                accessorySearchResults.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        }--}}

{{--        /**--}}
{{--         * Adds a new accessory selection row to the form.--}}
{{--         * @param {Object} accessory The accessory object to add.--}}
{{--         */--}}
{{--        function addAccessoryInput(accessory) {--}}
{{--            // Check if the accessory is already in the list--}}
{{--            const existingAccessoryInput = document.querySelector(`input[name*="[id]"][value="${accessory.id}"]`);--}}
{{--            if (existingAccessoryInput) {--}}
{{--                // If it exists, just increment the quantity--}}
{{--                const quantityInput = existingAccessoryInput.parentNode.querySelector('input[name*="[quantity]"]');--}}
{{--                quantityInput.value = parseInt(quantityInput.value) + 1;--}}
{{--                showToast(`Quantity for ${accessory.name} incremented.`, 'success');--}}
{{--                return;--}}
{{--            }--}}

{{--            const div = document.createElement('div');--}}
{{--            div.className = 'flex items-center gap-2 accessory-item-group mb-2';--}}

{{--            const brandName = accessory.brand ? accessory.brand.name : 'N/A';--}}
{{--            div.innerHTML = `--}}
{{--                <input type="hidden" name="accessories[${accessoryIndex}][id]" value="${accessory.id}">--}}
{{--                <input type="text" value="${accessory.name} - ${brandName} - $${parseFloat(accessory.selling_price).toFixed(2)}" readonly class="shadow appearance-none border rounded w-2/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline bg-gray-100">--}}
{{--                <input type="number" name="accessories[${accessoryIndex}][quantity]" min="1" value="1" class="shadow appearance-none border rounded w-1/3 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Quantity">--}}
{{--                <button type="button" onclick="removeInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>--}}
{{--            `;--}}
{{--            accessoryInputs.appendChild(div);--}}
{{--            accessoryIndex++; // Increment the counter--}}
{{--            showToast(`Accessory ${accessory.name} added successfully.`, 'success');--}}
{{--        }--}}

{{--        // Live search for phones--}}
{{--        phoneSearchInput.addEventListener('input', (event) => {--}}
{{--            const searchTerm = event.target.value.toLowerCase();--}}
{{--            if (searchTerm.length > 0) {--}}
{{--                const filteredPhones = availablePhones.filter(phone => {--}}
{{--                    const brandName = phone.brand ? phone.brand.name.toLowerCase() : '';--}}
{{--                    return phone.imei.toLowerCase().includes(searchTerm) ||--}}
{{--                        brandName.includes(searchTerm) ||--}}
{{--                        phone.model.toLowerCase().includes(searchTerm) ||--}}
{{--                        phone.color.toLowerCase().includes(searchTerm);--}}
{{--                });--}}
{{--                renderPhoneResults(filteredPhones);--}}
{{--            } else {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Handle selection from phone search results--}}
{{--        phoneSearchResults.addEventListener('click', (event) => {--}}
{{--            const imei = event.target.dataset.imei;--}}
{{--            if (imei) {--}}
{{--                const selectedPhone = availablePhones.find(p => p.imei === imei);--}}
{{--                if (selectedPhone) {--}}
{{--                    addPhoneInput(selectedPhone);--}}
{{--                    phoneSearchInput.value = '';--}}
{{--                    phoneSearchResults.classList.add('hidden');--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        // Live search for accessories--}}
{{--        accessorySearchInput.addEventListener('input', (event) => {--}}
{{--            const searchTerm = event.target.value.toLowerCase();--}}
{{--            if (searchTerm.length > 0) {--}}
{{--                const filteredAccessories = availableAccessories.filter(accessory => {--}}
{{--                    const brandName = accessory.brand ? accessory.brand.name.toLowerCase() : '';--}}
{{--                    return accessory.name.toLowerCase().includes(searchTerm) ||--}}
{{--                        brandName.includes(searchTerm);--}}
{{--                });--}}
{{--                renderAccessoryResults(filteredAccessories);--}}
{{--            } else {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Handle selection from accessory search results--}}
{{--        accessorySearchResults.addEventListener('click', (event) => {--}}
{{--            const id = parseInt(event.target.dataset.id);--}}
{{--            if (id) {--}}
{{--                const selectedAccessory = availableAccessories.find(a => a.id === id);--}}
{{--                if (selectedAccessory) {--}}
{{--                    addAccessoryInput(selectedAccessory);--}}
{{--                    accessorySearchInput.value = '';--}}
{{--                    accessorySearchResults.classList.add('hidden');--}}
{{--                }--}}
{{--            }--}}
{{--        });--}}

{{--        /**--}}
{{--         * Removes the parent div of the clicked button.--}}
{{--         * @param {HTMLElement} button The remove button element.--}}
{{--         */--}}
{{--        function removeInput(button) {--}}
{{--            button.closest('.phone-item-group, .accessory-item-group').remove();--}}
{{--        }--}}

{{--        // Toggle installment details visibility--}}
{{--        document.getElementById('is_installment').addEventListener('change', function() {--}}
{{--            const installmentDetails = document.getElementById('installment-details');--}}
{{--            if (this.checked) {--}}
{{--                installmentDetails.classList.remove('hidden');--}}
{{--            } else {--}}
{{--                installmentDetails.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // Close search results when clicking outside--}}
{{--        document.addEventListener('click', (event) => {--}}
{{--            if (!phoneSearchInput.contains(event.target) && !phoneSearchResults.contains(event.target)) {--}}
{{--                phoneSearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--            if (!accessorySearchInput.contains(event.target) && !accessorySearchResults.contains(event.target)) {--}}
{{--                accessorySearchResults.classList.add('hidden');--}}
{{--            }--}}
{{--        });--}}

{{--        // --- NEW BARCODE SCANNER LOGIC WITH TOASTS -----}}
{{--        let barcode = '';--}}
{{--        let barcodeTimeout;--}}
{{--        const barcodeTimeoutDuration = 200; // Time in ms to wait for the next character--}}

{{--        document.addEventListener('keypress', (event) => {--}}
{{--            const activeElement = document.activeElement;--}}
{{--            if (activeElement.id === 'phoneSearchInput' || activeElement.id === 'accessorySearchInput') {--}}
{{--                return;--}}
{{--            }--}}

{{--            if (event.key === 'Enter' || event.keyCode === 13) {--}}
{{--                event.preventDefault();--}}

{{--                if (barcode.length > 0) {--}}
{{--                    const foundPhone = availablePhones.find(p => p.imei.toLowerCase() === barcode.toLowerCase());--}}
{{--                    if (foundPhone) {--}}
{{--                        addPhoneInput(foundPhone);--}}
{{--                    } else {--}}
{{--                        const foundAccessory = availableAccessories.find(a => a.id.toString() === barcode);--}}
{{--                        if (foundAccessory) {--}}
{{--                            addAccessoryInput(foundAccessory);--}}
{{--                        } else {--}}
{{--                            showToast(`No product found for barcode: ${barcode}`, 'error');--}}
{{--                        }--}}
{{--                    }--}}
{{--                }--}}

{{--                barcode = '';--}}
{{--            } else {--}}
{{--                barcode += event.key;--}}
{{--                clearTimeout(barcodeTimeout);--}}
{{--                barcodeTimeout = setTimeout(() => {--}}
{{--                    barcode = '';--}}
{{--                }, barcodeTimeoutDuration);--}}
{{--            }--}}
{{--        });--}}

{{--        /**--}}
{{--         * Displays a temporary toast notification.--}}
{{--         * @param {string} message The message to display.--}}
{{--         * @param {'success'|'error'} type The type of toast (determines color).--}}
{{--         */--}}
{{--        function showToast(message, type) {--}}
{{--            // Remove any existing toasts to prevent stacking--}}
{{--            const existingToasts = document.querySelectorAll('.toast');--}}
{{--            existingToasts.forEach(toast => toast.remove());--}}

{{--            const toastContainer = document.createElement('div');--}}
{{--            const toastClasses = type === 'success'--}}
{{--                ? 'bg-green-500 text-white'--}}
{{--                : 'bg-red-500 text-white';--}}

{{--            toastContainer.className = `toast fixed bottom-4 right-4 z-50 p-4 rounded-lg shadow-xl transition-opacity duration-300 ease-in-out opacity-0 ${toastClasses}`;--}}
{{--            toastContainer.textContent = message;--}}

{{--            document.body.appendChild(toastContainer);--}}

{{--            // Animate the toast in and out--}}
{{--            setTimeout(() => {--}}
{{--                toastContainer.style.opacity = 1;--}}
{{--            }, 10);--}}

{{--            setTimeout(() => {--}}
{{--                toastContainer.style.opacity = 0;--}}
{{--                setTimeout(() => toastContainer.remove(), 500); // Remove after fade out--}}
{{--            }, 3000); // Display for 3 seconds--}}
{{--        }--}}
{{--    </script>--}}
@endsection
