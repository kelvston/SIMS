<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Sale Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            position: relative;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            position: relative;
        }
        header img.logo {
            position: absolute;
            left: 0;
            top: 0;
            height: 60px;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        section {
            margin-bottom: 25px;
        }
        h2, h3 {
            margin-bottom: 10px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            font-weight: 600;
        }
        p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #444;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: 700;
        }
        .right {
            text-align: right;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ccc;
            padding-top: 15px;
            margin-top: 40px;
        }
        /* Print styles */
        @media print {
            body {
                font-size: 10pt;
                color: #000;
            }
            header img.logo {
                height: 50px;
            }
            .container {
                border: none;
                padding: 0;
                margin: 0;
            }
            section {
                page-break-inside: avoid;
            }
        }
        /* QR code style */

    </style>
</head>
<body>
<div class="container">
    <header>
        {{-- Replace with your actual logo path or remove if none --}}
        <img src="{{ public_path('images/logo.png') }}" alt="Logo" class="logo" />
        <h1>PhoneStore Pro</h1>
        <p>Sales Receipt</p>

        {{-- QR Code --}}

    </header>

    <section class="receipt-info">
        <h2>Receipt Information</h2>
        <p><strong>Receipt Number:</strong> {{ $receipt->receipt_number }}</p>
        <p><strong>Date Issued:</strong> {{ $receipt->issued_at->format('Y-m-d H:i') }}</p>
    </section>

    <section class="customer-info">
        <h2>Customer Information</h2>
        <p><strong>Name:</strong> {{ $sale->customer_name }}</p>
        <p><strong>Email:</strong> {{ $sale->customer_email ?? 'N/A' }}</p>
    </section>

    <section class="sold-items">
        <h2>Sold Items</h2>
        <table>
            <thead>
            <tr>
                <th>IMEI</th>
                <th>Brand</th>
                <th>Model</th>
                <th>Color</th>
                <th>Storage</th>
                <th class="right">Unit Price (TZS)</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($sale->saleItems as $item)
                <tr>
                    <td>{{ $item->phone->imei }}</td>
                    <td>{{ $item->phone->brand->name ?? 'N/A' }}</td>
                    <td>{{ $item->phone->model }}</td>
                    <td>{{ $item->phone->color }}</td>
                    <td>{{ $item->phone->storage_capacity }}</td>
                    <td class="right">{{ number_format($item->unit_price, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </section>

    <section class="summary">
        <h2>Summary</h2>
        <p><strong>Subtotal:</strong> {{ number_format($receipt->subtotal, 2) }} TZS</p>
        <p><strong>Tax:</strong> {{ number_format($receipt->tax, 2) }} TZS</p>
        <p><strong>Discount:</strong> {{ number_format($receipt->discount, 2) }} TZS</p>
        <p><strong>Total:</strong> {{ number_format($receipt->total, 2) }} TZS</p>
        <p><strong>Paid Amount:</strong> {{ number_format($receipt->paid_amount, 2) }} TZS</p>
        <p><strong>Remaining Balance:</strong> {{ number_format($receipt->balance, 2) }} TZS</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($receipt->payment_method) }}</p>
        <p><strong>Status:</strong> {{ ucfirst($receipt->status) }}</p>
    </section>

    @if($sale->is_installment && $sale->installmentPlan)
        <section class="installment-info" style="margin-top: 30px;">
            <h2>Installment Plan Details</h2>
            <table>
                <tbody>
                <tr>
                    <td><strong>Total Installments</strong></td>
                    <td>{{ $sale->installmentPlan->total_installments }}</td>
                </tr>
                <tr>
                    <td><strong>Installment Amount (TZS)</strong></td>
                    <td>{{ number_format($sale->installmentPlan->installment_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Start Date</strong></td>
                    <td>{{ $sale->installmentPlan->start_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td><strong>Next Payment Due</strong></td>
                    <td>{{ $sale->installmentPlan->next_payment_date->format('Y-m-d') }}</td>
                </tr>
                <tr>
                    <td><strong>Status</strong></td>
                    <td>{{ ucfirst($sale->installmentPlan->status) }}</td>
                </tr>
                </tbody>
            </table>
        </section>
    @endif

    <footer class="footer">
        <p>Thank you for your purchase!</p>
        <p>PhoneStore Pro &mdash; Your trusted phone seller</p>
    </footer>
</div>
</body>
</html>
