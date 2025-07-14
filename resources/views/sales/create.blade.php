@extends('layouts.app')

@section('title', 'Create New Sale')
@section('subtitle', 'Record a new sales transaction.')

@section('content')
    <div class="container mx-auto bg-white p-8 rounded-lg shadow-md">
        <img src="{{ asset('images/watermark.png') }}"
             alt="Watermark"
             class="pointer-events-none select-none absolute top-1/2 left-1/2 opacity-20 w-96 z-0"
             style="transform: translate(-50%, -60%);" />
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Create New Sale</h1>

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

        <form action="{{ route('sales.store') }}" method="POST">
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
            </div>
            <div>
                <label for="customer_email" class="block text-gray-700 text-sm font-bold mb-2">Customer Email (Optional):</label>
                <input type="email" name="customer_email" id="customer_email"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('customer_email') border-red-500 @enderror"
                       value="{{ old('customer_email') }}" placeholder="customer@example.com">
                @error('customer_email')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Phones to Sell (IMEI):</label>
                <div id="phone-imei-inputs">
                    <div class="phone-item-group">
                        <select name="phone_imeis[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a Phone (IMEI - Model - Color - Price)</option>
                            @foreach ($availablePhones as $phone)
                                <option value="{{ $phone->imei }}" data-price="{{ $phone->selling_price }}">
                                    {{ $phone->imei }} - {{ $phone->brand->name }} {{ $phone->model }} ({{ $phone->color }}) - ${{ number_format($phone->selling_price, 2) }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" onclick="addPhoneInput()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Add</button>
                    </div>
                    @error('phone_imeis')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                    @error('phone_imeis.*')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
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

            <div class="mb-6">
                {{-- Hidden input to ensure 'is_installment' is always sent, even when unchecked --}}
                <input type="hidden" name="is_installment" value="0">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_installment" id="is_installment" class="form-checkbox h-5 w-5 text-blue-600" value="1" {{ old('is_installment') ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700">Is Installment Sale?</span>
                </label>
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

            <div class="flex items-center justify-between">
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
        const availablePhones = @json($availablePhones);

        function addPhoneInput() {
            const container = document.getElementById('phone-imei-inputs');
            const div = document.createElement('div');
            div.className = 'phone-item-group';

            let optionsHtml = '<option value="">Select a Phone (IMEI - Model - Color - Price)</option>';
            availablePhones.forEach(phone => {
                optionsHtml += `<option value="${phone.imei}" data-price="${phone.selling_price}">
                    {{--${phone.imei} - ${phone.brand.name} ${phone.model} (${phone.color}) - ${{ number_format($phone->selling_price, 2) }}--}}
                ${phone.imei} - ${phone.brand.name} ${phone.model} (${phone.color}) - $${parseFloat(phone.selling_price).toFixed(2)}

                </option>`;
            });

            div.innerHTML = `
                <select name="phone_imeis[]" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    ${optionsHtml}
                </select>
                <button type="button" onclick="removePhoneInput(this)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full transition duration-300 ease-in-out shadow-md">Remove</button>
            `;
            container.appendChild(div);
        }

        function removePhoneInput(button) {
            button.closest('.phone-item-group').remove();
        }


        document.getElementById('is_installment').addEventListener('change', function() {
            const installmentDetails = document.getElementById('installment-details');
            if (this.checked) {
                installmentDetails.classList.remove('hidden');
            } else {
                installmentDetails.classList.add('hidden');
            }
        });
    </script>
@endsection
