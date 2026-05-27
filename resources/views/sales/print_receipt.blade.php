<!DOCTYPE html>
<html>
<head>
    <title>Receipt #{{ $receipt->receipt_number }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('assets/css/tailwind.min.css') }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: #f3f4f6;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 2rem 1rem;
            font-family: 'Courier New', Courier, monospace;
        }

        .receipt {
            background: #fff;
            width: 100%;
            max-width: 360px;
            padding: 1.5rem 1.25rem;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }

        .receipt-header .logo {
            max-height: 60px;
            max-width: 120px;
            margin-bottom: 0.5rem;
        }

        .receipt-header h2 {
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .receipt-header p {
            font-size: 0.75rem;
            color: #555;
            margin-top: 2px;
        }

        .receipt-meta {
            font-size: 0.75rem;
            margin-bottom: 1rem;
            border-bottom: 1px dashed #ccc;
            padding-bottom: 0.75rem;
        }

        .receipt-meta .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .receipt-meta .label { color: #666; }

        .items-header {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .item-row {
            font-size: 0.75rem;
            margin-bottom: 6px;
        }

        .item-row .item-name {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .item-row .item-detail {
            display: flex;
            justify-content: space-between;
            color: #444;
        }

        .totals {
            border-top: 1px dashed #ccc;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            font-size: 0.78rem;
        }

        .totals .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .totals .grand-total {
            display: flex;
            justify-content: space-between;
            font-size: 1rem;
            font-weight: bold;
            border-top: 1px solid #333;
            border-bottom: 3px double #333;
            padding: 6px 0;
            margin-top: 6px;
        }

        .totals .balance-due {
            color: #c0392b;
            font-weight: bold;
        }

        .status-badge {
            display: inline-block;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .status-paid { background: #d4edda; color: #155724; }
        .status-partial { background: #fff3cd; color: #856404; }

        .receipt-footer {
            text-align: center;
            border-top: 1px dashed #ccc;
            margin-top: 1rem;
            padding-top: 0.75rem;
            font-size: 0.7rem;
            color: #666;
            line-height: 1.6;
        }

        @media print {
            body { background: none; padding: 0; }
            .receipt { box-shadow: none; max-width: 100%; }
        }
    </style>
</head>
<body>
<div class="receipt">

    {{-- Header --}}
    <div class="receipt-header">
        @if(isset($settings['organization_logo_path']) && $settings['organization_logo_path'])
            <img src="{{ asset('storage/' . $settings['organization_logo_path']) }}" alt="Logo" class="logo">
        @endif
        <h2>{{ $settings['organization_name'] ?? 'TARI NALIENDELE' }}</h2>
        <p>{{ $settings['organization_address'] ?? '' }}</p>
        <p>Tel: {{ $settings['organization_phone'] ?? 'N/A' }}</p>
        <p>{{ $settings['organization_email'] ?? '' }}</p>
    </div>

    {{-- Meta --}}
    <div class="receipt-meta">
        <div class="row">
            <span class="label">Receipt No:</span>
            <span>{{ $receipt->receipt_number }}</span>
        </div>
        <div class="row">
            <span class="label">Date:</span>
            <span>{{ $receipt->issued_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="row">
            <span class="label">Customer:</span>
            <span>{{ $receipt->sale->customer_name ?? 'Walk-in' }}</span>
        </div>
        <div class="row">
            <span class="label">Payment:</span>
            <span>{{ ucfirst($receipt->payment_method) }}</span>
        </div>
        <div class="row">
            <span class="label">Status:</span>
            <span>
                <span class="status-badge {{ $receipt->status === 'paid' ? 'status-paid' : 'status-partial' }}">
                    {{ ucfirst($receipt->status) }}
                </span>
            </span>
        </div>
    </div>

    {{-- Items --}}
    <div class="items-header">
        <span>Item</span>
        <span>Amount</span>
    </div>

    @foreach($receipt->sale->saleItems as $item)
        <div class="item-row">
            <div class="item-name">{{ $item->cashews->product->name ?? 'N/A' }}
                @if ($item->productSize)
                    <span class="text-gray-500 text-xs">
                                                        — {{ $item->productSize->size }} / {{ $item->productSize->color }}
                                                    </span>
                @endif
            </div>
            <div class="item-detail">
                <span>{{ $item->quantity }} x Tsh {{ number_format($item->unit_price, 2) }}</span>
                <span>Tsh {{ number_format($item->unit_price * $item->quantity, 2) }}</span>
            </div>
        </div>
    @endforeach

    {{-- Totals --}}
    <div class="totals">
        <div class="row">
            <span>Subtotal</span>
            <span>Tsh {{ number_format($receipt->subtotal, 2) }}</span>
        </div>
        @if($receipt->discount > 0)
            <div class="row">
                <span>Discount</span>
                <span>-Tsh {{ number_format($receipt->discount, 2) }}</span>
            </div>
        @endif
        <div class="grand-total">
            <span>TOTAL</span>
            <span>Tsh {{ number_format($receipt->total, 2) }}</span>
        </div>
        <div class="row" style="margin-top: 6px;">
            <span>Amount Paid</span>
            <span>Tsh {{ number_format($receipt->paid_amount, 2) }}</span>
        </div>
        @if($receipt->balance > 0)
            <div class="row balance-due">
                <span>Balance Due</span>
                <span>Tsh {{ number_format($receipt->balance, 2) }}</span>
            </div>
        @endif
    </div>

    {{-- Footer --}}
    <div class="receipt-footer">
        <p>*** Thank you for your purchase! ***</p>
        <p>{{ $settings['organization_name'] ?? '' }}</p>
        <p>{{ $settings['organization_address'] ?? '' }}</p>
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
