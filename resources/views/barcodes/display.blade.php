<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generated Barcodes</title>
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.min.css') }}">
    <style>
        /* This is the key to hiding the buttons on print */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            /* The !important rule forces the browser to hide this element */
            .no-print {
                display: none !important;
            }
            .barcode-grid {
                /* A tighter grid for printing more barcodes per page */
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 2px !important;
            }
            .barcode-item {
                border: 1px solid #ccc;
                padding: 2px;
                /* This is crucial to prevent barcodes from being split across pages */
                page-break-inside: avoid;
            }
            .barcode-svg {
                /* Ensure the SVG itself is small */
                width: 70px !important;
                height: 30px !important;
                /* Align the barcode itself to the start of its container */
                display: flex;
                justify-content: flex-start;
                align-items: center;
            }
        }
        /* Custom font size smaller than Tailwind's 'text-xs' */
        .text-xxs {
            font-size: 0.625rem; /* 10px */
        }
    </style>
</head>
<body class="bg-gray-100 flex flex-col items-center p-8 font-sans">

<!-- The entire div containing the buttons now has the 'no-print' class -->
<div class="no-print mb-8 w-full max-w-4xl flex justify-between items-center">
    <!-- Reduced the header font size from 'text-3xl' to 'text-2xl' -->
    <h1 class="text-2xl font-extrabold text-gray-900">Generated Barcodes</h1>
    <div class="flex space-x-4">
        <!-- Button to trigger the browser's print dialog -->
        <button onclick="window.print()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-200 ease-in-out">
            Print
        </button>
    </div>
</div>

<!-- Barcode display grid -->
<!-- The gap is now smaller and the columns are more compact -->
<div class="barcode-grid grid grid-cols-3 md:grid-cols-5 lg:grid-cols-6 gap-1 w-full max-w-4xl">
    @foreach($barcodes as $barcode)
        <!-- I've removed the 'rounded-lg' and 'shadow' classes and made padding smaller -->
        <div class="barcode-item bg-white p-1 flex flex-col items-start justify-center text-left">
            <!-- Now using the custom '.text-xxs' class for a smaller font size -->
            <p class="text-xxs font-semibold text-gray-800 mb-0.5">{{ $barcode['name'] }}</p>
            <!-- Reduced margin -->
            <div class="barcode-svg mb-0.5 w-20 h-8">
                {!! $barcode['svg'] !!}
            </div>
            <!-- Now using the custom '.text-xxs' class for a smaller font size -->
            <p class="text-xxs font-mono text-gray-600 mb-0">{{ $barcode['number'] }}</p>
        </div>
    @endforeach
</div>

</body>
</html>
