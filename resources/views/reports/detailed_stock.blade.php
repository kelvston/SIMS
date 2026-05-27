@extends('layouts.app')

@section('content')
    <div class="container mx-auto p-4 md:p-8">
        <h1 class="text-3xl font-bold mb-6 text-gray-800">Detailed Inventory</h1>
        <p class="text-gray-600 mb-6">
            This view allows you to see each unique batch of cosmetics and individual medicines for precise stock adjustments.
        </p>

        <!-- Navigation back to summary -->
        <div class="mb-6">
            <a href="{{ route('reports.stock') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back to Summary Report
            </a>
        </div>

        <!-- Detailed Cosmetics Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">In-Stock Cosmetics (by batch)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($detailedCosmeticStock as $cosmetic)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $cosmetic->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $cosmetic->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($cosmetic->purchase_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($cosmetic->selling_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="quantity-cosmetic-{{ $cosmetic->id }}">{{ number_format($cosmetic->quantity) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="showItemAdjustmentModal({{ $cosmetic->id }}, '{{ $cosmetic->name }}', '{{ $cosmetic->quantity }}', 'cosmetic')" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i> Adjust
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No in-stock cosmetics found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $detailedCosmeticStock->links() }}
            </div>
        </div>

        <!-- Detailed Medicines Table (New) -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">In-Stock Medicines (by BARCODE)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selling Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IMEI</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($detailedMedicineStock as $medicine)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $medicine->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $medicine->brand_name }} {{ $medicine->model }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($medicine->purchase_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($medicine->selling_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900" id="imei-medicine-{{ $medicine->id }}">{{ $medicine->imei }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex space-x-2">
                                <button onclick="showItemAdjustmentModal({{ $medicine->id }}, '{{ $medicine->brand_name }} {{ $medicine->model }}', '{{ $medicine->imei }}', 'medicine')" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-200">
                                    <i class="fas fa-edit mr-1"></i> Edit BARCODE
                                </button>
                                <button onclick="removeMedicineFromStock({{ $medicine->id }})" class="text-red-600 hover:text-red-900 transition-colors duration-200">
                                    <i class="fas fa-trash-alt mr-1"></i> Remove
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No in-stock medicines found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $detailedMedicineStock->links() }}
            </div>
        </div>
    </div>

    <!-- Stock Adjustment Modal -->
    <div id="stockAdjustmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center z-50">
        <div class="relative p-8 bg-white w-96 max-w-sm m-auto flex-col flex rounded-lg shadow-xl">
            <h3 class="text-xl font-semibold text-gray-900 mb-4" id="modalTitle"></h3>
            <form id="adjustmentForm" method="POST">
                @csrf
                <input type="hidden" name="item_id" id="itemId">
                <input type="hidden" name="item_type" id="itemType">
                <div class="mb-4">
                    <!-- This label and input will be dynamically updated by JavaScript -->
                    <label for="new_value" class="block text-sm font-medium text-gray-700" id="newValueLabel">New Value</label>
                    <input type="text" id="new_value" name="new_value" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="comment" class="block text-sm font-medium text-gray-700">Reason for Adjustment</label>
                    <textarea id="comment" name="comment" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g., changed IMEI due to typo, removed because of damage"></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus-ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>

    <script>
        function showItemAdjustmentModal(id, name, currentValue, itemType) {
            const modal = document.getElementById('stockAdjustmentModal');
            document.getElementById('modalTitle').innerText = `Adjusting Stock for: ${name}`;
            document.getElementById('itemId').value = id;
            document.getElementById('itemType').value = itemType;

            const newValueLabel = document.getElementById('newValueLabel');
            const newValueInput = document.getElementById('new_value');
            const adjustmentForm = document.getElementById('adjustmentForm');

            // Dynamically set label, input type, and form action
            if (itemType === 'cosmetic') {
                newValueLabel.innerText = 'New Quantity';
                newValueInput.type = 'number';
                newValueInput.value = currentValue;
                adjustmentForm.action = `/reports/stock/update-cosmetic/${id}`;
            } else if (itemType === 'medicine') {
                newValueLabel.innerText = 'New IMEI';
                newValueInput.type = 'text';
                newValueInput.value = currentValue;
                adjustmentForm.action = `/reports/stock/update-medicine-imei/${id}`;
            }

            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('stockAdjustmentModal');
            modal.classList.add('hidden');
        }

        async function removeMedicineFromStock(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this! Please provide a reason for removal.",
                icon: 'warning',
                input: 'textarea',
                inputPlaceholder: 'Type your reason here...',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, remove it!',
                inputValidator: (value) => {
                    if (!value) {
                        return 'You need to write a reason to proceed!';
                    }
                }
            }).then(async (result) => {
                if (result.isConfirmed) {
                    const comment = result.value;
                    try {
                        const response = await fetch(`/reports/stock/remove-medicine/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                            },
                            body: JSON.stringify({ comment: comment })
                        });

                        const result = await response.json();

                        if (response.ok) {
                            Swal.fire(
                                'Removed!',
                                'The medicine has been removed from stock.',
                                'success'
                            );
                            // Find the row by its unique ID and remove it from the table
                            const row = document.getElementById(`imei-medicine-${id}`).closest('tr');
                            row.remove();
                        } else {
                            Swal.fire(
                                'Error!',
                                `Error: ${result.message || 'An unexpected error occurred.'}`,
                                'error'
                            );
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire(
                            'Error!',
                            'An error occurred. Please try again.',
                            'error'
                        );
                    }
                }
            });
        }

        document.getElementById('adjustmentForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);
            const id = formData.get('item_id');
            const itemType = formData.get('item_type');
            const newValue = formData.get('new_value');
            const comment = formData.get('comment');
console.log(1);
            // Dynamically get the element ID to update
            const elementId = itemType === 'cosmetic' ? `quantity-cosmetic-${id}` : `imei-medicine-${id}`;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: JSON.stringify({
                        value: newValue,
                        comment: comment
                    })
                });

                const result = await response.json();

                if (response.ok) {
                    Swal.fire(
                        'Success!',
                        'Stock updated successfully!',
                        'success'
                    );
                    document.getElementById(elementId).innerText = newValue;
                    closeModal();
                } else {
                    Swal.fire(
                        'Error!',
                        `Error: ${result.message || 'An unexpected error occurred.'}`,
                        'error'
                    );
                }
            } catch (error) {
                Swal.fire(
                    'Error!',
                    'An error occurred. Please try again.',
                    'error'
                );
            }
        });
    </script>
@endsection
