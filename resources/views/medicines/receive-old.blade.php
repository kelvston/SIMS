{{--@extends('layouts.app')--}}

{{--@section('content')--}}
{{--    <style>--}}
{{--        .modal {--}}
{{--            background-color: rgba(0, 0, 0, 0.5);--}}
{{--            transition: opacity 0.3s ease-in-out;--}}
{{--        }--}}
{{--        .modal-content {--}}
{{--            transform: translateY(-20px);--}}
{{--            transition: transform 0.3s ease-in-out;--}}
{{--        }--}}
{{--        .modal:not(.hidden) .modal-content {--}}
{{--            transform: translateY(0);--}}
{{--        }--}}
{{--    </style>--}}

{{--    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md mt-10 relative">--}}
{{--        <img src="{{ asset('images/watermark.png') }}"--}}
{{--             alt="Watermark"--}}
{{--             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"--}}
{{--             style="transform: translate(-60%, -50%);" />--}}
{{--        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Receive New Inventory</h1>--}}
{{--        <div id="toast-container" class="fixed top-4 right-4 z-50"></div>--}}
{{--        <div id="confirmation-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">--}}
{{--            <div class="flex items-center justify-center min-h-screen p-4">--}}
{{--                <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-sm w-full relative text-center">--}}
{{--                    <p id="confirmation-message" class="mb-4 text-gray-800"></p>--}}
{{--                    <div class="flex justify-center space-x-4">--}}
{{--                        <button id="cancel-confirm-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>--}}
{{--                        <button id="confirm-action-btn" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full">Confirm</button>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

{{--        @if (session('success'))--}}
{{--            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">--}}
{{--                <strong class="font-bold">Success!</strong>--}}
{{--                <span class="block sm:inline">{{ session('success') }}</span>--}}
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

{{--        <form action="{{ route('cashews.receive.store') }}" method="POST" id="main-form">--}}
{{--            @csrf--}}
{{--            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">--}}
{{--                <!-- Streamlined Phones Section -->--}}
{{--                <div class="p-6 border rounded-lg bg-white shadow-sm">--}}
{{--                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Phones</h2>--}}
{{--                    <p class="text-sm text-gray-500 mb-6">Enter shared details once, then scan each phone's IMEI to add to the list.</p>--}}

{{--                    <!-- Shared Phone Details -->--}}
{{--                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Brand:</label>--}}
{{--                            <select id="phone-brand" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">--}}
{{--                                <option value="">Select a Brand</option>--}}
{{--                                @foreach ($brands as $brand)--}}
{{--                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Model:</label>--}}
{{--                            <input type="text" id="phone-model" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., iPhone 15 Pro Max">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Color:</label>--}}
{{--                            <input type="text" id="phone-color" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., Black, Blue">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Storage Capacity:</label>--}}
{{--                            <input type="text" id="phone-storage" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., 128GB">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Purchase Price:</label>--}}
{{--                            <input type="number" step="0.01" id="phone-purchase-price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., 500.00">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Selling Price:</label>--}}
{{--                            <input type="number" step="0.01" id="phone-selling-price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="e.g., 750.00">--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <hr class="my-6 border-gray-300">--}}

{{--                    <!-- Barcode Scanner / IMEI Input -->--}}
{{--                    <div class="mb-4">--}}
{{--                        <label for="imei-input" class="block text-sm font-bold text-gray-700 mb-2">Scan Barcode or Enter IMEI:</label>--}}
{{--                        <div class="flex gap-2">--}}
{{--                            <input type="text" id="imei-input" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Scan or type IMEI...">--}}
{{--                            <button type="button" id="add-imei-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Add</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <!-- List of IMEIs to be added -->--}}
{{--                    <div class="mt-4">--}}
{{--                        <label class="block text-sm font-medium text-gray-700 mb-2">IMEIs to be received:</label>--}}
{{--                        <ul id="imei-list" class="bg-gray-100 p-4 rounded-lg shadow-inner max-h-60 overflow-y-auto">--}}
{{--                            <li id="no-imei-placeholder" class="text-gray-500 italic">No IMEIs added yet.</li>--}}
{{--                        </ul>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Accessories Section -->--}}
{{--                <div class="p-6 border rounded-lg bg-white shadow-sm">--}}
{{--                    <div class="flex items-center justify-between mb-4">--}}
{{--                        <h2 class="text-2xl font-semibold text-gray-700">Accessories</h2>--}}
{{--                        <button type="button" id="add-accessory-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">--}}
{{--                            + Add Accessory--}}
{{--                        </button>--}}
{{--                    </div>--}}
{{--                    <p class="text-sm text-gray-500 mb-6">Add an accessory and its quantity. The details will be added below as a summary.</p>--}}
{{--                    <div id="accessory-list" class="space-y-4">--}}
{{--                        <!-- Hidden inputs for accessories will be added here -->--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <!-- Submit Button and Hidden Inputs -->--}}
{{--            <div class="flex items-center justify-between mt-6">--}}
{{--                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">--}}
{{--                    Receive Inventory--}}
{{--                </button>--}}
{{--                <a href="{{ route('cashews.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">--}}
{{--                    View All Phones--}}
{{--                </a>--}}
{{--            </div>--}}

{{--            <!-- Dynamic hidden inputs for cashews -->--}}
{{--            <div id="phone-hidden-inputs"></div>--}}
{{--        </form>--}}
{{--    </div>--}}

{{--    <!-- Accessory Modal (unchanged) -->--}}
{{--    <div id="accessory-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">--}}
{{--        <div class="flex items-center justify-center min-h-screen p-4">--}}
{{--            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">--}}
{{--                <h3 class="text-xl font-bold mb-4">Add/Edit Accessory</h3>--}}
{{--                <form id="accessory-form">--}}
{{--                    <input type="hidden" id="accessory-index" value="">--}}
{{--                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">--}}
{{--                        <div class="col-span-2">--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Accessory Name</label>--}}
{{--                            <input type="text" id="modal-name" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="e.g., USB Flash Drive">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Brand</label>--}}
{{--                            <select id="modal-brand" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">--}}
{{--                                <option value="">Select Brand</option>--}}
{{--                                @foreach ($brands as $brand)--}}
{{--                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Unit</label>--}}
{{--                            <select id="modal-unit" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full">--}}
{{--                                <option value="piece">Piece</option>--}}
{{--                                <option value="pack">Pack</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Purchase Price</label>--}}
{{--                            <input type="number" step="0.01" id="modal-purchase-price" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="P. Price">--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Selling Price</label>--}}
{{--                            <input type="number" step="0.01" id="modal-selling-price" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="S. Price">--}}
{{--                        </div>--}}
{{--                        <div class="col-span-2">--}}
{{--                            <label class="block text-sm font-medium text-gray-700">Quantity</label>--}}
{{--                            <input type="number" id="modal-quantity" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Qty">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="mt-6 flex justify-end space-x-2">--}}
{{--                        <button type="button" id="close-accessory-modal-btn" class="bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-full">Cancel</button>--}}
{{--                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full">Save</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function() {--}}
{{--            const brands = @json($brands);--}}
{{--            let accessoriesData = [];--}}
{{--            let accessoryCounter = 0;--}}

{{--            // --- Accessories Section (unchanged) -----}}
{{--            const accessoryModal = document.getElementById('accessory-modal');--}}
{{--            const addAccessoryBtn = document.getElementById('add-accessory-btn');--}}
{{--            const closeAccessoryModalBtn = document.getElementById('close-accessory-modal-btn');--}}
{{--            const accessoryForm = document.getElementById('accessory-form');--}}
{{--            const accessoryList = document.getElementById('accessory-list');--}}

{{--            addAccessoryBtn.addEventListener('click', () => {--}}
{{--                accessoryForm.reset();--}}
{{--                document.getElementById('accessory-index').value = '';--}}
{{--                accessoryModal.classList.remove('hidden');--}}
{{--            });--}}
{{--            closeAccessoryModalBtn.addEventListener('click', () => accessoryModal.classList.add('hidden'));--}}

{{--            accessoryForm.addEventListener('submit', (e) => {--}}
{{--                e.preventDefault();--}}
{{--                const index = document.getElementById('accessory-index').value;--}}
{{--                const name = document.getElementById('modal-name').value;--}}
{{--                const brandId = document.getElementById('modal-brand').value;--}}
{{--                const unit = document.getElementById('modal-unit').value;--}}
{{--                const purchasePrice = document.getElementById('modal-purchase-price').value;--}}
{{--                const sellingPrice = document.getElementById('modal-selling-price').value;--}}
{{--                const quantity = document.getElementById('modal-quantity').value;--}}

{{--                if (!name || !quantity) {--}}
{{--                    showToast('Name and Quantity are required.', 'error');--}}
{{--                    return;--}}
{{--                }--}}

{{--                const brandName = document.getElementById('modal-brand').options[document.getElementById('modal-brand').selectedIndex].text;--}}
{{--                const accessory = {--}}
{{--                    id: index ? accessoriesData[index].id : accessoryCounter++,--}}
{{--                    name, brand_id: brandId, brand_name: brandName, unit, purchase_price: purchasePrice, selling_price: sellingPrice, quantity--}}
{{--                };--}}

{{--                if (index) {--}}
{{--                    accessoriesData[index] = accessory;--}}
{{--                } else {--}}
{{--                    accessoriesData.push(accessory);--}}
{{--                }--}}
{{--                renderAccessoryList();--}}
{{--                accessoryModal.classList.add('hidden');--}}
{{--            });--}}

{{--            function renderAccessoryList() {--}}
{{--                accessoryList.innerHTML = '';--}}
{{--                accessoriesData.forEach((accessory, index) => {--}}
{{--                    const card = document.createElement('div');--}}
{{--                    card.className = 'flex items-center justify-between p-4 border rounded-lg bg-gray-50 shadow-sm';--}}
{{--                    card.innerHTML = `--}}
{{--                        <div>--}}
{{--                            <span class="font-semibold text-gray-800">${accessory.name} - ${accessory.brand_name}</span>--}}
{{--                            <p class="text-sm text-gray-500">${accessory.quantity} ${accessory.unit}(s) @ $${parseFloat(accessory.selling_price).toFixed(2)}</p>--}}
{{--                        </div>--}}
{{--                        <div class="flex space-x-2">--}}
{{--                            <button type="button" onclick="editAccessory(${index})" class="text-blue-500 hover:text-blue-700">Edit</button>--}}
{{--                            <button type="button" onclick="removeAccessory(${index})" class="text-red-500 hover:text-red-700">Remove</button>--}}
{{--                        </div>--}}
{{--                        <input type="hidden" name="accessories[${index}][name]" value="${accessory.name}">--}}
{{--                        <input type="hidden" name="accessories[${index}][brand_id]" value="${accessory.brand_id}">--}}
{{--                        <input type="hidden" name="accessories[${index}][unit]" value="${accessory.unit}">--}}
{{--                        <input type="hidden" name="accessories[${index}][purchase_price]" value="${accessory.purchase_price}">--}}
{{--                        <input type="hidden" name="accessories[${index}][selling_price]" value="${accessory.selling_price}">--}}
{{--                        <input type="hidden" name="accessories[${index}][quantity]" value="${accessory.quantity}">--}}
{{--                    `;--}}
{{--                    accessoryList.appendChild(card);--}}
{{--                });--}}
{{--            }--}}

{{--            window.editAccessory = (index) => {--}}
{{--                const accessory = accessoriesData[index];--}}
{{--                document.getElementById('modal-name').value = accessory.name;--}}
{{--                document.getElementById('modal-brand').value = accessory.brand_id;--}}
{{--                document.getElementById('modal-unit').value = accessory.unit;--}}
{{--                document.getElementById('modal-purchase-price').value = accessory.purchase_price;--}}
{{--                document.getElementById('modal-selling-price').value = accessory.selling_price;--}}
{{--                document.getElementById('modal-quantity').value = accessory.quantity;--}}
{{--                document.getElementById('accessory-index').value = index;--}}
{{--                accessoryModal.classList.remove('hidden');--}}
{{--            };--}}

{{--            window.removeAccessory = (index) => {--}}
{{--                showConfirmation('Are you sure you want to remove this accessory?', () => {--}}
{{--                    accessoriesData.splice(index, 1);--}}
{{--                    renderAccessoryList();--}}
{{--                });--}}
{{--            };--}}

{{--            // --- Streamlined Phone Section Logic -----}}
{{--            const mainForm = document.getElementById('main-form');--}}
{{--            const imeiInput = document.getElementById('imei-input');--}}
{{--            const addImeiBtn = document.getElementById('add-imei-btn');--}}
{{--            const imeiList = document.getElementById('imei-list');--}}
{{--            const noImeiPlaceholder = document.getElementById('no-imei-placeholder');--}}
{{--            const phoneHiddenInputsContainer = document.getElementById('phone-hidden-inputs');--}}

{{--            let imeisToAdd = new Set(); // Use a Set for unique IMEIs--}}

{{--            // Re-render the list of IMEIs--}}
{{--            function renderImeiList() {--}}
{{--                imeiList.innerHTML = '';--}}
{{--                if (imeisToAdd.size === 0) {--}}
{{--                    imeiList.appendChild(noImeiPlaceholder);--}}
{{--                } else {--}}
{{--                    imeisToAdd.forEach(imei => {--}}
{{--                        const listItem = document.createElement('li');--}}
{{--                        listItem.className = 'flex items-center justify-between p-2 my-1 bg-white rounded shadow-sm';--}}
{{--                        listItem.innerHTML = `--}}
{{--                            <span>${imei}</span>--}}
{{--                            <button type="button" data-imei="${imei}" class="remove-imei-btn text-red-500 hover:text-red-700 font-bold transition-colors">--}}
{{--                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />--}}
{{--                                </svg>--}}
{{--                            </button>--}}
{{--                        `;--}}
{{--                        imeiList.appendChild(listItem);--}}
{{--                    });--}}
{{--                }--}}
{{--            }--}}

{{--            // Add IMEI function with toast notification--}}
{{--            function addImei(imei) {--}}
{{--                if (imei.length > 0) {--}}
{{--                    if (imeisToAdd.has(imei)) {--}}
{{--                        showToast(`IMEI ${imei} is already in the list.`, 'error');--}}
{{--                    } else {--}}
{{--                        imeisToAdd.add(imei);--}}
{{--                        renderImeiList();--}}
{{--                        showToast(`IMEI ${imei} added successfully.`, 'success');--}}
{{--                    }--}}
{{--                    imeiInput.value = '';--}}
{{--                    imeiInput.focus();--}}
{{--                }--}}
{{--            }--}}

{{--            // Manual add button listener--}}
{{--            addImeiBtn.addEventListener('click', () => {--}}
{{--                addImei(imeiInput.value.trim());--}}
{{--            });--}}

{{--            // Remove IMEI button listener--}}
{{--            imeiList.addEventListener('click', (event) => {--}}
{{--                const removeBtn = event.target.closest('.remove-imei-btn');--}}
{{--                if (removeBtn) {--}}
{{--                    const imeiToRemove = removeBtn.dataset.imei;--}}
{{--                    showConfirmation(`Are you sure you want to remove IMEI ${imeiToRemove}?`, () => {--}}
{{--                        imeisToAdd.delete(imeiToRemove);--}}
{{--                        renderImeiList();--}}
{{--                        showToast(`IMEI ${imeiToRemove} removed.`, 'success');--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}

{{--            // Barcode Scanner Logic (keypress listener)--}}
{{--            let barcode = '';--}}
{{--            let barcodeTimeout;--}}
{{--            const barcodeTimeoutDuration = 200; // Time in ms to wait for the next character--}}

{{--            document.addEventListener('keypress', (event) => {--}}
{{--                // Ignore keypress if the imei input is not focused and another input is--}}
{{--                if (document.activeElement.tagName === 'INPUT' && document.activeElement.type === 'text' && document.activeElement.id !== 'imei-input') {--}}
{{--                    return;--}}
{{--                }--}}

{{--                if (event.key === 'Enter' || event.keyCode === 13) {--}}
{{--                    event.preventDefault();--}}
{{--                    if (barcode.length > 0) {--}}
{{--                        addImei(barcode);--}}
{{--                    }--}}
{{--                    barcode = '';--}}
{{--                } else {--}}
{{--                    barcode += event.key;--}}
{{--                    clearTimeout(barcodeTimeout);--}}
{{--                    barcodeTimeout = setTimeout(() => {--}}
{{--                        barcode = '';--}}
{{--                    }, barcodeTimeoutDuration);--}}
{{--                }--}}
{{--            });--}}

{{--            // Handle form submission to inject hidden inputs--}}
{{--            mainForm.addEventListener('submit', (e) => {--}}
{{--                e.preventDefault(); // Prevent default submission--}}

{{--                // Clear previous hidden inputs to avoid duplicates--}}
{{--                phoneHiddenInputsContainer.innerHTML = '';--}}

{{--                // Get the shared phone details from the form--}}
{{--                const phoneDetails = {--}}
{{--                    brand_id: document.getElementById('phone-brand').value,--}}
{{--                    model: document.getElementById('phone-model').value,--}}
{{--                    color: document.getElementById('phone-color').value,--}}
{{--                    storage_capacity: document.getElementById('phone-storage').value,--}}
{{--                    purchase_price: document.getElementById('phone-purchase-price').value,--}}
{{--                    selling_price: document.getElementById('phone-selling-price').value,--}}
{{--                };--}}

{{--                // Create a single "phone" entry with all IMEIs--}}
{{--                if (imeisToAdd.size > 0) {--}}
{{--                    // Create hidden inputs for the shared phone details--}}
{{--                    for (const key in phoneDetails) {--}}
{{--                        const input = document.createElement('input');--}}
{{--                        input.type = 'hidden';--}}
{{--                        input.name = `cashews[0][${key}]`;--}}
{{--                        input.value = phoneDetails[key];--}}
{{--                        phoneHiddenInputsContainer.appendChild(input);--}}
{{--                    }--}}

{{--                    // Create a hidden input for each IMEI--}}
{{--                    let imeiIndex = 0;--}}
{{--                    imeisToAdd.forEach(imei => {--}}
{{--                        const input = document.createElement('input');--}}
{{--                        input.type = 'hidden';--}}
{{--                        input.name = `cashews[0][imeis][${imeiIndex}]`;--}}
{{--                        input.value = imei;--}}
{{--                        phoneHiddenInputsContainer.appendChild(input);--}}
{{--                        imeiIndex++;--}}
{{--                    });--}}
{{--                }--}}

{{--                // Append accessory hidden inputs to the main form--}}
{{--                const accessoryHiddenInputs = Array.from(accessoryList.querySelectorAll('input[type="hidden"]'));--}}
{{--                accessoryHiddenInputs.forEach(input => {--}}
{{--                    const clonedInput = input.cloneNode(true);--}}
{{--                    mainForm.appendChild(clonedInput);--}}
{{--                });--}}

{{--                // Now submit the form--}}
{{--                mainForm.submit();--}}
{{--            });--}}

{{--            // Toast notification function--}}
{{--            function showToast(message, type) {--}}
{{--                const toastContainer = document.getElementById('toast-container');--}}
{{--                const toast = document.createElement('div');--}}
{{--                const toastClasses = type === 'success'--}}
{{--                    ? 'bg-green-500 text-white'--}}
{{--                    : 'bg-red-500 text-white';--}}

{{--                toast.className = `p-4 my-2 rounded-lg shadow-xl transition-opacity duration-300 ease-in-out opacity-0 ${toastClasses}`;--}}
{{--                toast.textContent = message;--}}

{{--                toastContainer.appendChild(toast);--}}

{{--                setTimeout(() => {--}}
{{--                    toast.style.opacity = 1;--}}
{{--                }, 10);--}}

{{--                setTimeout(() => {--}}
{{--                    toast.style.opacity = 0;--}}
{{--                    setTimeout(() => toast.remove(), 500);--}}
{{--                }, 3000);--}}
{{--            }--}}

{{--            // Custom Confirmation Modal--}}
{{--            const confirmationModal = document.getElementById('confirmation-modal');--}}
{{--            const confirmationMessage = document.getElementById('confirmation-message');--}}
{{--            const cancelConfirmBtn = document.getElementById('cancel-confirm-btn');--}}
{{--            const confirmActionBtn = document.getElementById('confirm-action-btn');--}}
{{--            let confirmCallback = null;--}}

{{--            function showConfirmation(message, onConfirm) {--}}
{{--                confirmationMessage.textContent = message;--}}
{{--                confirmCallback = onConfirm;--}}
{{--                confirmationModal.classList.remove('hidden');--}}
{{--            }--}}

{{--            confirmActionBtn.addEventListener('click', () => {--}}
{{--                if (confirmCallback) {--}}
{{--                    confirmCallback();--}}
{{--                }--}}
{{--                confirmationModal.classList.add('hidden');--}}
{{--            });--}}

{{--            cancelConfirmBtn.addEventListener('click', () => {--}}
{{--                confirmationModal.classList.add('hidden');--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}


@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.min.css') }}">

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

        <form action="{{ route('cashews.receive.store') }}" method="POST" id="main-form">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                <!-- Streamlined Phones Section -->
                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Phones</h2>
                    <p class="text-sm text-gray-500 mb-6">Enter shared details once, then scan each phone's IMEI to add to the list.</p>

                    <!-- Shared Phone Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <!-- Searchable Brand Select -->
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
                        <!-- Searchable Model Select -->
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
                        <!-- Searchable Color Select -->
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
                        <!-- Searchable Storage Capacity Select -->
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

                    <!-- Barcode Scanner / IMEI Input -->
                    <div class="mb-4">
                        <label for="imei-input" class="block text-sm font-bold text-gray-700 mb-2">Scan Barcode or Enter IMEI:</label>
                        <div class="flex gap-2">
                            <input type="text" id="imei-input" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Scan or type IMEI...">
                            <button type="button" id="add-imei-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Add</button>
                        </div>
                    </div>

                    <!-- List of IMEIs to be added -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">IMEIs to be received:</label>
                        <ul id="imei-list" class="bg-gray-100 p-4 rounded-lg shadow-inner max-h-60 overflow-y-auto">
                            <li id="no-imei-placeholder" class="text-gray-500 italic">No IMEIs added yet.</li>
                        </ul>
                    </div>
                </div>

                <!-- Accessories Section -->
                <div class="p-6 border rounded-lg bg-white shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-2xl font-semibold text-gray-700">Accessories</h2>
                        <button type="button" id="add-accessory-btn" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">
                            + Add Accessory
                        </button>
                    </div>
                    <p class="text-sm text-gray-500 mb-6">Add an accessory and its quantity. The details will be added below as a summary.</p>
                    <div id="accessory-list" class="space-y-4">
                        <!-- Hidden inputs for accessories will be added here -->
                    </div>
                </div>
            </div>

            <!-- Submit Button and Hidden Inputs -->
            <div class="flex items-center justify-between mt-6">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Receive Inventory
                </button>
                <a href="{{ route('cashews.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    View All Phones
                </a>
            </div>

            <!-- Dynamic hidden inputs for cashews -->
            <div id="phone-hidden-inputs"></div>
        </form>
    </div>

    <!-- Accessory Modal (updated) -->
    <div id="accessory-modal" class="modal fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="modal-content bg-white rounded-lg shadow-xl p-8 max-w-lg w-full relative">
                <h3 class="text-xl font-bold mb-4">Add/Edit Accessory</h3>
                <form id="accessory-form">
                    <input type="hidden" id="accessory-index" value="">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- UPDATED: Accessory Name with searchable dropdown -->
                        <div class="col-span-2 relative">
                            <label class="block text-sm font-medium text-gray-700">Accessory Name</label>
                            <input
                                type="text"
                                id="modal-name"
                                class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                placeholder="Search or enter a new Accessory"
                            >
                            <!-- Hidden input to store the ID of the selected existing accessory -->
                            <input type="hidden" id="selected-accessory-id">
                            <!-- Dropdown for search results -->
                            <ul id="accessory-name-dropdown" class="absolute z-10 w-full bg-white border border-gray-300 mt-1 rounded-md shadow-lg max-h-48 overflow-y-auto hidden"></ul>
                        </div>
                        <!-- END OF UPDATES -->
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
                        {{--                        <div>--}}
                        {{--                            <label class="block text-sm font-medium text-gray-700">Barcode</label>--}}
                        {{--                            <input type="text" id="modal-barcode" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Scan or type barcode">--}}
                        {{--                        </div>--}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input type="text" id="modal-barcode" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full" placeholder="Scan or type barcode">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Barcode</label>
                            <input type="text" id="modal-barcode" autocomplete="off"
                                   class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline w-full"
                                   placeholder="Scan or type barcode">
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
        let barcode = '';
        let interval;

        document.addEventListener('keydown', function (e) {
            if (interval) clearInterval(interval);

            if (e.key === 'Enter') {
                if (barcode.length >= 3) { // Only alert if barcode is long enough
                    alert('Barcode scanned: ' + barcode);
                }
                barcode = ''; // Reset after enter
                return;
            }

            if (e.key.length === 1) {
                barcode += e.key;

                // Reset barcode after 50ms of inactivity
                interval = setTimeout(() => barcode = '', 50);
            }
        });
    </script>



    {{--document.addEventListener('DOMContentLoaded', function() {--}}
    {{--    // --- Shared Data and Functions -----}}
    {{--    const brands = @json($brands);--}}
    {{--    const models = @json($models);--}}
    {{--    const colors = @json($colors);--}}
    {{--    const accessoryCategories = @json($accessory_categories);--}}
    {{--    const accessories = @json($accessories); // NEW: Get existing accessories data--}}
    {{--    // Pre-defined list of common storage capacities--}}
    {{--    const storageCapacities = [--}}
    {{--        { id: 1, name: '16GB' },--}}
    {{--        { id: 2, name: '32GB' },--}}
    {{--        { id: 3, name: '64GB' },--}}
    {{--        { id: 4, name: '128GB' },--}}
    {{--        { id: 5, name: '256GB' },--}}
    {{--        { id: 6, name: '512GB' },--}}
    {{--        { id: 7, name: '1TB' },--}}
    {{--        { id: 8, name: '2TB' }--}}
    {{--    ];--}}
    {{--    let accessoriesData = [];--}}
    {{--    let accessoryCounter = 0;--}}

    {{--    // --- Brand, Model, Color, and Storage Search Logic (Vanilla JS) -----}}
    {{--    const brandInput = document.getElementById('phone-brand-input');--}}
    {{--    const brandHiddenInput = document.getElementById('phone-brand');--}}
    {{--    const brandDropdown = document.getElementById('brand-dropdown');--}}
    {{--    const modelInput = document.getElementById('phone-model-input');--}}
    {{--    const modelHiddenInput = document.getElementById('phone-model');--}}
    {{--    const modelDropdown = document.getElementById('model-dropdown');--}}
    {{--    const colorInput = document.getElementById('phone-color-input');--}}
    {{--    const colorHiddenInput = document.getElementById('phone-color');--}}
    {{--    const colorDropdown = document.getElementById('color-dropdown');--}}
    {{--    const storageInput = document.getElementById('phone-storage-input');--}}
    {{--    const storageHiddenInput = document.getElementById('phone-storage');--}}
    {{--    const storageDropdown = document.getElementById('storage-dropdown');--}}

    {{--    // Handle Brand search--}}
    {{--    brandInput.addEventListener('input', () => {--}}
    {{--        const filter = brandInput.value.toLowerCase();--}}
    {{--        const filteredBrands = brands.filter(b => b.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(brandDropdown, filteredBrands, 'brand', brandInput, brandHiddenInput);--}}
    {{--        brandDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    brandInput.addEventListener('focus', () => {--}}
    {{--        const filter = brandInput.value.toLowerCase();--}}
    {{--        const filteredBrands = brands.filter(b => b.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(brandDropdown, filteredBrands, 'brand', brandInput, brandHiddenInput);--}}
    {{--        brandDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // Handle Model search--}}
    {{--    modelInput.addEventListener('input', () => {--}}
    {{--        const filter = modelInput.value.toLowerCase();--}}
    {{--        const filteredModels = models.filter(m => m.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(modelDropdown, filteredModels, 'model', modelInput, modelHiddenInput);--}}
    {{--        modelDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    modelInput.addEventListener('focus', () => {--}}
    {{--        const filter = modelInput.value.toLowerCase();--}}
    {{--        const filteredModels = models.filter(m => m.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(modelDropdown, filteredModels, 'model', modelInput, modelHiddenInput);--}}
    {{--        modelDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // Handle Color search--}}
    {{--    colorInput.addEventListener('input', () => {--}}
    {{--        const filter = colorInput.value.toLowerCase();--}}
    {{--        const filteredColors = colors.filter(c => c.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(colorDropdown, filteredColors, 'color', colorInput, colorHiddenInput);--}}
    {{--        colorDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    colorInput.addEventListener('focus', () => {--}}
    {{--        const filter = colorInput.value.toLowerCase();--}}
    {{--        const filteredColors = colors.filter(c => c.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(colorDropdown, filteredColors, 'color', colorInput, colorHiddenInput);--}}
    {{--        colorDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // Handle Storage search--}}
    {{--    storageInput.addEventListener('input', () => {--}}
    {{--        const filter = storageInput.value.toLowerCase();--}}
    {{--        const filteredStorage = storageCapacities.filter(s => s.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(storageDropdown, filteredStorage, 'storage', storageInput, storageHiddenInput);--}}
    {{--        storageDropdown.classList.remove('hidden');--}}
    {{--    });--}}
    {{--    storageInput.addEventListener('focus', () => {--}}
    {{--        const filter = storageInput.value.toLowerCase();--}}
    {{--        const filteredStorage = storageCapacities.filter(s => s.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(storageDropdown, filteredStorage, 'storage', storageInput, storageHiddenInput);--}}
    {{--        storageDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // NEW: Accessory Category search logic--}}
    {{--    const accessoryCategoryInput = document.getElementById('accessory-category-input');--}}
    {{--    const accessoryCategoryIdHidden = document.getElementById('accessory-category-id');--}}
    {{--    const accessoryCategoryDropdown = document.getElementById('category-dropdown');--}}

    {{--    accessoryCategoryInput.addEventListener('input', () => {--}}
    {{--        const filter = accessoryCategoryInput.value.toLowerCase();--}}
    {{--        const filteredCategories = accessoryCategories.filter(c => c.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(accessoryCategoryDropdown, filteredCategories, 'category', accessoryCategoryInput, accessoryCategoryIdHidden, true);--}}
    {{--        accessoryCategoryDropdown.classList.remove('hidden');--}}
    {{--    });--}}
    {{--    accessoryCategoryInput.addEventListener('focus', () => {--}}
    {{--        const filter = accessoryCategoryInput.value.toLowerCase();--}}
    {{--        const filteredCategories = accessoryCategories.filter(c => c.name.toLowerCase().includes(filter));--}}
    {{--        renderDropdown(accessoryCategoryDropdown, filteredCategories, 'category', accessoryCategoryInput, accessoryCategoryIdHidden, true);--}}
    {{--        accessoryCategoryDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // Universal dropdown renderer--}}
    {{--    function renderDropdown(dropdownElement, data, type, inputElement, hiddenInputElement, isIdRequired = false) {--}}
    {{--        dropdownElement.innerHTML = '';--}}
    {{--        if (data.length === 0) {--}}
    {{--            const noResults = document.createElement('li');--}}
    {{--            noResults.className = 'px-3 py-2 text-gray-500';--}}
    {{--            noResults.textContent = `No ${type}s found.`;--}}
    {{--            dropdownElement.appendChild(noResults);--}}
    {{--        } else {--}}
    {{--            data.forEach(item => {--}}
    {{--                const listItem = document.createElement('li');--}}
    {{--                listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';--}}
    {{--                listItem.textContent = item.name;--}}
    {{--                listItem.dataset.value = item.name;--}}
    {{--                listItem.dataset.id = item.id; // Store the ID for categories and accessories--}}

    {{--                listItem.addEventListener('click', () => {--}}
    {{--                    inputElement.value = item.name;--}}
    {{--                    if (isIdRequired) {--}}
    {{--                        hiddenInputElement.value = item.id;--}}
    {{--                    } else {--}}
    {{--                        hiddenInputElement.value = item.name;--}}
    {{--                    }--}}
    {{--                    dropdownElement.classList.add('hidden');--}}
    {{--                });--}}
    {{--                dropdownElement.appendChild(listItem);--}}
    {{--            });--}}
    {{--        }--}}
    {{--    }--}}

    {{--    // Close dropdowns when clicking outside--}}
    {{--    document.addEventListener('click', (event) => {--}}
    {{--        if (!brandInput.parentElement.contains(event.target)) {--}}
    {{--            brandDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--        if (!modelInput.parentElement.contains(event.target)) {--}}
    {{--            modelDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--        if (!colorInput.parentElement.contains(event.target)) {--}}
    {{--            colorDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--        if (!storageInput.parentElement.contains(event.target)) {--}}
    {{--            storageDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--        // NEW: Close accessory category dropdown--}}
    {{--        if (!accessoryCategoryInput.parentElement.contains(event.target)) {--}}
    {{--            accessoryCategoryDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--        // NEW: Close accessory name dropdown--}}
    {{--        if (!accessoryNameInput.parentElement.contains(event.target)) {--}}
    {{--            accessoryNameDropdown.classList.add('hidden');--}}
    {{--        }--}}
    {{--    });--}}

    {{--    // --- Accessories Section (updated) -------}}
    {{--    const accessoryModal = document.getElementById('accessory-modal');--}}
    {{--    const addAccessoryBtn = document.getElementById('add-accessory-btn');--}}
    {{--    const closeAccessoryModalBtn = document.getElementById('close-accessory-modal-btn');--}}
    {{--    const accessoryForm = document.getElementById('accessory-form');--}}
    {{--    const accessoryList = document.getElementById('accessory-list');--}}

    {{--    // NEW: Accessory Name search logic--}}
    {{--    const accessoryNameInput = document.getElementById('modal-name');--}}
    {{--    const accessoryNameDropdown = document.getElementById('accessory-name-dropdown');--}}
    {{--    const selectedAccessoryIdInput = document.getElementById('selected-accessory-id');--}}

    {{--    accessoryNameInput.addEventListener('input', () => {--}}
    {{--        const filter = accessoryNameInput.value.toLowerCase();--}}
    {{--        const filteredAccessories = accessories.filter(acc => acc.name.toLowerCase().includes(filter));--}}
    {{--        renderAccessoryNameDropdown(filteredAccessories, accessoryNameDropdown);--}}
    {{--        accessoryNameDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    accessoryNameInput.addEventListener('focus', () => {--}}
    {{--        const filter = accessoryNameInput.value.toLowerCase();--}}
    {{--        const filteredAccessories = accessories.filter(acc => acc.name.toLowerCase().includes(filter));--}}
    {{--        renderAccessoryNameDropdown(filteredAccessories, accessoryNameDropdown);--}}
    {{--        accessoryNameDropdown.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    // Helper function to render the accessory name dropdown--}}
    {{--    function renderAccessoryNameDropdown(data, dropdownElement) {--}}
    {{--        dropdownElement.innerHTML = '';--}}
    {{--        if (data.length === 0) {--}}
    {{--            const noResults = document.createElement('li');--}}
    {{--            noResults.className = 'px-3 py-2 text-gray-500';--}}
    {{--            noResults.textContent = 'No existing accessories found. Create a new one.';--}}
    {{--            dropdownElement.appendChild(noResults);--}}
    {{--        } else {--}}
    {{--            data.forEach(item => {--}}
    {{--                const listItem = document.createElement('li');--}}
    {{--                listItem.className = 'cursor-pointer px-3 py-2 hover:bg-gray-100';--}}
    {{--                listItem.textContent = `${item.name} ($${parseFloat(item.selling_price).toFixed(2)})`;--}}
    {{--                listItem.addEventListener('click', () => {--}}
    {{--                    // Populate form fields with selected accessory's data--}}
    {{--                    accessoryNameInput.value = item.name;--}}
    {{--                    selectedAccessoryIdInput.value = item.id;--}}
    {{--                    document.getElementById('modal-purchase-price').value = item.purchase_price;--}}
    {{--                    document.getElementById('modal-selling-price').value = item.selling_price;--}}
    {{--                    document.getElementById('modal-unit').value = item.unit;--}}
    {{--                    // Pre-fill category if it exists--}}
    {{--                    if (item.category) {--}}
    {{--                        const category = accessoryCategories.find(c => c.id === item.category);--}}
    {{--                        if (category) {--}}
    {{--                            accessoryCategoryInput.value = category.name;--}}
    {{--                            accessoryCategoryIdHidden.value = category.id;--}}
    {{--                        }--}}
    {{--                    }--}}
    {{--                    dropdownElement.classList.add('hidden');--}}
    {{--                });--}}
    {{--                dropdownElement.appendChild(listItem);--}}
    {{--            });--}}
    {{--        }--}}
    {{--    }--}}

    {{--    addAccessoryBtn.addEventListener('click', () => {--}}
    {{--        // Reset all fields when opening modal for a new accessory--}}
    {{--        accessoryForm.reset();--}}
    {{--        selectedAccessoryIdInput.value = '';--}}
    {{--        document.getElementById('accessory-index').value = '';--}}
    {{--        accessoryModal.classList.remove('hidden');--}}
    {{--    });--}}

    {{--    closeAccessoryModalBtn.addEventListener('click', () => accessoryModal.classList.add('hidden'));--}}

    {{--    accessoryForm.addEventListener('submit', (e) => {--}}
    {{--        e.preventDefault();--}}
    {{--        const index = document.getElementById('accessory-index').value;--}}
    {{--        const name = document.getElementById('modal-name').value;--}}
    {{--        const existingAccessoryId = selectedAccessoryIdInput.value;--}}
    {{--        const categoryId = document.getElementById('accessory-category-id').value;--}}
    {{--        const categoryName = document.getElementById('accessory-category-input').value;--}}
    {{--        const barcode = document.getElementById('modal-barcode').value;--}}
    {{--        const unit = document.getElementById('modal-unit').value;--}}
    {{--        const purchasePrice = document.getElementById('modal-purchase-price').value;--}}
    {{--        const sellingPrice = document.getElementById('modal-selling-price').value;--}}
    {{--        const quantity = document.getElementById('modal-quantity').value;--}}

    {{--        if (!name || !quantity) {--}}
    {{--            showToast('Name and Quantity are required.', 'error');--}}
    {{--            return;--}}
    {{--        }--}}

    {{--        const accessory = {--}}
    {{--            // If an existing accessory was selected, use its ID. Otherwise, use a new counter.--}}
    {{--            id: existingAccessoryId || (index ? accessoriesData[index].id : accessoryCounter++),--}}
    {{--            name,--}}
    {{--            category_id: categoryId,--}}
    {{--            category_name: categoryName,--}}
    {{--            barcode,--}}
    {{--            unit,--}}
    {{--            purchase_price: purchasePrice,--}}
    {{--            selling_price: sellingPrice,--}}
    {{--            quantity--}}
    {{--        };--}}

    {{--        if (index) {--}}
    {{--            accessoriesData[index] = accessory;--}}
    {{--        } else {--}}
    {{--            accessoriesData.push(accessory);--}}
    {{--        }--}}
    {{--        renderAccessoryList();--}}
    {{--        accessoryModal.classList.add('hidden');--}}
    {{--    });--}}

    {{--    function renderAccessoryList() {--}}
    {{--        accessoryList.innerHTML = '';--}}
    {{--        accessoriesData.forEach((accessory, index) => {--}}
    {{--            const card = document.createElement('div');--}}
    {{--            card.className = 'flex items-center justify-between p-4 border rounded-lg bg-gray-50 shadow-sm';--}}
    {{--            card.innerHTML = `--}}
    {{--                <div>--}}
    {{--                    <span class="font-semibold text-gray-800">${accessory.name} - ${accessory.category_name}</span>--}}
    {{--                    <p class="text-sm text-gray-500">${accessory.quantity} ${accessory.unit}(s) @ $${parseFloat(accessory.selling_price).toFixed(2)}</p>--}}
    {{--                    ${accessory.barcode ? `<p class="text-xs text-gray-400">Barcode: ${accessory.barcode}</p>` : ''}--}}
    {{--                </div>--}}
    {{--                <div class="flex space-x-2">--}}
    {{--                    <button type="button" onclick="editAccessory(${index})" class="text-blue-500 hover:text-blue-700">Edit</button>--}}
    {{--                    <button type="button" onclick="removeAccessory(${index})" class="text-red-500 hover:text-red-700">Remove</button>--}}
    {{--                </div>--}}
    {{--                <input type="hidden" name="accessories[${index}][id]" value="${accessory.id}">--}}
    {{--                <input type="hidden" name="accessories[${index}][name]" value="${accessory.name}">--}}
    {{--                <input type="hidden" name="accessories[${index}][category_id]" value="${accessory.category_id}">--}}
    {{--                <input type="hidden" name="accessories[${index}][barcode]" value="${accessory.barcode}">--}}
    {{--                <input type="hidden" name="accessories[${index}][unit]" value="${accessory.unit}">--}}
    {{--                <input type="hidden" name="accessories[${index}][purchase_price]" value="${accessory.purchase_price}">--}}
    {{--                <input type="hidden" name="accessories[${index}][selling_price]" value="${accessory.selling_price}">--}}
    {{--                <input type="hidden" name="accessories[${index}][quantity]" value="${accessory.quantity}">--}}
    {{--            `;--}}
    {{--            accessoryList.appendChild(card);--}}
    {{--        });--}}
    {{--    }--}}

    {{--    window.editAccessory = (index) => {--}}
    {{--        const accessory = accessoriesData[index];--}}
    {{--        document.getElementById('modal-name').value = accessory.name;--}}
    {{--        selectedAccessoryIdInput.value = accessory.id;--}}

    {{--        accessoryCategoryInput.value = accessory.category_name;--}}
    {{--        accessoryCategoryIdHidden.value = accessory.category_id;--}}
    {{--        document.getElementById('modal-barcode').value = accessory.barcode;--}}
    {{--        document.getElementById('modal-unit').value = accessory.unit;--}}
    {{--        document.getElementById('modal-purchase-price').value = accessory.purchase_price;--}}
    {{--        document.getElementById('modal-selling-price').value = accessory.selling_price;--}}
    {{--        document.getElementById('modal-quantity').value = accessory.quantity;--}}

    {{--        document.getElementById('accessory-index').value = index;--}}
    {{--        accessoryModal.classList.remove('hidden');--}}
    {{--    };--}}

    {{--    // --- Toast and Confirmation Modals (existing logic) -----}}
    {{--    window.showToast = function(message, type = 'success') {--}}
    {{--        const toastContainer = document.getElementById('toast-container');--}}
    {{--        const toast = document.createElement('div');--}}
    {{--        toast.className = `bg-${type === 'success' ? 'green' : 'red'}-500 text-white px-4 py-3 rounded-xl shadow-lg mb-2 flex items-center justify-between`;--}}
    {{--        toast.innerHTML = `--}}
    {{--            <span>${message}</span>--}}
    {{--            <button class="ml-4 font-bold text-lg leading-none" onclick="this.parentElement.remove()">×</button>--}}
    {{--        `;--}}
    {{--        toastContainer.appendChild(toast);--}}
    {{--        setTimeout(() => toast.remove(), 5000);--}}
    {{--    };--}}

    {{--    window.removeAccessory = (index) => {--}}
    {{--        accessoriesData.splice(index, 1);--}}
    {{--        renderAccessoryList();--}}
    {{--    };--}}
    {{--});--}}



    {{--    </script>--}}
@endsection

