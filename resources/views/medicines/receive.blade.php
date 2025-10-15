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
        <p class="text-center text-gray-500 mb-8">Fill in the shared details and then scan each medicine's to add to the list.</p>

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

        <form action="{{ route('medicines.receive.store') }}" method="POST" id="main-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-700">Medicines</h2>
                        <button type="button" id="add-medicine-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                            + Add Medicine
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Add an Medicine and its quantity. The details will be added below as a summary.</p>
                    <div id="medicine-list" class="space-y-4">
                    </div>
                </div>

                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-700">Cosmetics</h2>
                        <button type="button" id="add-cosmetic-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                            + Add Cosmetic
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Add an cosmetic and its quantity. The details will be added below as a summary.</p>
                    <div id="cosmetic-list" class="space-y-4">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Receive Inventory
                </button>
                <a href="{{ route('medicines.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    View All Medicines
                </a>
            </div>

{{--            <div id="medicine-hidden-inputs"></div>--}}
        </form>
    </div>

    <div id="cosmetic-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">
                <h3 class="text-xl font-bold mb-4">Add/Edit Cosmetic</h3>
                <form id="cosmetic-form">
                    <input type="hidden" id="cosmetic-index" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Cosmetic Name</label>
                            <input
                                type="text"
                                id="modal-name"
                                class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                placeholder="Search or enter a new Cosmetic"
                            >
                            <input type="hidden" id="selected-cosmetic-id">
                            <ul id="cosmetic-name-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <input
                                type="text"
                                id="cosmetic-category-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Category"
                            >
                            <input type="hidden" id="cosmetic-category-id">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" step="0.01" id="modal-description" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S. OS">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock Origin</label>
                            <input type="text" step="0.01" id="modal-stock_origin" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S.ORIGIN">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="modal-quantity" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Qty">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" id="close-cosmetic-modal-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="medicine-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">
                <h3 class="text-xl font-bold mb-4">Add/Edit Medicine</h3>
                <form id="medicine-form">
{{--                    <input type="hidden" id="medicine-index" value="">--}}
                    <input type="hidden" id="medicine-index" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Medicine Name</label>
                            <input
                                type="text"
                                id="modal-medicine_name"
                                class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                placeholder="Search or enter a new Medicine"
                            >
                            <input type="hidden" id="selected-medicine-id">
                            <ul id="medicine-name-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <div class="relative">
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <input
                                type="text"
                                id="medicine-category-input"
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline cursor-pointer"
                                placeholder="Search or select a Category"
                            >
                            <input type="hidden" id="medicine-category-id">
                            <ul id="category-medicine_dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <input type="text" step="0.01" id="modal-description" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S. OS">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Stock Origin</label>
                            <input type="text" step="0.01" id="modal-stock_origin" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S.ORIGIN">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" id="modal-quantity" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Qty">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" id="close-medicine-modal-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Shared Data and Functions ---
            const medicines = @json($products);
            const cosmeticCategories = @json($cosmetic_categories);
            const cosmetics = @json($cosmetics);

            let cosmeticsData = [];
            let cosmeticCounter = 0;

            let medicinesData = [];
            let medicineCounter = 0;


            // --- DOM Elements ---
            const productInput = document.getElementById('medicine-product-input');
            const productHiddenInput = document.getElementById('medicine-product');
            const productDropdown = document.getElementById('product-dropdown');
            // Add a condition input
            const conditionInput = document.getElementById('condition-input'); // Assuming you add this input to your HTML
            const barcodeInput = document.getElementById('barcode-input');
            const addImeiBtn = document.getElementById('add-barcode-btn');
            const barcodeList = document.getElementById('barcode-list');
            const noImeiPlaceholder = document.getElementById('no-barcode-placeholder');
            const medicineHiddenInputs = document.getElementById('medicine-hidden-inputs');

            const cosmeticModal = document.getElementById('cosmetic-modal');
            const addCosmeticBtn = document.getElementById('add-cosmetic-btn');
            const addMedicineBtn = document.getElementById('add-medicine-btn');
            const closeCosmeticModalBtn = document.getElementById('close-cosmetic-modal-btn');
            const medicineModal = document.getElementById('medicine-modal');
            const closeMedicineModalBtn = document.getElementById('close-medicine-modal-btn');
            const cosmeticForm = document.getElementById('cosmetic-form');
            const medicineForm = document.getElementById('medicine-form');
            const cosmeticList = document.getElementById('cosmetic-list');
            const medicineList = document.getElementById('medicine-list');
            const cosmeticNameInput = document.getElementById('modal-name');
            const medicineNameInput = document.getElementById('modal-medicine_name');
            const cosmeticNameDropdown = document.getElementById('cosmetic-name-dropdown');
            const medicineNameDropdown = document.getElementById('medicine-name-dropdown');
            const selectedCosmeticIdInput = document.getElementById('selected-cosmetic-id');
            const selectedMedicineIdInput = document.getElementById('selected-medicine-id');
            const cosmeticCategoryInput = document.getElementById('cosmetic-category-input');
            const medicineCategoryInput = document.getElementById('medicine-category-input');
            const cosmeticCategoryIdHidden = document.getElementById('cosmetic-category-id');
            const medicineCategoryIdHidden = document.getElementById('medicine-category-id');
            const cosmeticCategoryDropdown = document.getElementById('category-dropdown');
            const medicineCategoryDropdown = document.getElementById('category-medicine_dropdown');
            const mainForm = document.getElementById('main-form');
            const medicinePurchasePriceInput = document.getElementById('medicine-purchase-price');
            const medicineSellingPriceInput = document.getElementById('medicine-selling-price');
            const descriptionInput = document.getElementById('description');
            const stockOriginInput = document.getElementById('stock_origin');
            const submitButton = mainForm.querySelector('button[type="submit"]');

            // let medicinesData = [];

            function updateSubmitButtonState() {
                if (medicinesData.length > 0 || cosmeticsData.length > 0) {
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

            // --- BARCODEs Section (Main Logic) ---

            function addImei() {
                const barcode = barcodeInput.value.trim();
                // if (!barcode) {
                //     showToast('BARCODE cannot be empty.', 'error');
                //     return;
                // }

                const product = productInput.value.trim();
                const purchasePrice = medicinePurchasePriceInput.value;
                const sellingPrice = medicineSellingPriceInput.value;
                const description = descriptionInput.value;
                const stockOrigin = stockOriginInput.value;
                const productId = productHiddenInput.value;

                if (!productId ||!colorId || !storageId || !purchasePrice || !sellingPrice || !description || !stockOrigin) {
                    showToast('Please fill in all medicine details (Product and Prices) from the dropdowns before adding an BARCODE.', 'error');
                    return;
                }

                if (medicinesData.some(medicine => medicine.barcode === barcode)) {
                    showToast('BARCODE already exists in the list.', 'error');
                    barcodeInput.value = '';
                    return;
                }

                medicinesData.push({
                    product_id: productId,
                    product_name: product,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice,
                    description: description,
                    stock_origin: stockOrigin,
                    // You can hardcode a condition for now or add a dropdown to your HTML
                    condition: 'New'
                });

                renderImeiList();
                barcodeInput.value = '';
                barcodeInput.focus();
                showToast('BARCODE added successfully!', 'success');

                updateSubmitButtonState();
            }

            let barcodeTypingTimer;
            const barcodeDoneTypingInterval = 200;


            function renderImeiList() {
                if (noImeiPlaceholder) noImeiPlaceholder.classList.add('hidden');
                barcodeList.innerHTML = '';

                // Remove existing hidden inputs to avoid duplicates
                document.querySelectorAll('input[name^="medicines"]').forEach(el => el.remove());

                medicinesData.forEach((medicine, index) => {
                    const listItem = document.createElement('li');
                    listItem.className = 'flex justify-between items-center bg-white p-3 rounded-md shadow-sm mb-2 text-gray-700';
                    listItem.innerHTML = `
<!--                <span><strong>BARCODE:</strong> ${medicine.barcode} - ${medicine.product_name} ${medicine.model_name}</span>-->
                <button type="button" class="remove-barcode-btn text-red-500 hover:text-red-700 font-bold" data-index="${index}">&times;</button>
            `;
                    barcodeList.appendChild(listItem);
                    // Now, handle the nested 'barcodes' array

                    const inputCondition = document.createElement('input');
                    inputCondition.type = 'hidden';
                    inputCondition.name = `medicines[${index}][barcodes][0][condition]`;
                    inputCondition.value = medicine.condition;
                    medicineHiddenInputs.appendChild(inputCondition);

                    // Also, don't forget the other fields that don't need to be nested
                    const inputProductId = document.createElement('input');
                    inputProductId.type = 'hidden';
                    inputProductId.name = `medicines[${index}][product_id]`;
                    inputProductId.value = medicine.product_id;
                    medicineHiddenInputs.appendChild(inputProductId);

                    const inputPurchasePrice = document.createElement('input');
                    inputPurchasePrice.type = 'hidden';
                    inputPurchasePrice.name = `medicines[${index}][purchase_price]`;
                    inputPurchasePrice.value = medicine.purchase_price;
                    medicineHiddenInputs.appendChild(inputPurchasePrice);

                    const inputSellingPrice = document.createElement('input');
                    inputSellingPrice.type = 'hidden';
                    inputSellingPrice.name = `medicines[${index}][selling_price]`;
                    inputSellingPrice.value = medicine.selling_price;
                    medicineHiddenInputs.appendChild(inputSellingPrice);

                    const inputDescription = document.createElement('input');
                    inputDescription.type = 'hidden';
                    inputDescription.name = `medicines[${index}][description]`;
                    inputDescription.value = medicine.description;
                    medicineHiddenInputs.appendChild(inputDescription);

                    const inputStockOrigin = document.createElement('input');
                    inputStockOrigin.type = 'hidden';
                    inputStockOrigin.name = `medicines[${index}][stock_origin]`;
                    inputStockOrigin.value = medicine.stock_origin;
                    medicineHiddenInputs.appendChild(inputStockOrigin);
                });

                if (medicinesData.length === 0) {
                    if (noImeiPlaceholder) noImeiPlaceholder.classList.remove('hidden');
                }
            }


            // --- Other Sections (dropdowns, cosmetics, etc.) ---
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

            // productInput.addEventListener('input', () => {
            //     const filter = productInput.value.toLowerCase();
            //     const filteredProducts = products.filter(b => b.name.toLowerCase().includes(filter));
            //     renderDropdown(productDropdown, filteredProducts, 'product', productInput, productHiddenInput, true);
            //     productDropdown.classList.remove('hidden');
            // });
            //
            // productInput.addEventListener('focus', () => {
            //     const filter = productInput.value.toLowerCase();
            //     const filteredProducts = products.filter(b => b.name.toLowerCase().includes(filter));
            //     renderDropdown(productDropdown, filteredProducts, 'product', productInput, productHiddenInput, true);
            //     productDropdown.classList.remove('hidden');
            // });

            cosmeticCategoryInput.addEventListener('input', () => {
                const filter = cosmeticCategoryInput.value.toLowerCase();
                const filteredCategories = cosmeticCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(cosmeticCategoryDropdown, filteredCategories, 'category', cosmeticCategoryInput, cosmeticCategoryIdHidden, true);
                cosmeticCategoryDropdown.classList.remove('hidden');
            });
            cosmeticCategoryInput.addEventListener('focus', () => {
                const filter = cosmeticCategoryInput.value.toLowerCase();
                const filteredCategories = cosmeticCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(cosmeticCategoryDropdown, filteredCategories, 'category', cosmeticCategoryInput, cosmeticCategoryIdHidden, true);
                cosmeticCategoryDropdown.classList.remove('hidden');
            });
            medicineCategoryInput.addEventListener('input', () => {
                const filter = medicineCategoryInput.value.toLowerCase();
                const filteredCategories = medicineCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(medicineCategoryDropdown, filteredCategories, 'category', medicineCategoryInput, medicineCategoryIdHidden, true);
                medicineCategoryDropdown.classList.remove('hidden');
            });
            medicineCategoryInput.addEventListener('focus', () => {
                const filter = medicineCategoryInput.value.toLowerCase();
                const filteredCategories = medicineCategories.filter(c => c.name.toLowerCase().includes(filter));
                renderDropdown(medicineCategoryDropdown, filteredCategories, 'category', medicineCategoryInput, medicineCategoryIdHidden, true);
                medicineCategoryDropdown.classList.remove('hidden');
            });

            document.addEventListener('click', (event) => {
                // if (!productInput.parentElement.contains(event.target)) {
                //     productDropdown.classList.add('hidden');
                // }
                if (!cosmeticCategoryInput.parentElement.contains(event.target)) {
                    cosmeticCategoryDropdown.classList.add('hidden');
                }
                if (!cosmeticNameInput.parentElement.contains(event.target)) {
                    cosmeticNameDropdown.classList.add('hidden');
                }
                if (!medicineCategoryInput.parentElement.contains(event.target)) {
                    medicineCategoryDropdown.classList.add('hidden');
                }
                if (!medicineNameInput.parentElement.contains(event.target)) {
                    medicineNameDropdown.classList.add('hidden');
                }
            });

            addCosmeticBtn.addEventListener('click', () => {
                cosmeticForm.reset();
                selectedCosmeticIdInput.value = '';
                document.getElementById('cosmetic-index').value = '';
                cosmeticModal.classList.remove('hidden');
            });
            addMedicineBtn.addEventListener('click', () => {
                medicineForm.reset();
                selectedMedicineIdInput.value = '';
                document.getElementById('medicine-index').value = '';
                medicineModal.classList.remove('hidden');
            });

            closeCosmeticModalBtn.addEventListener('click', () => cosmeticModal.classList.add('hidden'));
            closeMedicineModalBtn.addEventListener('click', () => medicineModal.classList.add('hidden'));

            cosmeticNameInput.addEventListener('input', () => {
                const filter = cosmeticNameInput.value.toLowerCase();
                const filteredCosmetics = cosmetics.filter(acc => acc.name.toLowerCase().includes(filter));
                renderCosmeticNameDropdown(filteredCosmetics, cosmeticNameDropdown);
                cosmeticNameDropdown.classList.remove('hidden');
            });

            cosmeticNameInput.addEventListener('focus', () => {
                const filter = cosmeticNameInput.value.toLowerCase();
                const filteredCosmetics = cosmetics.filter(acc => acc.name.toLowerCase().includes(filter));
                renderCosmeticNameDropdown(filteredCosmetics, cosmeticNameDropdown);
                cosmeticNameDropdown.classList.remove('hidden');
            });
            medicineNameInput.addEventListener('input', () => {
                const filter = medicineNameInput.value.toLowerCase();
                const filteredMedicines = medicines.filter(acc => acc.name.toLowerCase().includes(filter));
                renderMedicineNameDropdown(filteredMedicines, medicineNameDropdown);
                medicineNameDropdown.classList.remove('hidden');
            });

            medicineNameInput.addEventListener('focus', () => {
                const filter = medicineNameInput.value.toLowerCase();
                const filteredMedicines = medicines.filter(acc => acc.name.toLowerCase().includes(filter));
                renderMedicineNameDropdown(filteredMedicines, medicineNameDropdown);
                medicineNameDropdown.classList.remove('hidden');
            });

            function renderCosmeticNameDropdown(data, dropdownElement) {
                dropdownElement.innerHTML = '';
                if (data.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-3 py-2 text-gray-500';
                    noResults.textContent = 'No existing cosmetics found. Create a new one.';
                    dropdownElement.appendChild(noResults);
                } else {
                    data.forEach(item => {
                        const listItem = document.createElement('li');
                        listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';
                        listItem.textContent = `${item.name} ($${parseFloat(item.selling_price).toFixed(2)})`;
                        listItem.addEventListener('click', () => {
                            cosmeticNameInput.value = item.name;
                            selectedCosmeticIdInput.value = item.id;
                            document.getElementById('modal-purchase-price').value = item.purchase_price;
                            document.getElementById('modal-selling-price').value = item.selling_price;
                            document.getElementById('modal-description').value = item.description;
                            document.getElementById('modal-stock_origin').value = item.stock_origin;
                            document.getElementById('modal-unit').value = item.unit;
                            if (item.category_id) {
                                const category = cosmeticCategories.find(c => c.id === item.category_id);
                                if (category) {
                                    cosmeticCategoryInput.value = category.name;
                                    cosmeticCategoryIdHidden.value = category.id;
                                }
                            }
                            dropdownElement.classList.add('hidden');
                        });
                        dropdownElement.appendChild(listItem);
                    });
                }
            }

            function renderMedicineNameDropdown(data, dropdownElement) {
                dropdownElement.innerHTML = '';
                if (data.length === 0) {
                    const noResults = document.createElement('li');
                    noResults.className = 'px-3 py-2 text-gray-500';
                    noResults.textContent = 'No existing cosmetics found. Create a new one.';
                    dropdownElement.appendChild(noResults);
                } else {
                    data.forEach(item => {
                        const listItem = document.createElement('li');
                        listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';
                        listItem.textContent = `${item.name} ($${parseFloat(item.selling_price).toFixed(2)})`;
                        listItem.addEventListener('click', () => {
                            medicineNameInput.value = item.name;
                            selectedMedicineIdInput.value = item.id;
                            document.getElementById('modal-purchase-price').value = item.purchase_price;
                            document.getElementById('modal-selling-price').value = item.selling_price;
                            document.getElementById('modal-description').value = item.description;
                            document.getElementById('modal-stock_origin').value = item.stock_origin;
                            document.getElementById('modal-unit').value = item.unit;
                            alert(item.category_id);
                            if (item.category_id) {
                                const category = medicineCategories.find(c => c.id === item.category_id);
                                if (category) {
                                    medicineCategoryInput.value = category.name;
                                    medicineCategoryIdHidden.value = category.id;
                                }
                            }
                            dropdownElement.classList.add('hidden');
                        });
                        dropdownElement.appendChild(listItem);
                    });
                }
            }

            cosmeticForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const index = document.getElementById('cosmetic-index').value;
                const name = document.getElementById('modal-name').value;
                const existingCosmeticId = selectedCosmeticIdInput.value;
                const categoryId = document.getElementById('cosmetic-category-id').value;
                const categoryName = document.getElementById('cosmetic-category-input').value;
                const barcode = document.getElementById('modal-barcode').value;
                const unit = document.getElementById('modal-unit').value;
                const purchasePrice = document.getElementById('modal-purchase-price').value;
                const sellingPrice = document.getElementById('modal-selling-price').value;
                const stockOrigin = document.getElementById('modal-stock_origin').value;
                const description = document.getElementById('modal-description').value;
                const quantity = document.getElementById('modal-quantity').value;

                if (!name || !quantity) {
                    showToast('Name and Quantity are required.', 'error');
                    return;
                }

                const cosmetic = {
                    id: existingCosmeticId || (index ? cosmeticsData[index].id : cosmeticCounter++),
                    name,
                    category_id: categoryId,
                    category_name: categoryName,
                    barcode,
                    unit,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice,
                    description: description,
                    stock_origin: stockOrigin,
                    quantity
                };

                if (index) {
                    cosmeticsData[index] = cosmetic;
                } else {
                    cosmeticsData.push(cosmetic);
                }
                renderCosmeticList();
                cosmeticModal.classList.add('hidden');
            });
            medicineForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const indexMedicine = document.getElementById('medicine-index').value;
                const name = document.getElementById('modal-medicine_name').value;
                const existingMedicineId = selectedMedicineIdInput.value;
                const categoryId = document.getElementById('medicine-category-id').value;
                const categoryName = document.getElementById('medicine-category-input').value;
                const barcode = document.getElementById('modal-barcode').value;
                const unit = document.getElementById('modal-unit').value;
                const purchasePrice = document.getElementById('modal-purchase-price').value;
                const sellingPrice = document.getElementById('modal-selling-price').value;
                const stockOrigin = document.getElementById('modal-stock_origin').value;
                const description = document.getElementById('modal-description').value;
                const quantity = document.getElementById('modal-quantity').value;

                if (!name || !quantity) {
                    showToast('Name and Quantity are required.', 'error');
                    return;
                }

                const medicine = {
                    id: existingMedicineId || (indexMedicine ? medicinesData[indexMedicine].id : medicineCounter++),
                    name,
                    category_id: categoryId,
                    category_name: categoryName,
                    barcode,
                    unit,
                    purchase_price: purchasePrice,
                    selling_price: sellingPrice,
                    description: description,
                    stock_origin: stockOrigin,
                    quantity
                };

                if (indexMedicine) {
                    medicinesData[indexMedicine] = medicine;
                } else {
                    medicinesData.push(medicine);
                }
                renderMedicineList();
                medicineModal.classList.add('hidden');
            });

            function renderCosmeticList() {
                cosmeticList.innerHTML = '';

                document.querySelectorAll('input[name^="cosmetics"]').forEach(el => el.remove());

                cosmeticsData.forEach((cosmetic, index) => {
                    const card = document.createElement('div');
                    card.className = 'bg-gray-100 p-4 rounded-md shadow-sm mb-2';
                    card.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold">${cosmetic.name}</h4>
                        <p class="text-sm text-gray-600">Category: ${cosmetic.category_name || 'N/A'}</p>
                        ${cosmetic.barcode ? `<p class="text-xs text-gray-400">Barcode: ${cosmetic.barcode}</p>` : ''}
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">${cosmetic.quantity} pcs</p>
                        <p class="text-sm text-gray-500">P.Price: $${parseFloat(cosmetic.purchase_price || 0).toFixed(2)}</p>
                    </div>
                </div>
                <div class="flex justify-end mt-2 space-x-2">
                    <button type="button" class="edit-cosmetic-btn text-blue-500 hover:text-blue-700 font-bold text-sm" data-index="${index}">Edit</button>
                    <button type="button" class="remove-cosmetic-btn text-red-500 hover:text-red-700 font-bold text-sm" data-index="${index}">Remove</button>
                </div>
            `;
                    cosmeticList.appendChild(card);

                    for (const key in cosmetic) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `cosmetics[${index}][${key}]`;
                        input.value = cosmetic[key];
                        medicineHiddenInputs.appendChild(input);
                    }
                });
                updateSubmitButtonState();
            }

            function renderMedicineList() {
                medicineList.innerHTML = '';

                document.querySelectorAll('input[name^="medicines"]').forEach(el => el.remove());

                medicinesData.forEach((medicine, indexMedicine) => {
                    const card = document.createElement('div');
                    card.className = 'bg-gray-100 p-4 rounded-md shadow-sm mb-2';
                    card.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-semibold">${medicine.name}</h4>
                        <p class="text-sm text-gray-600">Category: ${medicine.category_name || 'N/A'}</p>
                        ${medicine.barcode ? `<p class="text-xs text-gray-400">Barcode: ${medicine.barcode}</p>` : ''}
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold">${medicine.quantity} pcs</p>
                        <p class="text-sm text-gray-500">P.Price: $${parseFloat(medicine.purchase_price || 0).toFixed(2)}</p>
                    </div>
                </div>
                <div class="flex justify-end mt-2 space-x-2">
                    <button type="button" class="edit-cosmetic-btn text-blue-500 hover:text-blue-700 font-bold text-sm" data-index="${indexMedicine}">Edit</button>
                    <button type="button" class="remove-cosmetic-btn text-red-500 hover:text-red-700 font-bold text-sm" data-index="${indexMedicine}">Remove</button>
                </div>
            `;
                    medicineList.appendChild(card);

                    for (const key in medicine) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = `medicines[${indexMedicine}][${key}]`;
                        input.value =  medicine[key];
                        medicineHiddenInputs.appendChild(input);
                    }
                });
                updateSubmitButtonState();
            }

            cosmeticList.addEventListener('click', (e) => {
                if (e.target.classList.contains('edit-cosmetic-btn')) {
                    const index = e.target.dataset.index;
                    const cosmetic = cosmeticsData[index];
                    document.getElementById('cosmetic-index').value = index;
                    document.getElementById('modal-name').value = cosmetic.name;
                    document.getElementById('selected-cosmetic-id').value = cosmetic.id;
                    document.getElementById('cosmetic-category-input').value = cosmetic.category_name;
                    document.getElementById('cosmetic-category-id').value = cosmetic.category_id;
                    document.getElementById('modal-barcode').value = cosmetic.barcode;
                    document.getElementById('modal-unit').value = cosmetic.unit;
                    document.getElementById('modal-purchase-price').value = cosmetic.purchase_price;
                    document.getElementById('modal-selling-price').value = cosmetic.selling_price;
                    document.getElementById('modal-stock_origin').value = cosmetic.stock_origin;
                    document.getElementById('modal-description').value = cosmetic.description;
                    document.getElementById('modal-quantity').value = cosmetic.quantity;
                    cosmeticModal.classList.remove('hidden');
                } else if (e.target.classList.contains('remove-cosmetic-btn')) {
                    const index = e.target.dataset.index;
                    showConfirmation('Are you sure you want to remove this cosmetic?').then(result => {
                        if (result) {
                            cosmeticsData.splice(index, 1);
                            renderCosmeticList();
                            showToast('Cosmetic removed.', 'success');
                        }
                    });
                }
            });

            medicineList.addEventListener('click', (e) => {
                if (e.target.classList.contains('edit-medicine-btn')) {
                    const indexMedicine = e.target.dataset.indexMedicine;
                    const medicine = medicinesData[indexMedicine];
                    document.getElementById('medicine-index').value = indexMedicine;
                    document.getElementById('modal-name').value = medicine.name;
                    document.getElementById('selected-medicine-id').value = medicine.id;
                    document.getElementById('medicine-category-input').value = medicine.category_name;
                    document.getElementById('medicine-category-id').value = medicine.category_id;
                    document.getElementById('modal-barcode').value = medicine.barcode;
                    document.getElementById('modal-unit').value = medicine.unit;
                    document.getElementById('modal-purchase-price').value = medicine.purchase_price;
                    document.getElementById('modal-selling-price').value = medicine.selling_price;
                    document.getElementById('modal-stock_origin').value = medicine.stock_origin;
                    document.getElementById('modal-description').value = medicine.description;
                    document.getElementById('modal-quantity').value = medicine.quantity;
                    medicineModal.classList.remove('hidden');
                } else if (e.target.classList.contains('remove-medicine-btn')) {
                    const indexMedicine = e.target.dataset.indexMedicine;
                    showConfirmation('Are you sure you want to remove this medicine?').then(result => {
                        if (result) {
                            medicinesData.splice(indexMedicine, 1);
                            renderMedicineList();
                            showToast('Medicine removed.', 'success');
                        }
                    });
                }
            });

            // --- Initial Renders and State ---
            // renderImeiList();
            renderCosmeticList();
            renderMedicineList();
            updateSubmitButtonState();

            mainForm.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.tagName === 'INPUT' && e.target !== barcodeInput) {
                    e.preventDefault();
                }
            });
        });
    </script>
@endsection
