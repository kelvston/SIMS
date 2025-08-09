@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Sales Return</h1>
        <p class="text-gray-600 mb-6">
            Search for a previous sale to process a return and adjust stock accordingly.
        </p>

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Find a Sale</h2>
            <form id="searchForm" class="flex flex-col md:flex-row md:space-x-4">
                @csrf
                <div class="flex-grow mb-4 md:mb-0">
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
                    <input type="text" id="invoice_number" name="invoice_number" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., INV-00123" required>
                </div>
                <div class="flex-grow mb-4 md:mb-0">
                    <label for="customer_phone" class="block text-sm font-medium text-gray-700">Customer Phone (Optional)</label>
                    <input type="text" id="customer_phone" name="customer_phone" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., +255712345678">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <i class="fas fa-search mr-2"></i> Search
                    </button>
                </div>
            </form>
        </div>

        <!-- Sales Details and Return Form (hidden by default) -->
        <div id="saleDetailsSection" class="hidden">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex justify-between items-center border-b pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Sale Details</h2>
                    <div id="sale-info" class="text-gray-600 text-sm"></div>
                </div>

                <form id="returnForm">
                    @csrf
                    <input type="hidden" name="sale_id" id="saleIdInput">
                    <div id="saleItemsContainer">
                        <!-- Items will be dynamically loaded here -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="itemsGrid">
                            <!-- Items will be dynamically loaded here -->
                        </div>
                    </div>

                    <div class="mt-8">
                        <label for="return_reason" class="block text-sm font-medium text-gray-700">Reason for Return</label>
                        <textarea id="return_reason" name="return_reason" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., sold wrong item, customer changed mind" required></textarea>
                    </div>

                    <div class="flex justify-end mt-6">
                        <button type="submit" class="px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                            <i class="fas fa-undo mr-2"></i> Process Return
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 and Font Awesome -->
{{--    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>--}}
{{--    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" xintegrity="sha512-iecdLmaskl7CV0A5B7P4o+1241yC+x4f7A+w5+I8+4q5+4Q0+eC4p8A+a0b9x8yT" crossorigin="anonymous" referrerpolicy="no-referrer" />--}}
    <!-- Offline SweetAlert2 -->
    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">

    <script>
        document.getElementById('searchForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const invoiceNumber = formData.get('invoice_number');
            const customerPhone = formData.get('customer_phone');

            try {
                const response = await fetch(`/sales/search`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        invoice_number: invoiceNumber,
                        customer_phone: customerPhone
                    })
                });

                // Check if the response was successful (HTTP status 200-299)
                if (!response.ok) {
                    const errorResult = await response.json();
                    throw new Error(errorResult.message || 'An error occurred during search.');
                }

                const result = await response.json();
                displaySaleDetails(result.sale, invoiceNumber); // Pass the invoice number to the display function

                Swal.fire('Success!', result.message, 'success');

            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', error.message || 'An unexpected error occurred. Please try again.', 'error');
                document.getElementById('saleDetailsSection').classList.add('hidden');
            }
        });

        /**
         * Dynamically displays the sale details and populates the return form.
         * @param {object} sale - The sale object from the API.
         * @param {string} invoiceNumber - The invoice number used for the search.
         */
        function displaySaleDetails(sale, invoiceNumber) {
            const saleDetailsSection = document.getElementById('saleDetailsSection');
            const itemsGrid = document.getElementById('itemsGrid');
            const saleInfo = document.getElementById('sale-info');

            // Populate the sale information section
            saleInfo.innerHTML = `
            <p><strong>Invoice:</strong> ${invoiceNumber}</p>
            <p><strong>Customer:</strong> ${sale.customer_name}</p>
            <p><strong>Date:</strong> ${new Date(sale.created_at).toLocaleDateString()}</p>
        `;

            itemsGrid.innerHTML = '';

            // CORRECTED: Use 'sale_items' instead of 'items' to match the JSON response.
            if (sale.sale_items && sale.sale_items.length > 0) {
                sale.sale_items.forEach(item => {
                    const itemName = item.phone ? `${item.phone.model} (IMEI: ${item.phone.imei})` : item.accessory.name;
                    const quantity = item.quantity ? `Quantity: ${item.quantity}` : '';

                    itemsGrid.innerHTML += `
                    <div class="bg-gray-100 p-4 rounded-lg shadow-sm flex items-center space-x-4">
                        <input type="checkbox" name="returned_items[]" value="${item.id}" class="form-checkbox h-5 w-5 text-red-600 rounded">
                        <div class="flex-grow">
                            <p class="font-medium text-gray-800">${itemName}</p>
                            <p class="text-sm text-gray-500">${quantity}</p>
                        </div>
                    </div>
                `;
                });
            } else {
                itemsGrid.innerHTML = '<p class="text-gray-500">No items found for this sale.</p>';
            }

            saleDetailsSection.classList.remove('hidden');
            document.getElementById('saleIdInput').value = sale.id;
        }

        document.getElementById('returnForm').addEventListener('submit', async function(event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const saleId = formData.get('sale_id');
            const returnedItems = Array.from(document.querySelectorAll('input[name="returned_items[]"]:checked')).map(el => el.value);
            const returnReason = formData.get('return_reason');

            if (returnedItems.length === 0) {
                Swal.fire('Warning!', 'Please select at least one item to return.', 'warning');
                return;
            }

            try {
                const response = await fetch(`/sales/return/${saleId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        items: returnedItems,
                        reason: returnReason
                    })
                });

                if (!response.ok) {
                    const errorResult = await response.json();
                    throw new Error(errorResult.message || 'An error occurred while processing the return.');
                }

                const result = await response.json();
                Swal.fire('Return Processed!', result.message, 'success');

                // Reset the form and hide the details section
                form.reset();
                document.getElementById('saleDetailsSection').classList.add('hidden');

            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error!', error.message || 'An unexpected error occurred. Please try again.', 'error');
            }
        });
    </script>

@endsection
