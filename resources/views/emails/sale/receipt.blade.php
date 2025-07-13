<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Sale Receipt - {{ $receipt->receipt_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
            background-color: #f8f8f8;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            margin: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 15px;
        }
        p {
            margin: 10px 0;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #3490dc;
            color: white !important;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Hello {{ $sale->customer_name }},</h1>

    <p>Thank you for your purchase. Please find your receipt details below:</p>

    <p><strong>Receipt Number:</strong> {{ $receipt->receipt_number }}</p>
    <p><strong>Amount Paid:</strong> {{ number_format($receipt->paid_amount, 2) }} TZS</p>
    <p><strong>Remaining Balance:</strong> {{ number_format($receipt->balance, 2) }} TZS</p>
    <p><strong>Payment Method:</strong> {{ ucfirst($receipt->payment_method) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($receipt->status) }}</p>

    <a href="{{ url('/') }}" class="button">Visit PhoneStore</a>

    <p class="footer">Thanks,<br>{{ config('app.name') }}</p>
</div>
</body>
</html>
