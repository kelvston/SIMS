<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ ucfirst($frequency) }} Inventory Report</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; line-height: 1.5;">
    <h1>{{ ucfirst($frequency) }} Inventory Report</h1>
    <p><strong>Period:</strong> {{ $report['period'] }}</p>

    <h2>Summary</h2>
    <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #d1d5db;">
        <tr><td>Products sold</td><td>{{ number_format($report['sold_quantity']) }} units</td></tr>
        <tr><td>Products received</td><td>{{ number_format($report['received_quantity']) }} units</td></tr>
        <tr><td>Revenue</td><td>TZS {{ number_format($report['revenue'], 2) }}</td></tr>
        <tr><td>Cost of goods sold</td><td>TZS {{ number_format($report['cost_of_goods_sold'], 2) }}</td></tr>
        <tr><td>Gross profit</td><td>TZS {{ number_format($report['gross_profit'], 2) }}</td></tr>
        <tr><td>Expenses</td><td>TZS {{ number_format($report['expenses'], 2) }}</td></tr>
        <tr><td><strong>Net profit</strong></td><td><strong>TZS {{ number_format($report['net_profit'], 2) }}</strong></td></tr>
    </table>

    <h2>Sold Products</h2>
    @if (count($report['sold_products']))
        <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #d1d5db;">
            <thead><tr><th>Product</th><th>Quantity</th><th>Revenue</th><th>Profit</th></tr></thead>
            <tbody>
                @foreach ($report['sold_products'] as $product)
                    <tr><td>{{ $product['name'] }}</td><td>{{ number_format($product['quantity']) }}</td><td>TZS {{ number_format($product['revenue'], 2) }}</td><td>TZS {{ number_format($product['profit'], 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No products were sold during this period.</p>
    @endif

    <h2>Received Products</h2>
    @if (count($report['received_products']))
        <table cellpadding="6" cellspacing="0" border="1" style="border-collapse: collapse; border-color: #d1d5db;">
            <thead><tr><th>Product</th><th>Quantity</th><th>Purchase value</th></tr></thead>
            <tbody>
                @foreach ($report['received_products'] as $product)
                    <tr><td>{{ $product['name'] }}</td><td>{{ number_format($product['quantity']) }}</td><td>TZS {{ number_format($product['cost'], 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No products were received during this period.</p>
    @endif
</body>
</html>
