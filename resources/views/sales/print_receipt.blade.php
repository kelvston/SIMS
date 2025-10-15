<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
{{--    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">--}}
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.min.css') }}">

    <style>
        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
        /* Custom styles for a polished logo */
        .logo-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .logo {
            max-height: 80px;
            max-width: 150px;
            opacity: 0.9;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
            border-radius: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen p-4">
<div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-2xl">
    <div class="text-center mb-6">
        @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])
            <div class="logo-container">
                <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}" alt="Organization Logo" class="logo">
            </div>
        @endif
        <h2 class="text-2xl font-bold text-gray-800">Sales Receipt</h2>
        <p class="text-sm text-gray-500">Receipt No: {{ $receipt->receipt_number }}</p>
    </div>

    {{-- Organization Details --}}
    <div class="border-t border-b border-gray-200 py-4 mb-4">
        <div class="text-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">{{ $settings['organization_name'] ?? 'Your Organization Name' }}</h3>
            <p class="text-sm text-gray-600">{{ $settings['organization_address'] ?? '123 Main Street, City, ZIP' }}</p>
            <p class="text-sm text-gray-600">Phone: {{ $settings['organization_phone'] ?? 'N/A' }} | Email: {{ $settings['organization_email'] ?? 'N/A' }}</p>
        </div>
        <div class="flex justify-between text-sm">
            <p class="font-semibold text-gray-700">Date Issued:</p>
            <p>{{ $receipt->issued_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex justify-between text-sm">
            <p class="font-semibold text-gray-700">Customer Name:</p>
            <p>{{ $receipt->sale->customer_name ?? 'N/A' }}</p>
        </div>
        <div class="flex justify-between text-sm">
            <p class="font-semibold text-gray-700">Customer Phone:</p>
            <p>{{ $receipt->sale->customer_phone ?? 'N/A' }}</p>
        </div>
    </div>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">Items</h3>
    <div class="space-y-2 mb-4">
        @foreach($receipt->sale->saleItems as $item)
            <div class="flex justify-between text-sm">
                @if ($item->medicine)
                    <p class="text-gray-700">Medicine:  (BARCODE: {{ $item->medicine->barcode }})</p>
                @else
                    <p class="text-gray-700">Cosmetic: {{ $item->cosmetic->name }}</p>
                @endif
                <p class="text-gray-700">x{{ $item->quantity }} @ {{ number_format($item->unit_price, 2) }}</p>
            </div>
        @endforeach
    </div>
    <div class="border-t border-gray-200 pt-4">
        <div class="flex justify-between text-sm mb-1">
            <p class="text-gray-600">Subtotal</p>
            <p class="font-semibold">{{ number_format($receipt->subtotal, 2) }}</p>
        </div>
        <div class="flex justify-between text-sm mb-1">
            <p class="text-gray-600">Discount</p>
            <p class="font-semibold">-{{ number_format($receipt->discount, 2) }}</p>
        </div>
        <div class="flex justify-between text-lg font-bold text-gray-800 border-t border-gray-300 pt-2 mt-2">
            <p>Total</p>
            <p>{{ number_format($receipt->total, 2) }}</p>
        </div>
    </div>
</div>
<script>
    window.onload = function () {
        window.print();
        window.onafterprint = function () {
            window.close();
        };
    };
</script>


</body>
</html>
