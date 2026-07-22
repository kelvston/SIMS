{{-- resources/views/emails/general_report.blade.php --}}
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
    .wrapper { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden;
               box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
    .header { background: #1e3a5f; color: #fff; padding: 28px 32px; text-align: center; }
    .header h1 { font-size: 22px; margin: 0 0 4px; }
    .header p { font-size: 13px; opacity: 0.8; margin: 0; }
    .body { padding: 28px 32px; }
    .body p { color: #444; font-size: 14px; line-height: 1.6; margin-bottom: 16px; }
    .kpi-row { display: flex; gap: 12px; margin-bottom: 20px; }
    .kpi { flex: 1; background: #f8f9fa; border-radius: 6px; padding: 14px 10px; text-align: center;
           border-top: 3px solid #1e3a5f; }
    .kpi .val { font-size: 18px; font-weight: 700; color: #1e3a5f; }
    .kpi .lbl { font-size: 11px; color: #888; margin-top: 3px; }
    .divider { border: none; border-top: 1px solid #e9ecef; margin: 20px 0; }
    .footer { background: #f8f9fa; padding: 16px 32px; text-align: center; font-size: 11px; color: #aaa; }
    .btn { display: inline-block; background: #1e3a5f; color: #fff; text-decoration: none;
           padding: 10px 22px; border-radius: 5px; font-size: 13px; margin-top: 8px; }
</style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>📊 General Business Report</h1>
        <p>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>

    <div class="body">
        <p>Hello Admin,</p>
        <p>Please find attached the <strong>General Business Report</strong> for the period
            <strong>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }}</strong> to
            <strong>{{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</strong>.
            Here is a quick summary:
        </p>

        <!-- KPI Row 1 -->
        <div class="kpi-row">
            <div class="kpi">
                <div class="val">Tsh {{ number_format($totalRevenue, 2) }}</div>
                <div class="lbl">Total Revenue</div>
            </div>
            <div class="kpi">
                <div class="val" style="color:{{ $netProfit >= 0 ? '#198754' : '#dc3545' }}">Tsh {{ number_format($netProfit, 2) }}</div>
                <div class="lbl">Net Profit</div>
            </div>
            <div class="kpi">
                <div class="val">{{ $profitMargin }}%</div>
                <div class="lbl">Profit Margin</div>
            </div>
        </div>

        <!-- KPI Row 2 -->
        <div class="kpi-row">
            <div class="kpi">
                <div class="val">{{ $totalSalesCount }}</div>
                <div class="lbl">Total Sales</div>
            </div>
            <div class="kpi">
                <div class="val" style="color:#dc3545">Tsh {{ number_format($totalExpenses, 2) }}</div>
                <div class="lbl">Total Expenses</div>
            </div>
            <div class="kpi">
                <div class="val">{{ $availablePhones }}</div>
                <div class="lbl">Phones in Stock</div>
            </div>
        </div>

        @if($pendingInstallments > 0)
        <p style="background:#fff3cd;padding:10px 14px;border-radius:5px;border-left:4px solid #ffc107;">
            ⚠️ <strong>Tsh {{ number_format($pendingInstallments, 2) }}</strong> in installment payments are still outstanding.
        </p>
        @endif

        <hr class="divider">
        <p>The full report (PDF) is attached to this email. You can also view it directly in the admin panel.</p>
        <p style="text-align:center;">
            <a href="{{ url('/reports/general') }}" class="btn">View Full Report Online</a>
        </p>
    </div>

    <div class="footer">
        This is an automated report email. Generated on {{ now()->format('d M Y, H:i') }}.<br>
        &copy; {{ date('Y') }} Your Business Name. All rights reserved.
    </div>
</div>
</body>
</html>
