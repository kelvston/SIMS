@extends('layouts.app')

@section('content')
    <style>
        .imei-input-group {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
        }
        .imei-input-group input {
            flex-grow: 1;
        }
        .imei-input-group button {
            margin-left: 8px;
        }
        .autocomplete-wrap {
            position: relative;
        }
        .autocomplete-list {
            position: absolute;
            z-index: 30;
            width: 100%;
            max-height: 220px;
            overflow-y: auto;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            margin-top: 0.25rem;
        }
        .autocomplete-option {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
        }
        .autocomplete-option:hover,
        .autocomplete-option.active {
            background: #eff6ff;
        }
        .autocomplete-empty {
            color: #6b7280;
            font-size: 0.875rem;
        }
    </style>
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-60%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Receive Inventory</h1>

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

    <form action="{{ route('phones.receive.store') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="product_type" class="block text-gray-700 text-sm font-bold mb-2">Product Type:</label>
            <select name="product_type" id="product_type" class="shadow appearance-none border rounded w-full md:w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <option value="phone" {{ old('product_type', 'phone') === 'phone' ? 'selected' : '' }}>Phone</option>
                <option value="accessory" {{ old('product_type') === 'accessory' ? 'selected' : '' }}>Accessory</option>
            </select>
        </div>

        <div id="phone-receive-fields">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="brand_id" class="block text-gray-700 text-sm font-bold mb-2">Brand:</label>
                <select name="brand_id" id="brand_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('brand_id') border-red-500 @enderror">
                    <option value="">Select a Brand</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
                @error('brand_id')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="model" class="block text-gray-700 text-sm font-bold mb-2">Model:</label>
                <div class="autocomplete-wrap">
                    <input type="text" name="model" id="model" class="autocomplete-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('model') border-red-500 @enderror" value="{{ old('model') }}" placeholder="Search or type new model" autocomplete="off" disabled>
                    <div id="model_suggestions" class="autocomplete-list hidden"></div>
                </div>
                @error('model')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="color" class="block text-gray-700 text-sm font-bold mb-2">Color:</label>
                <div class="autocomplete-wrap">
                    <input type="text" name="color" id="color" class="autocomplete-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('color') border-red-500 @enderror" value="{{ old('color') }}" placeholder="Search or type new color" autocomplete="off" disabled>
                    <div id="color_suggestions" class="autocomplete-list hidden"></div>
                </div>
                @error('color')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="storage_capacity" class="block text-gray-700 text-sm font-bold mb-2">Storage Capacity:</label>
                <div class="autocomplete-wrap">
                    <input type="text" name="storage_capacity" id="storage_capacity" class="autocomplete-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('storage_capacity') border-red-500 @enderror" value="{{ old('storage_capacity') }}" placeholder="Search or type new storage, e.g. 2TB" autocomplete="off" disabled>
                    <div id="storage_capacity_suggestions" class="autocomplete-list hidden"></div>
                </div>
                @error('storage_capacity')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="purchase_price" class="block text-gray-700 text-sm font-bold mb-2">Purchase Price:</label>
                <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('purchase_price') border-red-500 @enderror" value="{{ old('purchase_price') }}" placeholder="e.g., 500.00">
                @error('purchase_price')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="selling_price" class="block text-gray-700 text-sm font-bold mb-2">Selling Price:</label>
                <input type="number" step="0.01" name="selling_price" id="selling_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('selling_price') border-red-500 @enderror" value="{{ old('selling_price') }}" placeholder="e.g., 750.00">
                @error('selling_price')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">IMEI Numbers:</label>
            <p id="imei-scan-message" class="text-sm mb-2"></p>
            <div id="imei-inputs">
                <!-- Initial IMEI input field -->
                <div class="imei-input-group">
                    <input type="text" name="imeis[]" class="imei-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter IMEI or scan barcode" autocomplete="off" autofocus>
                    <button type="button" onclick="addImeiInput()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Add</button>
                </div>
                @error('imeis')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
                @error('imeis.*')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
        </div>
        </div>

        <div id="accessory-receive-fields" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="accessory_name" class="block text-gray-700 text-sm font-bold mb-2">Accessory Name:</label>
                    <input type="text" name="accessory_name" id="accessory_name" list="accessory-name-list" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('accessory_name') border-red-500 @enderror" value="{{ old('accessory_name') }}" placeholder="Phone case, charger, USB cable, flash disk">
                    <datalist id="accessory-name-list">
                        @foreach ($accessories as $accessory)
                            <option value="{{ $accessory->name }}"></option>
                        @endforeach
                    </datalist>
                    @error('accessory_name')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="accessory_quantity" class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                    <input type="number" name="accessory_quantity" id="accessory_quantity" min="1" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('accessory_quantity') border-red-500 @enderror" value="{{ old('accessory_quantity') }}" placeholder="e.g., 20">
                    @error('accessory_quantity')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="accessory_purchase_price" class="block text-gray-700 text-sm font-bold mb-2">Purchase Price Per Unit:</label>
                    <input type="number" step="0.01" name="accessory_purchase_price" id="accessory_purchase_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('accessory_purchase_price') border-red-500 @enderror" value="{{ old('accessory_purchase_price') }}" placeholder="e.g., 3000.00">
                    @error('accessory_purchase_price')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="accessory_selling_price" class="block text-gray-700 text-sm font-bold mb-2">Selling Price Per Unit:</label>
                    <input type="number" step="0.01" name="accessory_selling_price" id="accessory_selling_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('accessory_selling_price') border-red-500 @enderror" value="{{ old('accessory_selling_price') }}" placeholder="e.g., 5000.00">
                    @error('accessory_selling_price')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="accessory_unit" class="block text-gray-700 text-sm font-bold mb-2">Unit:</label>
                    <input type="text" name="accessory_unit" id="accessory_unit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('accessory_unit', 'piece') }}">
                </div>
                <div>
                    <label for="accessory_low_stock_threshold" class="block text-gray-700 text-sm font-bold mb-2">Low Stock Threshold:</label>
                    <input type="number" name="accessory_low_stock_threshold" id="accessory_low_stock_threshold" min="0" step="1" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('accessory_low_stock_threshold', 5) }}">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                Receive Inventory
            </button>
            <a href="{{ route('phones.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                View All Phones
            </a>
        </div>
    </form>
