<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barcodes</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
        }
        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        .barcode-item {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            /* This is crucial for printing, ensures the item doesn't get cut in half */
            page-break-inside: avoid;
        }
        .barcode-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .barcode-number {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #555;
            margin-top: 5px;
        }
        .barcode-svg svg {
            width: 100%;
            height: auto;
        }
    </style>
</head>
<body>

<div class="barcode-grid">
    @foreach($barcodes as $barcode)
        <div class="barcode-item">
            <p class="barcode-name">{{ $barcode['name'] }}</p>
            <div class="barcode-svg">
                <!-- The SVG is rendered directly from the data -->
                {!! $barcode['svg'] !!}
            </div>
            <p class="barcode-number">{{ $barcode['number'] }}</p>
        </div>
    @endforeach
</div>

</body>
</html>
