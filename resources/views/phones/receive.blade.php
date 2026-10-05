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
        .imei-input-group {
            gap: 0.5rem;
        }
        .imei-input-group button {
            margin-left: 0;
            flex-shrink: 0;
        }
        #imei-camera-panel {
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            overflow: hidden;
            background: #111827;
        }
        #imei-camera-reader {
            width: 100%;
            min-height: 260px;
            background: #111827;
            position: relative;
        }
        #imei-camera-reader video {
            width: 100% !important;
            min-height: 320px;
            object-fit: cover;
        }
        .imei-camera-guide {
            position: absolute;
            left: 50%;
            top: 50%;
            width: min(88%, 520px);
            height: 118px;
            transform: translate(-50%, -50%);
            border: 3px solid rgba(34, 197, 94, 0.95);
            border-radius: 0.75rem;
            box-shadow: 0 0 0 9999px rgba(17, 24, 39, 0.28);
            pointer-events: none;
            z-index: 20;
        }
        .imei-camera-guide::before {
            content: "";
            position: absolute;
            left: 10%;
            right: 10%;
            top: 50%;
            border-top: 2px solid rgba(239, 68, 68, 0.95);
            transform: translateY(-50%);
        }
        @media (max-width: 639px) {
            .imei-input-group {
                align-items: stretch;
                flex-wrap: wrap;
            }
            .imei-input-group input {
                flex-basis: 100%;
            }
            .imei-input-group button {
                flex: 1 1 0;
            }
        }
    </style>
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/spare.png') }}"
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
        <div id="phone-receive-fields">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                {{-- Product --}}
                <div>
                    <label for="brand_id"
                           class="block text-gray-700 text-sm font-bold mb-2">
                        Product:
                    </label>

                    <div class="autocomplete-wrap">
                        <input type="hidden" name="brand_id" id="brand_id" value="{{ old('brand_id') }}">
                        <input type="text" id="brand_search" autocomplete="off" placeholder="Search product..."
                               class="autocomplete-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('brand_id') border-red-500 @enderror">
                        <div id="brand_search_suggestions" class="autocomplete-list hidden"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Type to find a product, then choose it from the list.</p>

                    @error('brand_id')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                    @enderror
                </div>


                {{-- Model --}}
                <div>

                    <label for="model"
                           class="block text-gray-700 text-sm font-bold mb-2">

                        Model:

                    </label>

                    <div class="autocomplete-wrap">

                        <input type="text"
                               name="model"
                               id="model"
                               value="{{ old('model') }}"
                               placeholder="Search existing model or type a new model"
                               autocomplete="off"
                               class="autocomplete-input shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('model') border-red-500 @enderror">

                        <div id="model_suggestions"
                             class="autocomplete-list hidden">
                        </div>

                    </div>

                    <p class="text-xs text-gray-500 mt-1">
                        Start typing to search, or enter a new model name.
                    </p>

                    @error('model')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Purchase Price --}}
                <div>

                    <label for="purchase_price"
                           class="block text-gray-700 text-sm font-bold mb-2">

                        Purchase Price:

                    </label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="purchase_price"
                           id="purchase_price"
                           value="{{ old('purchase_price') }}"
                           placeholder="e.g. 500.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('purchase_price') border-red-500 @enderror">

                    @error('purchase_price')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Selling Price --}}
                <div>

                    <label for="selling_price"
                           class="block text-gray-700 text-sm font-bold mb-2">

                        Selling Price:

                    </label>

                    <input type="number"
                           step="0.01"
                           min="0"
                           name="selling_price"
                           id="selling_price"
                           value="{{ old('selling_price') }}"
                           placeholder="e.g. 750.00"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('selling_price') border-red-500 @enderror">

                    @error('selling_price')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>


                {{-- Quantity --}}
                <div>

                    <label for="quantity"
                           class="block text-gray-700 text-sm font-bold mb-2">

                        Quantity:

                    </label>

                    <input type="number"
                           name="quantity"
                           id="quantity"
                           min="1"
                           step="1"
                           value="{{ old('quantity', 1) }}"
                           placeholder="e.g. 20"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('quantity') border-red-500 @enderror">

                    @error('quantity')
                    <p class="text-red-500 text-xs italic mt-1">
                        {{ $message }}
                    </p>
                    @enderror

                </div>

            </div>

        </div>


        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                Receive Inventory
            </button>
            <a href="{{ route('phones.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                View All Products
            </a>
        </div>
    </form>