</div>

<script>
    const brandOptions = @json($brandOptions);
    const oldValues = {
        brandId: @json((string) old('brand_id')),
        model: @json(old('model')),
        color: @json(old('color')),
        storageCapacity: @json(old('storage_capacity')),
    };
    const productTypeSelect = document.getElementById('product_type');
    let activeSuggestionIndex = -1;
    let currentSuggestions = [];
    let currentSuggestionInput = null;
    let dependentModelValue = oldValues.model || '';

    function optionMatchesSearch(optionValue, searchValue) {
        return String(optionValue || '').toLowerCase().includes(String(searchValue || '').toLowerCase());
    }

    function selectedBrandData() {
        const brandId = document.getElementById('brand_id').value;
        return brandOptions[brandId] || null;
    }

    function selectedModelData() {
        const brandData = selectedBrandData();
        const modelName = document.getElementById('model').value.trim();
        return brandData && brandData.models ? brandData.models[modelName] : null;
    }

    function modelOptions() {
        const brandData = selectedBrandData();
        return brandData && brandData.models ? Object.keys(brandData.models) : [];
    }

    function colorOptions() {
        const modelData = selectedModelData();
        return modelData ? modelData.colors : [];
    }

    function storageOptions() {
        const modelData = selectedModelData();
        return modelData ? modelData.storage_capacities : [];
    }

    function normalizeValue(value) {
        return String(value || '').trim();
    }

    function filteredOptions(options, query) {
        const normalizedQuery = normalizeValue(query);

        return [...new Set(options)]
            .filter(value => optionMatchesSearch(value, normalizedQuery))
            .slice(0, 25);
    }

    function suggestionsElement(input) {
        return document.getElementById(`${input.id}_suggestions`);
    }

    function closeSuggestions() {
        document.querySelectorAll('.autocomplete-list').forEach(list => {
            list.classList.add('hidden');
            list.innerHTML = '';
        });
        activeSuggestionIndex = -1;
        currentSuggestions = [];
        currentSuggestionInput = null;
    }

    function markActiveSuggestion() {
        const list = currentSuggestionInput ? suggestionsElement(currentSuggestionInput) : null;
        if (!list) {
            return;
        }

        Array.from(list.querySelectorAll('.autocomplete-option')).forEach((option, index) => {
            option.classList.toggle('active', index === activeSuggestionIndex);
        });
    }

    function chooseSuggestion(input, value) {
        input.value = value;
        closeSuggestions();

        if (input.id === 'model') {
            dependentModelValue = value;
            document.getElementById('color').value = '';
            document.getElementById('storage_capacity').value = '';
            updateAutocompleteAvailability();
            document.getElementById('color').focus();
        }
    }

    function openSuggestions(input, options, emptyText) {
        const list = suggestionsElement(input);
        const typedValue = normalizeValue(input.value);
        const matches = filteredOptions(options, typedValue);
        const exactMatch = matches.some(value => value.toLowerCase() === typedValue.toLowerCase());

        closeSuggestions();
        currentSuggestionInput = input;
        currentSuggestions = matches;

        matches.forEach(value => {
            const item = document.createElement('div');
            item.className = 'autocomplete-option';
            item.textContent = value;
            item.addEventListener('mousedown', function(event) {
                event.preventDefault();
                chooseSuggestion(input, value);
            });
            list.appendChild(item);
        });

        if (typedValue && !exactMatch) {
            const item = document.createElement('div');
            item.className = 'autocomplete-option';
            item.innerHTML = `Use new value: <strong>${typedValue}</strong>`;
            item.addEventListener('mousedown', function(event) {
                event.preventDefault();
                chooseSuggestion(input, typedValue);
            });
            list.appendChild(item);
            currentSuggestions.push(typedValue);
        }

        if (!list.children.length) {
            const item = document.createElement('div');
            item.className = 'autocomplete-option autocomplete-empty';
            item.textContent = emptyText;
            list.appendChild(item);
        }

        list.classList.remove('hidden');
    }

    function updateAutocompleteAvailability() {
        if (productTypeSelect.value !== 'phone') {
            return;
        }

        const brandSelected = Boolean(document.getElementById('brand_id').value);
        const modelValue = normalizeValue(document.getElementById('model').value);

        document.getElementById('model').disabled = !brandSelected;
        document.getElementById('color').disabled = !modelValue;
        document.getElementById('storage_capacity').disabled = !modelValue;
    }

    function setRequiredWithin(containerId, required) {
        const requiredNames = [
            'brand_id', 'model', 'color', 'storage_capacity', 'purchase_price', 'selling_price', 'imeis[]',
            'accessory_name', 'accessory_quantity', 'accessory_purchase_price', 'accessory_selling_price', 'accessory_unit'
        ];

        document.querySelectorAll(`#${containerId} input, #${containerId} select`).forEach(field => {
            field.required = required && requiredNames.includes(field.name);
        });
    }

    function toggleReceiveType() {
        const receivingPhone = productTypeSelect.value === 'phone';

        document.getElementById('phone-receive-fields').classList.toggle('hidden', !receivingPhone);
        document.getElementById('accessory-receive-fields').classList.toggle('hidden', receivingPhone);

        setRequiredWithin('phone-receive-fields', receivingPhone);
        setRequiredWithin('accessory-receive-fields', !receivingPhone);

        document.querySelectorAll('#phone-receive-fields input, #phone-receive-fields select').forEach(field => {
            field.disabled = !receivingPhone;
        });
        document.querySelectorAll('#accessory-receive-fields input, #accessory-receive-fields select').forEach(field => {
            field.disabled = receivingPhone;
        });

        if (receivingPhone) {
            updateAutocompleteAvailability();
        }
    }

    function bindAutocomplete(inputId, getOptions, emptyText) {
        const input = document.getElementById(inputId);

        input.addEventListener('focus', function() {
            if (!input.disabled) {
                openSuggestions(input, getOptions(), emptyText);
            }
        });

        input.addEventListener('input', function() {
            if (input.id === 'model' && normalizeValue(input.value) !== dependentModelValue) {
                dependentModelValue = normalizeValue(input.value);
                document.getElementById('color').value = '';
                document.getElementById('storage_capacity').value = '';
            }

            updateAutocompleteAvailability();
            openSuggestions(input, getOptions(), emptyText);
        });

        input.addEventListener('keydown', function(event) {
            if (suggestionsElement(input).classList.contains('hidden')) {
                return;
            }

            if (event.key === 'ArrowDown') {
                event.preventDefault();
                activeSuggestionIndex = Math.min(activeSuggestionIndex + 1, currentSuggestions.length - 1);
                markActiveSuggestion();
            }

            if (event.key === 'ArrowUp') {
                event.preventDefault();
                activeSuggestionIndex = Math.max(activeSuggestionIndex - 1, 0);
                markActiveSuggestion();
            }

            if (event.key === 'Enter' && activeSuggestionIndex >= 0) {
                event.preventDefault();
                chooseSuggestion(input, currentSuggestions[activeSuggestionIndex]);
            }

            if (event.key === 'Escape') {
                closeSuggestions();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const brandSelect = document.getElementById('brand_id');
        const modelInput = document.getElementById('model');
        const colorInput = document.getElementById('color');
        const storageInput = document.getElementById('storage_capacity');

        productTypeSelect.addEventListener('change', toggleReceiveType);
        bindAutocomplete('model', modelOptions, 'No saved models. Type a new model name.');
        bindAutocomplete('color', colorOptions, 'No saved colors. Type a new color.');
        bindAutocomplete('storage_capacity', storageOptions, 'No saved storage options. Type a new storage capacity.');

        brandSelect.addEventListener('change', function() {
            modelInput.value = '';
            colorInput.value = '';
            storageInput.value = '';
            dependentModelValue = '';
            updateAutocompleteAvailability();
            closeSuggestions();
        });

        document.addEventListener('mousedown', function(event) {
            if (!event.target.closest('.autocomplete-wrap')) {
                closeSuggestions();
            }
        });

        updateAutocompleteAvailability();
        toggleReceiveType();
    });

    // Function to add a new IMEI input field
    function addImeiInput() {
        const container = document.getElementById('imei-inputs');
        const div = document.createElement('div');
        div.className = 'imei-input-group';
        div.innerHTML = `
                <input type="text" name="imeis[]" class="imei-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter IMEI or scan barcode" autocomplete="off">
                <button type="button" onclick="removeImeiInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>
            `;
        container.appendChild(div);
        div.querySelector('input').focus(); // Focus on the newly added input
    }

    // Function to remove an IMEI input field
    function removeImeiInput(button) {
        button.closest('.imei-input-group').remove();
    }

    function normalizeImei(value) {
        return String(value || '').trim().replace(/[\r\n\t ]+/g, '');
    }

    function setImeiScanMessage(message, isError = false) {
        const messageElement = document.getElementById('imei-scan-message');
        messageElement.textContent = message;
        messageElement.className = `text-sm mb-2 ${isError ? 'text-red-600' : 'text-green-600'}`;
    }

    function enteredImeis() {
        return Array.from(document.querySelectorAll('input[name="imeis[]"]'))
            .map(input => normalizeImei(input.value))
            .filter(Boolean);
    }

    function focusNextImeiInput(currentInput) {
        const allInputs = Array.from(document.querySelectorAll('input[name="imeis[]"]'));
        const currentIndex = allInputs.indexOf(currentInput);
        const emptyInput = allInputs.find(input => input !== currentInput && normalizeImei(input.value) === '');

        if (emptyInput) {
            emptyInput.focus();
            return;
        }

        if (currentIndex === allInputs.length - 1) {
            addImeiInput();
            return;
        }

        allInputs[currentIndex + 1]?.focus();
    }

    // Optional: Handle barcode scanner input (simulates pressing Enter after scan)
    document.addEventListener('DOMContentLoaded', function() {
        const imeiInputsContainer = document.getElementById('imei-inputs');

        imeiInputsContainer.addEventListener('keydown', function(event) {
            // Check if the pressed key is Enter (key code 13)
            if (event.key === 'Enter') {
                event.preventDefault(); // Prevent form submission

                const currentInput = event.target;
                // Check if the current input is an IMEI field and has a value
                if (currentInput.name === 'imeis[]' && currentInput.value.trim() !== '') {
                    const normalizedImei = normalizeImei(currentInput.value);
                    currentInput.value = normalizedImei;

                    const duplicateCount = enteredImeis().filter(imei => imei === normalizedImei).length;
                    if (duplicateCount > 1) {
                        setImeiScanMessage(`IMEI ${normalizedImei} is already scanned.`, true);
                        currentInput.value = '';
                        currentInput.focus();
                        return;
                    }

                    setImeiScanMessage(`Added IMEI ${normalizedImei}.`);
                    focusNextImeiInput(currentInput);
                }
            }
        });

        imeiInputsContainer.addEventListener('blur', function(event) {
            if (event.target.name !== 'imeis[]') {
                return;
            }

            event.target.value = normalizeImei(event.target.value);
        }, true);
    });
</script>
@endsection
