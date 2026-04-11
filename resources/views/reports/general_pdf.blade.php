{{-- resources/views/reports/general_pdf.blade.php --}}
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 11px; color: #1a1a1a; background: #fff; }

        /* Header */
        .header { background: #e67e00; color: #fff; padding: 18px 24px; border-radius: 6px; margin-bottom: 16px; }
        .header h1 { font-size: 20px; font-weight: 700; margin-bottom: 2px; }
        .header p  { font-size: 11px; opacity: 0.8; }

        /* Section title */
        .section-title { font-size: 13px; font-weight: 700; color: #e67e00; border-bottom: 2px solid #e67e00;
            padding-bottom: 4px; margin: 18px 0 8px; }

        /* KPI grid */
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        .kpi-grid td { width: 25%; padding: 8px; text-align: center; vertical-align: top; }
        .kpi-box { border: 1px solid #dee2e6; border-radius: 5px; padding: 10px 6px; }
        .kpi-value { font-size: 16px; font-weight: 700; }
        .kpi-label { font-size: 9px; color: #6c757d; margin-top: 2px; }
        .c-green  { color: #198754; } .c-blue  { color: #0d6efd; }
        .c-red    { color: #dc3545; } .c-amber { color: #e67e00; }
        .c-teal   { color: #0dcaf0; } .c-gray  { color: #6c757d; }

        /* Generic table */
        table.report { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.report th { background: #e67e00; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
        table.report th.r { text-align: right; }
        table.report td { padding: 5px 8px; border-bottom: 1px solid #e9ecef; font-size: 10px; }
        table.report td.r { text-align: right; }
        table.report tr:nth-child(even) td { background: #f8f9fa; }
        table.report tfoot td { background: #e9ecef; font-weight: 700; }

        /* PL table */
        table.pl { width: 100%; border-collapse: collapse; }
        table.pl td { padding: 6px 8px; border-bottom: 1px solid #e9ecef; font-size: 10px; }
        table.pl td.r { text-align: right; font-weight: 600; }
        table.pl tr.highlight td { background: #dbeafe; font-weight: 700; }
        table.pl tr.total td { background: #e67e00; color: #fff; font-weight: 700; font-size: 11px; }

        /* Two-column layout */
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col > tbody > tr > td { width: 50%; vertical-align: top; padding-right: 8px; }
        .two-col > tbody > tr > td:last-child { padding-right: 0; padding-left: 8px; }

        /* Alert */
        .alert-warn { background: #fff3cd; border: 1px solid #ffc107; border-radius: 4px;
            padding: 8px 10px; font-size: 10px; margin-top: 6px; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #aaa; margin-top: 24px; padding-top: 8px;
            border-top: 1px solid #dee2e6; }
    </style>
</head>
<body>

{{-- ── Header ──────────────────────────────────────────────────────────────── --}}
<div class="header">
    <h1>📊 General Business Report</h1>
    <p>Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }} &nbsp;|&nbsp; Generated: {{ now()->format('d M Y, H:i') }}</p>
</div>

{{-- ── KPI Cards ────────────────────────────────────────────────────────────── --}}
<div class="section-title">Key Performance Indicators</div>
<table class="kpi-grid">
    <tr>
        <td><div class="kpi-box"><div class="kpi-value c-green">${{ number_format($totalRevenue, 2) }}</div><div class="kpi-label">Total Revenue</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value {{ $netProfit >= 0 ? 'c-blue' : 'c-red' }}">${{ number_format($netProfit, 2) }}</div><div class="kpi-label">Net Profit</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value c-amber">{{ $profitMargin }}%</div><div class="kpi-label">Profit Margin</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value c-teal">{{ $totalSalesCount }}</div><div class="kpi-label">Total Sales</div></div></td>
    </tr>
    <tr>
        <td><div class="kpi-box"><div class="kpi-value c-red">${{ number_format($totalExpenses, 2) }}</div><div class="kpi-label">Total Expenses</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value c-gray">{{ $availablePhones }}</div><div class="kpi-label">Phones in Stock</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value c-blue">${{ number_format($inventoryValue, 2) }}</div><div class="kpi-label">Inventory Value</div></div></td>
        <td><div class="kpi-box"><div class="kpi-value c-amber">${{ number_format($pendingInstallments, 2) }}</div><div class="kpi-label">Pending Installments</div></div></td>
    </tr>
</table>

{{-- ── P&L + Expenses ───────────────────────────────────────────────────────── --}}
<div class="section-title">Profit &amp; Loss Summary</div>
<table class="two-col">
    <tr>
        <td>
            <table class="pl">
                <tr><td>Total Revenue</td><td class="r c-green">${{ number_format($totalRevenue, 2) }}</td></tr>
                <tr><td>Cost of Goods Sold</td><td class="r c-red">– ${{ number_format($totalCogs, 2) }}</td></tr>
                <tr class="highlight"><td>Gross Profit</td><td class="r">${{ number_format($grossProfit, 2) }}</td></tr>
                <tr><td>Total Operating Expenses</td><td class="r c-red">– ${{ number_format($totalExpenses, 2) }}</td></tr>
                <tr class="total"><td>Net Profit</td><td class="r">${{ number_format($netProfit, 2) }}</td></tr>
                <tr><td>Discounts Given</td><td class="r">– ${{ number_format($totalDiscounts, 2) }}</td></tr>
                <tr><td>Profit Margin</td><td class="r">{{ $profitMargin }}%</td></tr>
            </table>
        </td>
        <td>
            <table class="report">
                <thead><tr><th>Expense Category</th><th class="r">Amount</th><th class="r">% Share</th></tr></thead>
                <tbody>
                @forelse($expensesByCategory as $exp)
                    <tr>
                        <td>{{ $exp->category }}</td>
                        <td class="r">${{ number_format($exp->total, 2) }}</td>
                        <td class="r">{{ $totalExpenses > 0 ? round(($exp->total / $totalExpenses) * 100, 1) : 0 }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:#999">No expenses.</td></tr>
                @endforelse
                </tbody>
                <tfoot><tr><td>Total</td><td class="r">${{ number_format($totalExpenses, 2) }}</td><td class="r">100%</td></tr></tfoot>
            </table>
        </td>
    </tr>
</table>

{{-- ── Sales + Top Brands ───────────────────────────────────────────────────── --}}
<div class="section-title">Sales Overview</div>
<table class="two-col">
    <tr>
        <td>
            <table class="report">
                <thead><tr><th>Metric</th><th class="r">Value</th></tr></thead>
                <tbody>
                <tr><td>Total Transactions</td><td class="r">{{ $totalSalesCount }}</td></tr>
                <tr><td>Full Payment Sales</td><td class="r">{{ $fullPaymentSales }}</td></tr>
                <tr><td>Installment Sales</td><td class="r">{{ $installmentSales }}</td></tr>
                <tr><td>Active Installment Plans</td><td class="r">{{ $activeInstallmentCount }}</td></tr>
                <tr><td>Receivable (Installments)</td><td class="r">${{ number_format($pendingInstallments, 2) }}</td></tr>
                </tbody>
            </table>
        </td>
        <td>
            <table class="report">
                <thead><tr><th>Brand</th><th class="r">Units</th><th class="r">Revenue</th></tr></thead>
                <tbody>
                @forelse($topBrands as $brand)
                    <tr>
                        <td>{{ $brand->brand_name }}</td>
                        <td class="r">{{ $brand->units_sold }}</td>
                        <td class="r">${{ number_format($brand->revenue, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:#999">No data.</td></tr>
                @endforelse
                </tbody>
            </table>
        </td>
    </tr>
</table>

{{-- ── Inventory ────────────────────────────────────────────────────────────── --}}
<div class="section-title">Inventory by Brand</div>
<table class="report">
    <thead><tr><th>Brand</th><th class="r">Units in Stock</th><th class="r">Stock Value (Cost)</th></tr></thead>
    <tbody>
    @forelse($stockByBrand as $stock)
        <tr>
            <td>{{ optional($stock->brand)->name ?? 'Unknown' }}</td>
            <td class="r">{{ $stock->count }}</td>
            <td class="r">${{ number_format($stock->value, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="3" style="text-align:center;color:#999">No inventory data.</td></tr>
    @endforelse
    </tbody>
    <tfoot><tr><td>Total</td><td class="r">{{ $availablePhones }}</td><td class="r">${{ number_format($inventoryValue, 2) }}</td></tr></tfoot>
</table>

@if($lowStockItems->isNotEmpty())
    <div class="alert-warn">
        ⚠️ <strong>Low Stock:</strong>
        {{ $lowStockItems->map(fn($s) => optional($s->brand)->name . ' (' . $s->current_stock . ' units)')->implode(' · ') }}
    </div>
@endif

{{-- ── Recent Sales ─────────────────────────────────────────────────────────── --}}
<div class="section-title">Recent Sales</div>
<table class="report">
    <thead><tr><th>Date</th><th>Customer</th><th>Items</th><th class="r">Amount</th><th class="r">Type</th></tr></thead>
    <tbody>
    @forelse($recentSales as $sale)
        <tr>
            <td>{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
            <td>{{ $sale->customer_name }}</td>
            <td>{{ $sale->saleItems->map(fn($i) => optional(optional($i->phone)->brand)->name . ' ' . optional($i->phone)->model)->implode(', ') }}</td>
            <td class="r">${{ number_format($sale->final_amount, 2) }}</td>
            <td class="r">{{ $sale->is_installment ? 'Installment' : 'Full' }}</td>
        </tr>
    @empty
        <tr><td colspan="5" style="text-align:center;color:#999">No sales.</td></tr>
    @endforelse
    </tbody>
</table>

<div class="footer">
    This report is auto-generated and confidential. &copy; {{ date('Y') }} Your Business Name. All rights reserved.
</div>
</body>
</html>
