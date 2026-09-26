@extends('layouts.app')

@section('title', 'Create New Sale')
@section('subtitle', 'Record a new sales transaction.')

@section('content')
    <style>
        .sale-search-wrap { position: relative; }
        .sale-search-results {
            position: absolute;
            z-index: 40;
            width: 100%;
            max-height: 260px;
            overflow-y: auto;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            box-shadow: 0 12px 20px -12px rgba(15, 23, 42, 0.35);
            margin-top: 0.25rem;
        }
        .sale-search-option {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.65rem 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }
        .sale-search-option:last-child { border-bottom: 0; }
        .sale-search-option:hover, .sale-search-option.active { background: #eff6ff; }
        .sale-search-empty {
            padding: 0.75rem;
            color: #6b7280;
            font-size: 0.875rem;
        }
        @media (max-width: 639px) {
            .sale-form-shell {
                padding: 1rem !important;
                border-radius: 0.5rem;
            }
            .sale-form-title {
                font-size: 1.5rem;
                line-height: 2rem;
            }
            .sale-search-option {
                display: block;
            }
            .sale-search-option > span:last-child {
                display: block;
                margin-top: 0.25rem;
            }
            .sale-summary-row {
                align-items: flex-start;
                gap: 0.75rem;
            }
            .sale-submit-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.75rem;
            }
            .sale-submit-actions > * {
                width: 100%;
                text-align: center;
            }
            .sale-empty-row td {
                display: block !important;
            }
            .sale-empty-row td::before {
                content: none !important;
            }
        }
    </style>

    <div class="sale-form-shell container mx-auto bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-md relative overflow-hidden">
        <img src="{{ asset('images/spare.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-64 sm:w-96 z-0"
             style="transform: translate(-50%, -60%);" />
        <h1 class="sale-form-title text-2xl sm:text-3xl font-bold text-gray-800 mb-6 text-center relative z-10">Create New Sale</h1>

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

        <form action="{{ route('sales.store') }}" method="POST" id="sale-form" class="relative z-10">
            @csrf

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

                <div class="md:col-span-2">
                    <label for="customer_email" class="block text-gray-700 text-sm font-bold mb-2">Customer Email (Optional):</label>
                    <input type="email" name="customer_email" id="customer_email"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_email') border-red-500 @enderror"
                           value="{{ old('customer_email') }}" placeholder="customer@example.com">
                    @error('customer_email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <div class="border rounded-lg p-4 bg-gray-50">
                    <label for="phone-search"
                           class="block text-gray-700 text-sm font-bold mb-2">
                        Search Phone:
                    </label>

                    {{-- Step 1: Search --}}
                    <div class="sale-search-wrap">
                        <input type="text"
                               id="phone-search"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                               placeholder="Search phone name or model..."
                               autocomplete="off">

                        <div id="phone-results"
                             class="sale-search-results hidden">
                        </div>
                    </div>

                    {{-- Step 2 + 3: Selected product + quantity --}}
                    <div id="phone-quantity-box"
                         class="hidden mt-3 border rounded-lg bg-white p-3">

                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">

                            <div class="flex-1">
                                <p class="text-sm text-gray-500">
                                    Selected Phone
                                </p>

                                <p id="selected-phone-name"
                                   class="font-semibold text-gray-800">
                                </p>

                                <p id="selected-phone-price"
                                   class="text-sm text-gray-600">
                                </p>
                            </div>

                            <div class="flex items-center gap-2">
                                <label for="phone-qty"
                                       class="text-sm font-semibold text-gray-700">
                                    Quantity
                                </label>

                                <input type="number"
                                       id="phone-qty"
                                       min="1"
                                       step="1"
                                       value="1"
                                       class="w-24 shadow appearance-none border rounded py-2 px-3 text-gray-700 text-center focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       autocomplete="off">
                            </div>

                            <button type="button"
                                    id="add-phone-button"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg">
                                Add
                            </button>

                        </div>
                    </div>

                    <p id="phone-message"
                       class="text-sm mt-2">
                    </p>
                </div>
            </div>

            <div class="mb-6 border rounded-lg overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 flex items-center justify-between gap-3">
                    <h2 class="font-semibold text-gray-800">Selected Items</h2>
                    <span id="selected-count" class="text-sm text-gray-600 whitespace-nowrap">0 items</span>
                </div>
                <div class="table-scroll">
                    <table class="responsive-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                        </thead>
                        <tbody id="selected-items-body" class="bg-white divide-y divide-gray-200">
                        <tr id="selected-empty-row" class="sale-empty-row">
                            <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">No item selected yet.</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="sale-hidden-inputs"></div>

            <div class="bg-gray-50 border rounded-lg p-4 mb-6">
                <div class="sale-summary-row flex justify-between mb-2">
                    <span class="font-semibold text-gray-700">Subtotal</span>
                    <span id="sale-subtotal" class="whitespace-nowrap">$0.00</span>
                </div>
                <div class="sale-summary-row flex justify-between mb-2">
                    <span class="font-semibold text-gray-700">Discount</span>
                    <span id="sale-discount" class="whitespace-nowrap">$0.00</span>
                </div>
                <div class="sale-summary-row flex justify-between text-lg font-bold text-gray-900">
                    <span>Final Total</span>
                    <span id="sale-final-total" class="whitespace-nowrap">$0.00</span>
                </div>
                <p id="sale-total-warning" class="text-sm text-red-600 mt-2 hidden">Discount cannot exceed subtotal.</p>

            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="discount_amount" class="block text-gray-700 text-sm font-bold mb-2">Discount Amount (Tsh):</label>
                    <input type="number" step="0.01" name="discount_amount" id="discount_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('discount_amount') border-red-500 @enderror" value="{{ old('discount_amount', 0) }}" min="0">
                    @error('discount_amount')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            @php($selectedPaymentOption = old('payment_option', old('is_installment') ? 'installment' : 'cash'))
            <div class="mb-6">
                <input type="hidden" name="is_installment" id="is_installment" value="{{ $selectedPaymentOption === 'installment' ? 1 : 0 }}">
                <label for="payment_option" class="block text-gray-700 text-sm font-bold mb-2">Payment Type:</label>
                <select name="payment_option" id="payment_option"
                        class="shadow appearance-none border rounded w-full lg:w-1/2 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('payment_option') border-red-500 @enderror">
                    <option value="cash" {{ $selectedPaymentOption === 'cash' ? 'selected' : '' }}>Normal Sale / Cash</option>
                    <option value="installment" {{ $selectedPaymentOption === 'installment' ? 'selected' : '' }}>Sale by Installment</option>
                    <option value="credit" {{ $selectedPaymentOption === 'credit' ? 'selected' : '' }}>Credit Sale</option>
                </select>
                @error('payment_option')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div id="installment-details" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 {{ $selectedPaymentOption === 'installment' ? '' : 'hidden' }}">
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

            <div id="credit-details" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6 {{ $selectedPaymentOption === 'credit' ? '' : 'hidden' }}">
                <div>
                    <label for="credit_due_date" class="block text-gray-700 text-sm font-bold mb-2">Credit Due Date:</label>
                    <input type="date" name="credit_due_date" id="credit_due_date"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('credit_due_date') border-red-500 @enderror"
                           value="{{ old('credit_due_date') }}">
                    @error('credit_due_date')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="credit_paid_amount" class="block text-gray-700 text-sm font-bold mb-2">Paid Now (Tsh):</label>
                    <input type="number" step="0.01" name="credit_paid_amount" id="credit_paid_amount"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('credit_paid_amount') border-red-500 @enderror"
                           value="{{ old('credit_paid_amount', 0) }}" min="0">
                    @error('credit_paid_amount')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="credit_reminder_days" class="block text-gray-700 text-sm font-bold mb-2">Remind Days Before:</label>
                    <input type="number" name="credit_reminder_days" id="credit_reminder_days"
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('credit_reminder_days') border-red-500 @enderror"
                           value="{{ old('credit_reminder_days', 3) }}" min="0" max="30">
                    @error('credit_reminder_days')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="sale-submit-actions flex items-center justify-between">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded-full focus:outline-none focus:shadow-outline transition duration-300 ease-in-out shadow-lg">
                    Record Sale
                </button>
                <a href="{{ route('sales.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800 py-2">
                    View All Sales
                </a>
            </div>
        </form>
    </div>

    <script>
        const availablePhones = @json($availablePhones);

        const selectedPhones = new Map();
        let pendingPhone = null;

        function moneyToCents(value) {
            return Math.round((parseFloat(value) || 0) * 100);
        }

        function formatMoney(cents) {
            return `Tsh ${(cents / 100).toFixed(2)}`;
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value ?? '');
            return div.innerHTML;
        }

        function phoneLabel(phone) {
            return `${(phone.brand && phone.brand.name) || 'N/A'} ${phone.model}`;
        }

        function phoneSearchText(phone) {
            return `${(phone.brand && phone.brand.name) || ''} ${phone.model}`;
        }

        function phoneAvailableQuantity(phone) {
            return Math.floor(
                Number(
                    phone.available_quantity ??
                    phone.quantity ??
                    phone.stock_quantity ??
                    0
                )
            );
        }

        function closeSearchResults() {
            const results = document.getElementById('phone-results');

            results.classList.add('hidden');
            results.innerHTML = '';
        }

        function setMessage(message, isError = false) {
            const element = document.getElementById('phone-message');

            element.textContent = message;
            element.className = `text-sm mt-2 ${
                isError ? 'text-red-600' : 'text-green-600'
            }`;
        }

        function searchPhones(query) {
            const searchValue = String(query || '').trim().toLowerCase();

            const matches = availablePhones
                .filter(phone => {
                    const productId = String(phone.brand_id);

                    return !selectedPhones.has(productId);
                })
                .filter(phone => {
                    if (!searchValue) {
                        return true;
                    }

                    return phoneSearchText(phone)
                        .toLowerCase()
                        .includes(searchValue);
                })
                .slice(0, 20);

            const container = document.getElementById('phone-results');

            container.innerHTML = '';

            if (!matches.length) {
                container.innerHTML = `
            <div class="sale-search-empty">
                No available phone found.
            </div>
        `;

                container.classList.remove('hidden');
                return;
            }

            matches.forEach(phone => {
                const option = document.createElement('div');

                option.className = 'sale-search-option';

                option.innerHTML = `
            <span>
                <span class="block font-semibold text-gray-800">
                    ${escapeHtml(phoneLabel(phone))}
                </span>

                <span class="block text-xs text-gray-500">
                    ${escapeHtml(phone.color || '')}
                    ${phone.storage_capacity ? ' - ' + escapeHtml(phone.storage_capacity) : ''}
                </span>
            </span>

            <span class="text-sm font-semibold text-gray-800">
                ${formatMoney(moneyToCents(phone.selling_price))}
            </span>
        `;

                // Step 1 -> Step 2: selecting a result no longer adds it.
                // It just loads it into the quantity box and waits for "Add".
                option.addEventListener('mousedown', function(event) {
                    event.preventDefault();

                    selectPhoneForQuantity(phone);

                    closeSearchResults();
                });

                container.appendChild(option);
            });

            container.classList.remove('hidden');
        }

        // Step 2: show the selected phone + quantity box, don't touch the table yet.
        function selectPhoneForQuantity(phone) {
            pendingPhone = phone;

            const availableQuantity = phoneAvailableQuantity(phone);

            document.getElementById('selected-phone-name').textContent =
                phoneLabel(phone);

            document.getElementById('selected-phone-price').textContent =
                `${formatMoney(moneyToCents(phone.selling_price))}` +
                (availableQuantity > 0 ? ` · ${availableQuantity} in stock` : '');

            const qtyInput = document.getElementById('phone-qty');

            qtyInput.value = 1;

            if (availableQuantity > 0) {
                qtyInput.setAttribute('max', availableQuantity);
            } else {
                qtyInput.removeAttribute('max');
            }

            document.getElementById('phone-search').value = phoneLabel(phone);
            document.getElementById('phone-quantity-box').classList.remove('hidden');

            setMessage('');
            qtyInput.focus();
        }

        function clearPendingSelection() {
            pendingPhone = null;

            document.getElementById('phone-quantity-box').classList.add('hidden');
            document.getElementById('selected-phone-name').textContent = '';
            document.getElementById('selected-phone-price').textContent = '';
            document.getElementById('phone-search').value = '';
        }

        // Step 3: only now does the phone go into the selected-items table.
        function addPhoneToSale(phone, quantity) {
            const productId = String(phone.brand_id);

            let normalizedQuantity = Math.max(1, parseInt(quantity, 10) || 1);

            const availableQuantity = phoneAvailableQuantity(phone);

            if (availableQuantity > 0 && normalizedQuantity > availableQuantity) {
                setMessage(
                    `Only ${availableQuantity} unit(s) of ${phoneLabel(phone)} are available.`,
                    true
                );

                return;
            }

            selectedPhones.set(productId, {
                item: phone,
                quantity: normalizedQuantity
            });

            setMessage(`Added ${phoneLabel(phone)} x ${normalizedQuantity}.`);

            clearPendingSelection();
            renderSelectedItems();
        }

        document.getElementById('add-phone-button')
            .addEventListener('click', function() {
                if (!pendingPhone) {
                    setMessage('Search and select a phone first.', true);
                    return;
                }

                const qtyInput = document.getElementById('phone-qty');

                addPhoneToSale(pendingPhone, qtyInput.value);
            });

        function updatePhoneQuantity(productId, quantity) {
            const line = selectedPhones.get(String(productId));

            if (!line) {
                return;
            }

            const availableQuantity = phoneAvailableQuantity(line.item);

            let nextQuantity = parseInt(quantity, 10) || 1;

            nextQuantity = Math.max(1, nextQuantity);

            if (availableQuantity > 0) {
                nextQuantity = Math.min(nextQuantity, availableQuantity);
            }

            line.quantity = nextQuantity;

            selectedPhones.set(String(productId), line);

            renderSelectedItems();
        }

        function removePhone(productId) {
            selectedPhones.delete(String(productId));

            renderSelectedItems();
        }

        function renderSelectedItems() {
            const body = document.getElementById('selected-items-body');
            const hiddenInputs = document.getElementById('sale-hidden-inputs');

            body.innerHTML = '';
            hiddenInputs.innerHTML = '';

            let subtotalCents = 0;
            let itemCount = 0;
            let phoneIndex = 0;

            selectedPhones.forEach((line, productId) => {
                const phone = line.item;
                const quantity = line.quantity;

                const unitPriceCents = moneyToCents(phone.selling_price);
                const totalCents = unitPriceCents * quantity;

                subtotalCents += totalCents;
                itemCount += quantity;

                hiddenInputs.insertAdjacentHTML(
                    'beforeend',
                    `
            <input type="hidden"
                   name="phones[${phoneIndex}][product_id]"
                   value="${escapeHtml(productId)}">

            <input type="hidden"
                   name="phones[${phoneIndex}][quantity]"
                   value="${quantity}">
            `
                );

                body.insertAdjacentHTML(
                    'beforeend',
                    `
            <tr>
                <td data-label="Item"
                    class="px-4 py-3 text-sm text-gray-900">
                    ${escapeHtml(phoneLabel(phone))}
                </td>

                <td data-label="Type"
                    class="px-4 py-3 text-sm text-gray-700">
                    Phone
                </td>

                <td data-label="Qty"
                    class="px-4 py-3 text-sm text-gray-900 text-right">
                    <input
                        type="number"
                        min="1"
                        ${availableQuantityAttribute(phone)}
                        value="${quantity}"
                        onchange="updatePhoneQuantity('${escapeHtml(productId)}', this.value)"
                        class="w-20 text-right border rounded py-1 px-2"
                    >
                </td>

                <td data-label="Unit Price"
                    class="px-4 py-3 text-sm text-gray-900 text-right">
                    ${formatMoney(unitPriceCents)}
                </td>

                <td data-label="Total"
                    class="px-4 py-3 text-sm text-gray-900 text-right">
                    ${formatMoney(totalCents)}
                </td>

                <td data-label="Action"
                    class="px-4 py-3 text-sm text-right">
                    <button
                        type="button"
                        onclick="removePhone('${escapeHtml(productId)}')"
                        class="text-red-600 hover:text-red-800 font-semibold">
                        Remove
                    </button>
                </td>
            </tr>
            `
                );

                phoneIndex++;
            });

            if (!selectedPhones.size) {
                body.innerHTML = `
            <tr id="selected-empty-row" class="sale-empty-row">
                <td colspan="6"
                    class="px-4 py-6 text-center text-sm text-gray-500">
                    No phone selected yet.
                </td>
            </tr>
        `;
            }

            const discountCents = moneyToCents(
                document.getElementById('discount_amount').value
            );

            const finalCents = subtotalCents - discountCents;

            document.getElementById('selected-count').textContent =
                `${itemCount} phone${itemCount === 1 ? '' : 's'}`;

            document.getElementById('sale-subtotal').textContent =
                formatMoney(subtotalCents);

            document.getElementById('sale-discount').textContent =
                formatMoney(discountCents);

            document.getElementById('sale-final-total').textContent =
                formatMoney(Math.max(finalCents, 0));

            document.getElementById('sale-total-warning')
                .classList.toggle('hidden', finalCents >= 0);
        }

        function availableQuantityAttribute(phone) {
            const availableQuantity = phoneAvailableQuantity(phone);

            return availableQuantity > 0
                ? `max="${availableQuantity}"`
                : '';
        }

        document.getElementById('phone-search')
            .addEventListener('input', function() {
                // Typing again after a selection cancels the pending pick.
                if (pendingPhone) {
                    clearPendingSelection();
                }

                searchPhones(this.value);
            });

        document.getElementById('phone-search')
            .addEventListener('focus', function() {
                if (!pendingPhone) {
                    searchPhones(this.value);
                }
            });

        document.getElementById('phone-search')
            .addEventListener('keydown', function(event) {
                if (event.key !== 'Enter') {
                    return;
                }

                event.preventDefault();

                const query = this.value.trim();

                if (!query) {
                    setMessage('Enter a phone name or model.', true);
                    return;
                }

                const phone = availablePhones.find(item => {
                    const productId = String(item.id);

                    return !selectedPhones.has(productId) &&
                        phoneSearchText(item)
                            .toLowerCase()
                            .includes(query.toLowerCase());
                });

                if (!phone) {
                    setMessage('No available phone found.', true);
                    return;
                }

                selectPhoneForQuantity(phone);
                closeSearchResults();
            });

        document.addEventListener('mousedown', function(event) {
            if (!event.target.closest('.sale-search-wrap')) {
                closeSearchResults();
            }
        });

        document.getElementById('discount_amount')
            .addEventListener('input', renderSelectedItems);

        document.getElementById('payment_option')
            .addEventListener('change', function() {
                const isInstallment = this.value === 'installment';
                const isCredit = this.value === 'credit';

                document.getElementById('is_installment').value =
                    isInstallment ? '1' : '0';

                document.getElementById('installment-details')
                    .classList.toggle('hidden', !isInstallment);

                document.getElementById('credit-details')
                    .classList.toggle('hidden', !isCredit);
            });

        document.getElementById('sale-form')
            .addEventListener('submit', function(event) {
                if (!selectedPhones.size) {
                    event.preventDefault();

                    setMessage(
                        'Add at least one phone before recording the sale.',
                        true
                    );
                }
            });

        renderSelectedItems();
    </script>
@endsection
