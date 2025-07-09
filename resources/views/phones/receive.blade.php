@extends('layouts.app')

@section('content')
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
</head>
<body class="bg-gray-100 p-6">
<div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10">
    <img src="{{ asset('images/watermark.png') }}"
         alt="Watermark"
         class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
         style="transform: translate(-60%, -50%);" />
    <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Receive New Phones</h1>

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
                <input type="text" name="model" id="model" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('model') border-red-500 @enderror" value="{{ old('model') }}" placeholder="e.g., iPhone 15 Pro Max">
                @error('model')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="color" class="block text-gray-700 text-sm font-bold mb-2">Color:</label>
                <input type="text" name="color" id="color" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('color') border-red-500 @enderror" value="{{ old('color') }}" placeholder="e.g., Black, Blue, Silver">
                @error('color')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="storage_capacity" class="block text-gray-700 text-sm font-bold mb-2">Storage Capacity:</label>
                <input type="text" name="storage_capacity" id="storage_capacity" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('storage_capacity') border-red-500 @enderror" value="{{ old('storage_capacity') }}" placeholder="e.g., 128GB, 256GB">
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
            <div id="imei-inputs">
                <!-- Initial IMEI input field -->
                <div class="imei-input-group">
                    <input type="text" name="imeis[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter IMEI or scan barcode" autofocus>
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

        <div class="flex items-center justify-between">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                Receive Phones
            </button>
            <a href="{{ route('phones.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                View All Phones
            </a>
        </div>
    </form>
</div>

<script>
    // Function to add a new IMEI input field
    function addImeiInput() {
        const container = document.getElementById('imei-inputs');
        const div = document.createElement('div');
        div.className = 'imei-input-group';
        div.innerHTML = `
                <input type="text" name="imeis[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter IMEI or scan barcode">
                <button type="button" onclick="removeImeiInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>
            `;
        container.appendChild(div);
        div.querySelector('input').focus(); // Focus on the newly added input
    }

    // Function to remove an IMEI input field
    function removeImeiInput(button) {
        button.closest('.imei-input-group').remove();
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
                    // If it's the last input, add a new one
                    const allInputs = imeiInputsContainer.querySelectorAll('input[name="imeis[]"]');
                    if (currentInput === allInputs[allInputs.length - 1]) {
                        addImeiInput();
                    } else {
                        // If not the last, try to focus on the next empty one or the next one
                        let focusedNext = false;
                        for (let i = 0; i < allInputs.length; i++) {
                            if (allInputs[i].value.trim() === '' && allInputs[i] !== currentInput) {
                                allInputs[i].focus();
                                focusedNext = true;
                                break;
                            }
                        }
                        if (!focusedNext) { // If no empty input found, focus on the next sibling
                            const nextInputGroup = currentInput.closest('.imei-input-group').nextElementSibling;
                            if (nextInputGroup && nextInputGroup.querySelector('input[name="imeis[]"]')) {
                                nextInputGroup.querySelector('input[name="imeis[]"]').focus();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
