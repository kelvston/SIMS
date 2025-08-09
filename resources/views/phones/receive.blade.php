@extends('layouts.app')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');
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
    </style>

    <div class="container mx-auto bg-white p-8 rounded-xl shadow-2xl mt-10 relative max-w-6xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Receive Inventory</h1>
        <p class="text-center text-gray-500 mb-8">Fill in the shared details and then scan each phone's IMEI to add to the list.</p>

        <div id="toast-container" class="fixed top-4 right-4 z-50"></div>
        <div id="confirmation-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-sm w-full relative text-center">
                    <p id="confirmation-message" class="mb-4 text-gray-800"></p>
                    <div class="flex justify-center space-x-4">
                        <button id="cancel-confirm-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>
                        <button id="confirm-action-btn" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
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

        <form action="{{ route('phones.receive.store') }}" method="POST" id="main-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Phones</h2>
                    <p class="text-sm text-gray-500 mb-6">Enter shared details once, then scan each phone's IMEI to add to the list.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Brand:</label>
                            <input
                                type="text"
                                id="phone-brand-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Brand"
                            >
                            <input type="hidden" id="phone-brand">
                            <ul id="brand-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Model:</label>
                            <input
                                type="text"
                                id="phone-model-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Model"
                            >
                            <input type="hidden" id="phone-model">
                            <ul id="model-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Color:</label>
                            <input
                                type="text"
                                id="phone-color-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Color"
                            >
                            <input type="hidden" id="phone-color">
                            <ul id="color-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Storage Capacity:</label>
                            <input
                                type="text"
                                id="phone-storage-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Storage"
                            >
                            <input type="hidden" id="phone-storage">
                            <ul id="storage-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Price:</label>
                            <input type="number" step="0.01" id="phone-purchase-price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., 500.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selling Price:</label>
                            <input type="number" step="0.01" id="phone-selling-price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., 750.00">
                        </div>
                    </div>

                    <hr class="my-6 border-gray-300">

                    <div class="mb-4">
                        <label for="imei-input" class="block text-sm font-bold text-gray-700 mb-2">Scan Barcode or Enter IMEI:</label>
                        <div class="flex gap-2">
                            <input type="text" id="imei-input" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Scan or type IMEI...">
                            <button type="button" id="add-imei-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Add</button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">IMEIs to be received:</label>
                        <ul id="imei-list" class="bg-gray-100 p-4 rounded-lg shadow-inner max-h-60 overflow-y-auto">
                            <li id="no-imei-placeholder" class="text-gray-500 italic">No IMEIs added yet.</li>
                        </ul>
                    </div>
                </div>

                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-700">Accessories</h2>
                        <button type="button" id="add-accessory-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                            + Add Accessory
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Add an accessory and its quantity. The details will be added below as a summary.</p>
                    <div id="accessory-list" class="space-y-4">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Receive Inventory
                </button>
                <a href="{{ route('phones.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    View All Phones
                </a>
            </div>

            <div id="phone-hidden-inputs"></div>
        </form>
    </div>

    <div id="accessory-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">
                <h3 class="text-xl font-bold mb-4">Add/Edit Accessory</h3>
                <form id="accessory-form">
                    <input type="hidden" id="accessory-index" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Accessory Name</label>
                            <input
                                type="text"
                                id="modal-name"
                                class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                placeholder="Search or enter a new Accessory"
                            >
                            <input type="hidden" id="selected-accessory-id">
                            <ul id="accessory-name-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <input
                                type="text"
                                id="accessory-category-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Category"
                            >
                            <input type="hidden" id="accessory-category-id">
                            <ul id="category-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input type="text" id="modal-barcode" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Scan or type barcode">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit</label>
                            <select id="modal-unit" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">
                                <option value="piece">Piece</option>
                                <option value="pack">Pack</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Purchase Price</label>
                            <input type="number" step="0.01" id="modal-purchase-price" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="P. Price">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                            <input type="number" step="0.01" id="modal-selling-price" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S. Price">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="modal-quantity" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Qty">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" id="close-accessory-modal-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Shared Data and Functions ---
            const brands = @json($brands);
            const models = @json($models);
            const colors = @json($colors);
            const accessoryCategories = @json($accessory_categories);
            const accessories = @json($accessories);
            const storageCapacities = [
                { id: 1, name: '16GB' },
                { id: 2, name: '32GB' },
                { id: 3, name: '64GB' },
                { id: 4, name: '128GB' },
                { id: 5, name: '256GB' },
                { id: 6, name: '512GB' },
                { id: 7, name: '1TB' },
                { id: 8, name: '2TB' }
            ];
            let accessoriesData = [];
            let accessoryCounter = 0;

            // --- DOM Elements ---
            const brandInput = document.getElementById('phone-brand-input');
            const brandHiddenInput = document.getElementById('phone-brand');
            const brandDropdown = document.getElementById('brand-dropdown');
            const modelInput = document.getElementById('phone-model-input');
            const modelHiddenInput = document.getElementById('phone-model');
            const modelDropdown = document.getElementById('model-dropdown');
            const colorInput = document.getElementById('phone-color-input');
            const colorHiddenInput = document.getElementById('phone-color');
            const colorDropdown = document.getElementById('color-dropdown');
            const storageInput = document.getElementById('phone-storage-input');
            const storageHiddenInput = document.getElementById('phone-storage');
            const storageDropdown = document.getElementById('storage-dropdown');

            // Add a condition input
            const conditionInput = document.getElementById('condition-input'); // Assuming you add this input to your HTML
            const imeiInput = document.getElementById('imei-input');
            const addImeiBtn = document.getElementById('add-imei-btn');
            const imeiList = document.getElementById('imei-list');
            const noImeiPlaceholder = document.getElementById('no-imei-placeholder');
            const phoneHiddenInputs = document.getElementById('phone-hidden-inputs');

            const accessoryModal = document.getElementById('accessory-modal');
            const addAccessoryBtn = document.getElementById('add-accessory-btn');
            const closeAccessoryModalBtn = document.getElementById('close-accessory-modal-btn');
            const accessoryForm = document.getElementById('accessory-form');
            const accessoryList = document.getElementById('accessory-list');
            const accessoryNameInput = document.getElementById('modal-name');
            const accessoryNameDropdown = document.getElementById('accessory-name-dropdown');
            const selectedAccessoryIdInput = document.getElementById('selected-accessory-id');
            const accessoryCategoryInput = document.getElementById('accessory-category-input');
            const accessoryCategoryIdHidden = document.getElementById('accessory-category-id');
            const accessoryCategoryDropdown = document.getElementById('category-dropdown');
            const mainForm = document.getElementById('main-form');
            const phonePurchasePriceInput = document.getElementById('phone-purchase-price');
            const phoneSellingPriceInput = document.getElementById('phone-selling-price');
            const submitButton = mainForm.querySelector('button[type="submit"]');

            let phonesData = [];

            function updateSubmitButtonState() {
                if (phonesData.length > 0 || accessoriesData.length > 0) {
                    submitButton.disabled = false;
                    submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    submitButton.disabled = true;
                    submitButton.classList.add('opacity-50', 'cursor-not-allowed');
                }
            }

            function showToast(message, type = 'success') {
                const toastContainer = document.getElementById('toast-container');
                const toast = document.createElement('div');
                toast.className = `p-4 rounded-lg shadow-lg mb-4 text-white flex items-center space-x-2 animate-fade-in-down`;
                if (type === 'success') {
                    toast.classList.add('bg-green-500');
                    toast.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <span>${message}</span>`;
                } else {
                    toast.classList.add('bg-red-500');
                    toast.innerHTML = `<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> <span>${message}</span>`;
                }
                toastContainer.appendChild(toast);
                setTimeout(() => toast.remove(), 5000);
            }

            const confirmationModal = document.getElementById('confirmation-modal');
            const confirmationMessage = document.getElementById('confirmation-message');
            const cancelConfirmBtn = document.getElementById('cancel-confirm-btn');
            const confirmActionBtn = document.getElementById('confirm-action-btn');
            let confirmationPromiseResolver;

            function showConfirmation(message) {
                return new Promise((resolve) => {
                    confirmationMessage.textContent = message;
                    confirmationModal.classList.remove('hidden');
                    confirmationPromiseResolver = resolve;
                });
            }

            cancelConfirmBtn.addEventListener('click', () => {
                confirmationModal.classList.add('hidden');
                confirmationPromiseResolver(false);
            });

            confirmActionBtn.addEventListener('click', () => {
                confirmationModal.classList.add('hidden');
                confirmationPromiseResolver(true);
            });

            // --- IMEIs Section (Main Logic) ---

            function addImei() {
                const imei = imeiInput.value.trim();
                if (!imei) {
                    showToast('IMEI cannot be empty.', 'error');
                    return;
                }

                const brand = brandInput.value.trim();
                const model = modelInput.value.trim();
                const color = colorInput.value.trim();
                const storage = storageInput.value.trim();
                const purchasePrice = phonePurchasePriceInput.value;
                const sellingPrice = phoneSellingPriceInput.value;
                // const condition = conditionInput.value; // Get the condition from the new input

                // Validation for hidden IDs is crucial here
                const brandId = brandHiddenInput.value;
                const modelId = modelHiddenInput.value;
                const colorId = colorHiddenInput.value;
                const storageId = storageHiddenInput.value;

                if (!brandId || !modelId || !colorId || !storageId || !purchasePrice || !sellingPrice) {
                    showToast('Please fill in all phone details (Brand, Model, Color, Storage, and Prices) from the dropdowns before adding an IMEI.', 'error');
                    return;
                }

                if (phonesData.some(phone => phone.imei === imei)) {
                    showToast('IMEI already exists in the list.', 'error');
                    imeiInput.value = '';
                    return;
                }

                phonesData.push({
                    imei,
                    brand_id: brandId,
                    brand_name: brand,
                    model_id: modelId,
                    model_name: model,
                    color_id: colorId,
                    color_name: color,
                    storage_id: storageId,
                    storage_name: storage,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice,
                    // You can hardcode a condition for now or add a dropdown to your HTML
                    condition: 'New'
                });

                renderImeiList();
                imeiInput.value = '';
                imeiInput.focus();
                showToast('IMEI added successfully!', 'success');

                updateSubmitButtonState();
            }

            let imeiTypingTimer;
            const imeiDoneTypingInterval = 200;

            imeiInput.addEventListener('input', () => {
                clearTimeout(imeiTypingTimer);
                imeiTypingTimer = setTimeout(() => {
                    if (imeiInput.value.trim().length >= 15) {
                        addImei();
                    }
                }, imeiDoneTypingInterval);
            });

            addImeiBtn.addEventListener('click', addImei);

            imeiInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    addImei();
                }
            });

            function renderImeiList() {
                if (noImeiPlaceholder) noImeiPlaceholder.classList.add('hidden');
                imeiList.innerHTML = '';

                // Remove existing hidden inputs to avoid duplicates
                document.querySelectorAll('input[name^="phones"]').forEach(el => el.remove());

                phonesData.forEach((phone, index) => {
                    const listItem = document.createElement('li');
                    listItem.className = 'flex justify-between items-center bg-white p-3 rounded-md shadow-sm mb-2 text-gray-700';
                    listItem.innerHTML = `
                <span><strong>IMEI:</strong> ${phone.imei} - ${phone.brand_name} ${phone.model_name}</span>
                <button type="button" class="remove-imei-btn text-red-500 hover:text-red-700 font-bold" data-index="${index}">&times;</button>
            `;
                    imeiList.appendChild(listItem);

                    // Create a hidden input for each phone's details
                    const inputModel = document.createElement('input');
                    inputModel.type = 'hidden';
                    inputModel.name = `phones[${index}][model]`;
                    inputModel.value = phone.model_name; // Use model_name for the 'model' field
                    phoneHiddenInputs.appendChild(inputModel);

                    const inputColor = document.createElement('input');
                    inputColor.type = 'hidden';
                    inputColor.name = `phones[${index}][color]`;
                    inputColor.value = phone.color_name; // Use color_name for the 'color' field
                    phoneHiddenInputs.appendChild(inputColor);

                    const inputStorage = document.createElement('input');
                    inputStorage.type = 'hidden';
                    inputStorage.name = `phones[${index}][storage_capacity]`;
                    inputStorage.value = phone.storage_name; // Use storage_name for the 'storage_capacity' field
                    phoneHiddenInputs.appendChild(inputStorage);

                    // Now, handle the nested 'imeis' array
                    const inputImei = document.createElement('input');
                    inputImei.type = 'hidden';
                    inputImei.name = `phones[${index}][imeis][0][imei]`;
                    inputImei.value = phone.imei;
                    phoneHiddenInputs.appendChild(inputImei);

                    const inputCondition = document.createElement('input');
                    inputCondition.type = 'hidden';
                    inputCondition.name = `phones[${index}][imeis][0][condition]`;
                    inputCondition.value = phone.condition;
                    phoneHiddenInputs.appendChild(inputCondition);

                    // Also, don't forget the other fields that don't need to be nested
                    const inputBrandId = document.createElement('input');
                    inputBrandId.type = 'hidden';
                    inputBrandId.name = `phones[${index}][brand_id]`;
                    inputBrandId.value = phone.brand_id;
                    phoneHiddenInputs.appendChild(inputBrandId);

                    const inputPurchasePrice = document.createElement('input');
                    inputPurchasePrice.type = 'hidden';
                    inputPurchasePrice.name = `phones[${index}][purchase_price]`;
                    inputPurchasePrice.value = phone.purchase_price;
                    phoneHiddenInputs.appendChild(inputPurchasePrice);

                    const inputSellingPrice = document.createElement('input');
                    inputSellingPrice.type = 'hidden';
                    inputSellingPrice.name = `phones[${index}][selling_price]`;
                    inputSellingPrice.value = phone.selling_price;
                    phoneHiddenInputs.appendChild(inputSellingPrice);
                });

                if (phonesData.length === 0) {
                    if (noImeiPlaceholder) noImeiPlaceholder.classList.remove('hidden');
                }
            }

            imeiList.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-imei-btn')) {
                    const index = e.target.dataset.index;
                    phonesData.splice(index, 1);
                    renderImeiList();
                    updateSubmitButtonState();
                }
            });

            imeiInput.addEventListener('focus', () => {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            });

            imeiInput.addEventListener('blur', () => {
                updateSubmitButtonState();
            });

            // --- Other Sections (dropdowns, accessories, etc.) ---
            function renderDropdown(dropdownElement, data, type, inputElement, hiddenInputElement, isIdRequired = false) {
                dropdownElement.innerHTML = '';
                if (data.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-3 py-2 text-gray-500';
                    noResults.textContent = `No ${type}s found.`;
                    dropdownElement.appendChild(noResults);
                } else {
                    data.forEach(item => {
                        const listItem = document.createElement('li');
                        listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';
                        listItem.textContent = item.name;
                        listItem.dataset.value = item.name;
                        listItem.dataset.id = item.id;

                        listItem.addEventListener('click', () => {
                            inputElement.value = item.name;
                            if (isIdRequired) {
                                hiddenInputElement.value = item.id;
                            } else {
                                hiddenInputElement.value = item.name;
                            }
                            dropdownElement.classList.add('hidden');
                        });
                        dropdownElement.appendChild(listItem);
                    });
                }
            }

            brandInput.addEventListener('input', () => {
                const filter = brandInput.value.toLowerCase();
                const filteredBrands = brands.filter(b => b.name.toLowerCase().includes(filter));
                renderDropdown(brandDropdown, filteredBrands, 'brand', brandInput, brandHiddenInput, true);
                brandDropdown.classList.remove('hidden');
            });

            brandInput.addEventListener('focus', () => {
                const filter = brandInput.value.toLowerCase();
                const filteredBrands = brands.filter(b => b.name.toLowerCase().includes(filter));
                renderDropdown(brandDropdown, filteredBrands, 'brand', brandInput, brandHiddenInput, true);
                brandDropdown.classList.remove('hidden');
            });

            modelInput.addEventListener('input', () => {
                const filter = modelInput.value.toLowerCase();
                const filteredModels = models.filter(m => m.name.toLowerCase().includes(filter));
                renderDropdown(modelDropdown, filteredModels, 'model', modelInput, modelHiddenInput, true);
                modelDropdown.classList.remove('hidden');
            });

            modelInput.addEventListener('focus', () => {
                const filter = modelInput.value.toLowerCase();
                const filteredModels = models.filter(m => m.name.toLowerCase().includes(filter));
                renderDropdown(modelDropdown, filteredModels, 'model', modelInput, modelHiddenInput, true);
                modelDropdown.classList.remove('hidden');
            });

            colorInput.addEventListener('input', () => {
                const filter = colorInput.value.toLowerCase();
                const filteredColors = colors.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(colorDropdown, filteredColors, 'color', colorInput, colorHiddenInput, true);
                colorDropdown.classList.remove('hidden');
            });

            colorInput.addEventListener('focus', () => {
                const filter = colorInput.value.toLowerCase();
                const filteredColors = colors.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(colorDropdown, filteredColors, 'color', colorInput, colorHiddenInput, true);
                colorDropdown.classList.remove('hidden');
            });

            storageInput.addEventListener('input', () => {
                const filter = storageInput.value.toLowerCase();
                const filteredStorage = storageCapacities.filter(s => s.name.toLowerCase().includes(filter));
                renderDropdown(storageDropdown, filteredStorage, 'storage', storageInput, storageHiddenInput, true);
                storageDropdown.classList.remove('hidden');
            });

            storageInput.addEventListener('focus', () => {
                const filter = storageInput.value.toLowerCase();
                const filteredStorage = storageCapacities.filter(s => s.name.toLowerCase().includes(filter));
                renderDropdown(storageDropdown, filteredStorage, 'storage', storageInput, storageHiddenInput, true);
                storageDropdown.classList.remove('hidden');
            });

            accessoryCategoryInput.addEventListener('input', () => {
                const filter = accessoryCategoryInput.value.toLowerCase();
                const filteredCategories = accessoryCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(accessoryCategoryDropdown, filteredCategories, 'category', accessoryCategoryInput, accessoryCategoryIdHidden, true);
                accessoryCategoryDropdown.classList.remove('hidden');
            });
            accessoryCategoryInput.addEventListener('focus', () => {
                const filter = accessoryCategoryInput.value.toLowerCase();
                const filteredCategories = accessoryCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(accessoryCategoryDropdown, filteredCategories, 'category', accessoryCategoryInput, accessoryCategoryIdHidden, true);
                accessoryCategoryDropdown.classList.remove('hidden');
            });

            document.addEventListener('click', (event) => {
                if (!brandInput.parentElement.contains(event.target)) {
                    brandDropdown.classList.add('hidden');
                }
                if (!modelInput.parentElement.contains(event.target)) {
                    modelDropdown.classList.add('hidden');
                }
                if (!colorInput.parentElement.contains(event.target)) {
                    colorDropdown.classList.add('hidden');
                }
                if (!storageInput.parentElement.contains(event.target)) {
                    storageDropdown.classList.add('hidden');
                }
                if (!accessoryCategoryInput.parentElement.contains(event.target)) {
                    accessoryCategoryDropdown.classList.add('hidden');
                }
                if (!accessoryNameInput.parentElement.contains(event.target)) {
                    accessoryNameDropdown.classList.add('hidden');
                }
            });

            addAccessoryBtn.addEventListener('click', () => {
                accessoryForm.reset();
                selectedAccessoryIdInput.value = '';
                document.getElementById('accessory-index').value = '';
                accessoryModal.classList.remove('hidden');
            });

            closeAccessoryModalBtn.addEventListener('click', () => accessoryModal.classList.add('hidden'));

            accessoryNameInput.addEventListener('input', () => {
                const filter = accessoryNameInput.value.toLowerCase();
                const filteredAccessories = accessories.filter(acc => acc.name.toLowerCase().includes(filter));
                renderAccessoryNameDropdown(filteredAccessories, accessoryNameDropdown);
                accessoryNameDropdown.classList.remove('hidden');
            });

            accessoryNameInput.addEventListener('focus', () => {
                const filter = accessoryNameInput.value.toLowerCase();
                const filteredAccessories = accessories.filter(acc => acc.name.toLowerCase().includes(filter));
                renderAccessoryNameDropdown(filteredAccessories, accessoryNameDropdown);
                accessoryNameDropdown.classList.remove('hidden');
            });

            function renderAccessoryNameDropdown(data, dropdownElement) {
                dropdownElement.innerHTML = '';
                if (data.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-3 py-2 text-gray-500';
                    noResults.textContent = 'No existing accessories found. Create a new one.';
                    dropdownElement.appendChild(noResults);
                } else {
                    data.forEach(item => {
                        const listItem = document.createElement('li');
                        listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';
                        listItem.textContent = `${item.name} ($${parseFloat(item.selling_price).toFixed(2)})`;
                        listItem.addEventListener('click', () => {
                            accessoryNameInput.value = item.name;
                            selectedAccessoryIdInput.value = item.id;
                            document.getElementById('modal-purchase-price').value = item.purchase_price;
                            document.getElementById('modal-selling-price').value = item.selling_price;
                            document.getElementById('modal-unit').value = item.unit;
                            if (item.category_id) {
                                const category = accessoryCategories.find(c => c.id === item.category_id);
                                if (category) {
                                    accessoryCategoryInput.value = category.name;
                                    accessoryCategoryIdHidden.value = category.id;
                                }
                            }
                            dropdownElement.classList.add('hidden');
                        });
                        dropdownElement.appendChild(listItem);
                    });
                }
            }

            accessoryForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const index = document.getElementById('accessory-index').value;
                const name = document.getElementById('modal-name').value;
                const existingAccessoryId = selectedAccessoryIdInput.value;
                const categoryId = document.getElementById('accessory-category-id').value;
                const categoryName = document.getElementById('accessory-category-input').value;
                const barcode = document.getElementById('modal-barcode').value;
                const unit = document.getElementById('modal-unit').value;
                const purchasePrice = document.getElementById('modal-purchase-price').value;
                const sellingPrice = document.getElementById('modal-selling-price').value;
                const quantity = document.getElementById('modal-quantity').value;

                if (!name || !quantity) {
                    showToast('Name and Quantity are required.', 'error');
                    return;
                }

                const accessory = {
                    id: existingAccessoryId || (index ? accessoriesData[index].id : accessoryCounter++),
                    name,
                    category_id: categoryId,
                    category_name: categoryName,
                    barcode,
                    unit,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice,
                    quantity
                };

                if (index) {
                    accessoriesData[index] = accessory;
                } else {
                    accessoriesData.push(accessory);
                }
                renderAccessoryList();
                accessoryModal.classList.add('hidden');
            });

            function renderAccessoryList() {
                accessoryList.innerHTML = '';

                document.querySelectorAll('input[name^="accessories"]').forEach(el => el.remove());

                accessoriesData.forEach((accessory, index) => {
                    const card = document.createElement('div');
                    card.className = 'bg-gray-100 p-4 rounded-md shadow-sm mb-2';
                    card.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold">${accessory.name}</h4>
                        <p class="text-sm text-gray-600">Category: ${accessory.category_name || 'N/A'}</p>
                        ${accessory.barcode ? `<p class="text-xs text-gray-400">Barcode: ${accessory.barcode}</p>` : ''}
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">${accessory.quantity} pcs</p>
                        <p class="text-sm text-gray-500">P.Price: $${parseFloat(accessory.purchase_price || 0).toFixed(2)}</p>
                    </div>
                </div>
                <div class="flex justify-end mt-2 space-x-2">
                    <button type="button" class="edit-accessory-btn text-blue-500 hover:text-blue-700 font-bold text-sm" data-index="${index}">Edit</button>
                    <button type="button" class="remove-accessory-btn text-red-500 hover:text-red-700 font-bold text-sm" data-index="${index}">Remove</button>
                </div>
            `;
                    accessoryList.appendChild(card);

                    for (const key in accessory) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `accessories[${index}][${key}]`;
                        input.value = accessory[key];
                        phoneHiddenInputs.appendChild(input);
                    }
                });
                updateSubmitButtonState();
            }

            accessoryList.addEventListener('click', (e) => {
                if (e.target.classList.contains('edit-accessory-btn')) {
                    const index = e.target.dataset.index;
                    const accessory = accessoriesData[index];
                    document.getElementById('accessory-index').value = index;
                    document.getElementById('modal-name').value = accessory.name;
                    document.getElementById('selected-accessory-id').value = accessory.id;
                    document.getElementById('accessory-category-input').value = accessory.category_name;
                    document.getElementById('accessory-category-id').value = accessory.category_id;
                    document.getElementById('modal-barcode').value = accessory.barcode;
                    document.getElementById('modal-unit').value = accessory.unit;
                    document.getElementById('modal-purchase-price').value = accessory.purchase_price;
                    document.getElementById('modal-selling-price').value = accessory.selling_price;
                    document.getElementById('modal-quantity').value = accessory.quantity;
                    accessoryModal.classList.remove('hidden');
                } else if (e.target.classList.contains('remove-accessory-btn')) {
                    const index = e.target.dataset.index;
                    showConfirmation('Are you sure you want to remove this accessory?').then(result => {
                        if (result) {
                            accessoriesData.splice(index, 1);
                            renderAccessoryList();
                            showToast('Accessory removed.', 'success');
                        }
                    });
                }
            });

            // --- Initial Renders and State ---
            renderImeiList();
            renderAccessoryList();
            updateSubmitButtonState();

            mainForm.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target !== imeiInput) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