</div>

    <script>
        const brandOptions = @json($brandOptions ?? []);

        const oldValues = {
            brandId: @json((string) old('brand_id')),
            model: @json(old('model')),
        };

        let activeSuggestionIndex = -1;
        let currentSuggestions = [];
        let currentSuggestionInput = null;


        /*
        |--------------------------------------------------------------------------
        | Helpers
        |--------------------------------------------------------------------------
        */

        function normalizeValue(value) {
            return String(value || '').trim();
        }


        function optionMatchesSearch(optionValue, searchValue) {
            return String(optionValue || '')
                .toLowerCase()
                .includes(String(searchValue || '').toLowerCase());
        }


        /*
        |--------------------------------------------------------------------------
        | Selected Product
        |--------------------------------------------------------------------------
        */

        function selectedBrandData() {

            const brandId = document.getElementById('brand_id').value;

            return brandOptions[brandId] || null;
        }


        /*
        |--------------------------------------------------------------------------
        | Model Options
        |--------------------------------------------------------------------------
        */

        function modelOptions() {

            const brandData = selectedBrandData();

            if (!brandData || !brandData.models) {
                return [];
            }

            return Object.keys(brandData.models);
        }


        /*
        |--------------------------------------------------------------------------
        | Filter Models
        |--------------------------------------------------------------------------
        */

        function filteredOptions(options, query) {

            const normalizedQuery = normalizeValue(query);

            return [...new Set(options)]
                .filter(value => optionMatchesSearch(value, normalizedQuery))
                .slice(0, 25);
        }


        /*
        |--------------------------------------------------------------------------
        | Suggestions
        |--------------------------------------------------------------------------
        */

        function suggestionsElement(input) {

            return document.getElementById(
                `${input.id}_suggestions`
            );
        }


        function closeSuggestions() {

            document
                .querySelectorAll('.autocomplete-list')
                .forEach(list => {

                    list.classList.add('hidden');
                    list.innerHTML = '';

                });

            activeSuggestionIndex = -1;
            currentSuggestions = [];
            currentSuggestionInput = null;
        }


        function markActiveSuggestion() {

            const list = currentSuggestionInput
                ? suggestionsElement(currentSuggestionInput)
                : null;

            if (!list) {
                return;
            }

            Array.from(
                list.querySelectorAll('.autocomplete-option')
            ).forEach((option, index) => {

                option.classList.toggle(
                    'active',
                    index === activeSuggestionIndex
                );

            });
        }


        /*
        |--------------------------------------------------------------------------
        | Choose Model
        |--------------------------------------------------------------------------
        */

        function chooseSuggestion(input, value) {

            input.value = value;

            closeSuggestions();

            input.focus();
        }

        function productOptions() {
            return Object.entries(brandOptions).map(([id, data]) => ({
                id,
                name: data.name || data.label || String(id)
            }));
        }

        function chooseProduct(product) {
            document.getElementById('brand_id').value = product.id;
            document.getElementById('brand_search').value = product.name;
            document.getElementById('model').value = '';
            closeSuggestions();
            document.getElementById('model').focus();
        }

        function openProductSuggestions() {
            const input = document.getElementById('brand_search');
            const list = document.getElementById('brand_search_suggestions');
            const query = normalizeValue(input.value).toLowerCase();
            const matches = productOptions().filter(product => product.name.toLowerCase().includes(query)).slice(0, 25);
            closeSuggestions();
            matches.forEach(product => {
                const item = document.createElement('div');
                item.className = 'autocomplete-option';
                item.textContent = product.name;
                item.addEventListener('mousedown', event => { event.preventDefault(); chooseProduct(product); });
                list.appendChild(item);
            });
            if (!matches.length) {
                const item = document.createElement('div');
                item.className = 'autocomplete-option autocomplete-empty';
                item.textContent = 'No matching products found.';
                list.appendChild(item);
            }
            list.classList.remove('hidden');
        }


        /*
        |--------------------------------------------------------------------------
        | Open Suggestions
        |--------------------------------------------------------------------------
        */

        function openSuggestions(input, options) {

            const list = suggestionsElement(input);

            if (!list) {
                return;
            }

            const typedValue = normalizeValue(input.value);

            const matches = filteredOptions(
                options,
                typedValue
            );

            const exactMatch = matches.some(
                value =>
                    value.toLowerCase() ===
                    typedValue.toLowerCase()
            );


            closeSuggestions();

            currentSuggestionInput = input;
            currentSuggestions = [];


            /*
            |--------------------------------------------------------------------------
            | Existing Models
            |--------------------------------------------------------------------------
            */

            matches.forEach(value => {

                const item = document.createElement('div');

                item.className = 'autocomplete-option';

                item.textContent = value;

                item.addEventListener(
                    'mousedown',
                    function(event) {

                        event.preventDefault();

                        chooseSuggestion(
                            input,
                            value
                        );

                    }
                );

                list.appendChild(item);

                currentSuggestions.push(value);

            });


            /*
            |--------------------------------------------------------------------------
            | New Model
            |--------------------------------------------------------------------------
            */

            if (typedValue && !exactMatch) {

                const item = document.createElement('div');

                item.className = 'autocomplete-option';

                item.innerHTML =
                    `Use new model: <strong>${typedValue}</strong>`;

                item.addEventListener(
                    'mousedown',
                    function(event) {

                        event.preventDefault();

                        chooseSuggestion(
                            input,
                            typedValue
                        );

                    }
                );

                list.appendChild(item);

                currentSuggestions.push(
                    typedValue
                );
            }


            /*
            |--------------------------------------------------------------------------
            | No Results
            |--------------------------------------------------------------------------
            */

            if (!list.children.length) {

                const item = document.createElement('div');

                item.className =
                    'autocomplete-option autocomplete-empty';

                item.textContent =
                    'No saved models. Type a new model name.';

                list.appendChild(item);
            }


            list.classList.remove('hidden');
        }


        /*
        |--------------------------------------------------------------------------
        | Model Autocomplete
        |--------------------------------------------------------------------------
        */

        function bindModelAutocomplete() {

            const input =
                document.getElementById('model');

            if (!input) {
                return;
            }


            input.addEventListener(
                'focus',
                function() {

                    openSuggestions(
                        input,
                        modelOptions()
                    );

                }
            );


            input.addEventListener(
                'input',
                function() {

                    openSuggestions(
                        input,
                        modelOptions()
                    );

                }
            );


            input.addEventListener(
                'keydown',
                function(event) {

                    const list =
                        suggestionsElement(input);

                    if (!list ||
                        list.classList.contains('hidden')) {
                        return;
                    }


                    if (event.key === 'ArrowDown') {

                        event.preventDefault();

                        activeSuggestionIndex =
                            Math.min(
                                activeSuggestionIndex + 1,
                                currentSuggestions.length - 1
                            );

                        markActiveSuggestion();
                    }


                    if (event.key === 'ArrowUp') {

                        event.preventDefault();

                        activeSuggestionIndex =
                            Math.max(
                                activeSuggestionIndex - 1,
                                0
                            );

                        markActiveSuggestion();
                    }


                    if (
                        event.key === 'Enter' &&
                        activeSuggestionIndex >= 0
                    ) {

                        event.preventDefault();

                        chooseSuggestion(
                            input,
                            currentSuggestions[
                                activeSuggestionIndex
                                ]
                        );
                    }


                    if (event.key === 'Escape') {

                        closeSuggestions();

                    }

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Product Change
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const brandSelect =
                    document.getElementById('brand_id');

                const brandSearch =
                    document.getElementById('brand_search');

                const modelInput =
                    document.getElementById('model');


                if (!brandSelect || !brandSearch || !modelInput) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Existing Product Selected
                |--------------------------------------------------------------------------
                */

                brandSearch.addEventListener('focus', openProductSuggestions);
                brandSearch.addEventListener('input', function () {
                    brandSelect.value = '';
                    modelInput.value = '';
                    openProductSuggestions();
                });


                bindModelAutocomplete();


                /*
                |--------------------------------------------------------------------------
                | Close Suggestions
                |--------------------------------------------------------------------------
                */

                document.addEventListener(
                    'mousedown',
                    function(event) {

                        if (
                            !event.target.closest(
                                '.autocomplete-wrap'
                            )
                        ) {

                            closeSuggestions();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Restore Old Model After Validation Error
                |--------------------------------------------------------------------------
                */

                if (oldValues.brandId) {

                    brandSelect.value =
                        oldValues.brandId;

                    const selectedProduct = productOptions().find(product => String(product.id) === String(oldValues.brandId));
                    if (selectedProduct) brandSearch.value = selectedProduct.name;

                }

                if (oldValues.model) {

                    modelInput.value =
                        oldValues.model;

                }

            }
        );
    </script>
@endsection
