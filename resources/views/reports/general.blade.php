{{-- resources/views/reports/general.blade.php --}}
@extends('layouts.app')

@section('title', 'General Business Report')

@push('styles')
    <style>
        :root {
            --navy:       #0f1f3d;
            --navy-light: #1a3260;
            --accent:     #2563eb;
            --accent-2:   #0ea5e9;
            --emerald:    #10b981;
            --amber:      #f59e0b;
            --rose:       #f43f5e;
            --slate:      #64748b;
            --surface:    #f8fafc;
            --border:     #e2e8f0;
            --white:      #ffffff;
            --text:       #0f172a;
            --text-muted: #64748b;
        }

        body { font-family: 'DM Sans', sans-serif; background: var(--surface); color: var(--text); }

        /* ── Page Shell ─────────────────────────────────────────── */
        .rpt-wrapper { max-width: 1400px; margin: 0 auto; padding: 28px 24px 60px; }

        /* ── Hero Header ────────────────────────────────────────── */
        .rpt-hero {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-light) 60%, #e67e00 100%);
            border-radius: 20px;
            padding: 36px 40px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            color: #fff;
        }
        .rpt-hero::before {
            content: '';
            position: absolute; inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .rpt-hero-content { position: relative; z-index: 1; }
        .rpt-hero h1 { font-size: 28px; font-weight: 700; margin: 0 0 6px; letter-spacing: -0.5px; }
        .rpt-hero p  { font-size: 14px; opacity: 0.7; margin: 0; }
        .rpt-hero-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 0; }

        .btn-pdf {
            display: inline-flex; align-items: center; gap: 8px;
            background: var(--rose); color: #fff; border: none;
            padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer; transition: all .2s;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-pdf:hover { background: #e11d48; color: #fff; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(244,63,94,.4); }

        .btn-email {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.3);
            padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s; backdrop-filter: blur(4px);
            font-family: 'DM Sans', sans-serif;
        }
        .btn-email:hover { background: rgba(255,255,255,0.25); transform: translateY(-1px); }

        /* ── Filter Card ────────────────────────────────────────── */
        .filter-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 16px; padding: 20px 24px; margin-bottom: 28px;
            display: flex; align-items: flex-end; gap: 16px; flex-wrap: wrap;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .filter-card .fg { display: flex; flex-direction: column; gap: 6px; min-width: 180px; }
        .filter-card label { font-size: 12px; font-weight: 600; color: var(--text-muted); text-transform: uppercase; letter-spacing: .6px; }
        .filter-card input[type=date] {
            border: 1.5px solid var(--border); border-radius: 10px;
            padding: 9px 14px; font-size: 13px; font-family: 'DM Sans', sans-serif;
            color: var(--text); background: var(--surface); outline: none; transition: border .2s;
        }
        .filter-card input[type=date]:focus { border-color: var(--accent); }
        .btn-filter {
            background: var(--navy); color: #fff; border: none;
            padding: 10px 24px; border-radius: 10px; font-size: 13px; font-weight: 600;
            cursor: pointer; transition: all .2s; font-family: 'DM Sans', sans-serif;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-filter:hover { background: var(--accent); transform: translateY(-1px); }

        /* ── Section Label ──────────────────────────────────────── */
        .section-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1.2px;
            text-transform: uppercase; color: var(--text-muted);
            margin: 0 0 14px; padding-left: 2px;
        }

        /* ── KPI Grid ───────────────────────────────────────────── */
        .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 28px; }
        @media(max-width:900px){ .kpi-grid{ grid-template-columns: repeat(2,1fr); } }
        @media(max-width:500px){ .kpi-grid{ grid-template-columns: 1fr 1fr; } }

        .kpi-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 16px; padding: 20px 22px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
            position: relative; overflow: hidden; transition: transform .2s, box-shadow .2s;
        }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.08); }
        .kpi-card::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            border-radius: 16px 16px 0 0;
        }
        .kpi-card.c-green::after  { background: var(--emerald); }
        .kpi-card.c-blue::after   { background: var(--accent); }
        .kpi-card.c-amber::after  { background: var(--amber); }
        .kpi-card.c-rose::after   { background: var(--rose); }
        .kpi-card.c-sky::after    { background: var(--accent-2); }
        .kpi-card.c-slate::after  { background: var(--slate); }
        .kpi-card.c-navy::after   { background: var(--navy); }
        .kpi-card.c-teal::after   { background: #14b8a6; }

        .kpi-icon { font-size: 22px; margin-bottom: 10px; display: block; }
        .kpi-value { font-size: 26px; font-weight: 700; letter-spacing: -1px; line-height: 1; margin-bottom: 4px; font-family: 'DM Mono', monospace; }
        .kpi-value.c-green  { color: var(--emerald); }
        .kpi-value.c-blue   { color: var(--accent); }
        .kpi-value.c-amber  { color: var(--amber); }
        .kpi-value.c-rose   { color: var(--rose); }
        .kpi-value.c-sky    { color: var(--accent-2); }
        .kpi-value.c-slate  { color: var(--slate); }
        .kpi-value.c-navy   { color: var(--navy); }
        .kpi-value.c-teal   { color: #14b8a6; }
        .kpi-label { font-size: 12px; color: var(--text-muted); font-weight: 500; }

        /* ── Charts ─────────────────────────────────────────────── */
        .chart-row { display: grid; grid-template-columns: 2fr 1fr; gap: 14px; margin-bottom: 28px; }
        @media(max-width:768px){ .chart-row{ grid-template-columns: 1fr; } }

        .chart-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 16px; padding: 22px 24px;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .chart-card h3 { font-size: 14px; font-weight: 600; margin: 0 0 18px; color: var(--text); }

        /* ── Two-Col Panels ─────────────────────────────────────── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 28px; }
        @media(max-width:768px){ .two-col{ grid-template-columns: 1fr; } }

        /* ── Report Panel ───────────────────────────────────────── */
        .rpt-panel {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 1px 4px rgba(0,0,0,.04);
        }
        .rpt-panel-header {
            padding: 16px 22px; border-bottom: 1px solid var(--border);
            font-size: 14px; font-weight: 600; color: var(--text);
            display: flex; align-items: center; gap: 8px;
            background: linear-gradient(to right, var(--surface), var(--white));
        }
        .rpt-panel-header .dot {
            width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
        }

        /* ── Data Tables ────────────────────────────────────────── */
        .rpt-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .rpt-table thead th {
            padding: 10px 16px; text-align: left;
            font-size: 11px; font-weight: 700; letter-spacing: .8px;
            text-transform: uppercase; color: var(--text-muted);
            background: var(--surface); border-bottom: 1px solid var(--border);
        }
        .rpt-table thead th.r { text-align: right; }
        .rpt-table tbody td {
            padding: 11px 16px; border-bottom: 1px solid #f1f5f9;
            color: var(--text); vertical-align: middle;
        }
        .rpt-table tbody td.r { text-align: right; font-family: 'DM Mono', monospace; font-size: 12px; }
        .rpt-table tbody tr:last-child td { border-bottom: none; }
        .rpt-table tbody tr:hover td { background: #f8fafc; }
        .rpt-table .row-gross td { background: #f0fdf4; font-weight: 600; }
        .rpt-table .row-net   td { background: #eff6ff; font-weight: 700; font-size: 14px; }
        .rpt-table .row-sub   td { color: var(--text-muted); font-size: 12px; }
        .rpt-table tfoot td {
            padding: 12px 16px; background: var(--surface);
            font-weight: 700; border-top: 2px solid var(--border); font-size: 13px;
        }
        .rpt-table tfoot td.r { text-align: right; font-family: 'DM Mono', monospace; }

        /* ── Badge ──────────────────────────────────────────────── */
        .badge-pill {
            display: inline-block; padding: 3px 10px; border-radius: 999px;
            font-size: 11px; font-weight: 600;
        }
        .badge-full    { background: #dcfce7; color: #15803d; }
        .badge-install { background: #fef9c3; color: #854d0e; }

        /* ── Progress bar ───────────────────────────────────────── */
        .prog-wrap { height: 5px; background: #e2e8f0; border-radius: 4px; overflow: hidden; margin-top: 5px; }
        .prog-bar  { height: 100%; border-radius: 4px; transition: width .6s ease; }

        /* ── Alerts ─────────────────────────────────────────────── */
        .alert-low-stock {
            background: #fffbeb; border: 1px solid #fde68a;
            border-left: 4px solid var(--amber);
            border-radius: 12px; padding: 14px 18px;
            font-size: 13px; color: #78350f; margin-bottom: 28px;
        }
        .flash-success {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-left: 4px solid var(--emerald);
            border-radius: 12px; padding: 14px 18px;
            font-size: 13px; color: #14532d; margin-bottom: 20px;
            display: flex; align-items: center; justify-content: space-between;
        }

        .full-panel { margin-bottom: 28px; }

        /* ── Animations ─────────────────────────────────────────── */
        .fade-up { animation: fadeUp .4s ease both; }
        @keyframes fadeUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:none; } }
        .d1 { animation-delay:.05s; } .d2 { animation-delay:.1s; }
        .d3 { animation-delay:.15s; } .d4 { animation-delay:.2s; }
    </style>
@endpush

@section('content')
    <div class="rpt-wrapper">

        {{-- Flash --}}
        @if(session('success'))
            <div class="flash-success fade-up">
                <span>✅ {{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;font-size:18px;color:#14532d;line-height:1;">×</button>
            </div>
        @endif

        {{-- Hero --}}
        <div class="rpt-hero fade-up">
            <div class="rpt-hero-content" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:20px;">
                <div>
                    <h1>📊 General Business Report</h1>
                    <p>{{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} &mdash; {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }} &nbsp;·&nbsp; Generated {{ now()->format('d M Y, H:i') }}</p>
                </div>
                <div class="rpt-hero-actions">
                    <a href="{{ route('general.download', request()->query()) }}" class="btn-pdf">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
                        Download PDF
                    </a>
                    <form method="POST" action="{{ route('general.email') }}" style="display:inline">
                        @csrf
                        @foreach(request()->query() as $k => $v)
                            <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                        @endforeach
                        <button type="submit" class="btn-email" onclick="return confirm('Send this report to the admin email?')">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            Email Report
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('reports.general') }}">
            <div class="filter-card fade-up d1">
                <div class="fg">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate }}">
                </div>
                <div class="fg">
                    <label>End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate }}">
                </div>
                <button type="submit" class="btn-filter">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4h18M7 9h10M11 14h2"/></svg>
                    Apply Filter
                </button>
            </div>
        </form>

        {{-- KPIs --}}
        <p class="section-label fade-up d2">Key Performance Indicators</p>
        <div class="kpi-grid fade-up d2">
            <div class="kpi-card c-green">
                <span class="kpi-icon">💰</span>
                <div class="kpi-value c-green">Tsh {{ number_format($totalRevenue, 0) }}</div>
                <div class="kpi-label">Total Revenue</div>
            </div>
            <div class="kpi-card {{ $netProfit >= 0 ? 'c-blue' : 'c-rose' }}">
                <span class="kpi-icon">{{ $netProfit >= 0 ? '📈' : '📉' }}</span>
                <div class="kpi-value {{ $netProfit >= 0 ? 'c-blue' : 'c-rose' }}">Tsh {{ number_format($netProfit, 0) }}</div>
                <div class="kpi-label">Net Profit</div>
            </div>
            <div class="kpi-card c-amber">
                <span class="kpi-icon">🎯</span>
                <div class="kpi-value c-amber">{{ $profitMargin }}%</div>
                <div class="kpi-label">Profit Margin</div>
            </div>
            <div class="kpi-card c-sky">
                <span class="kpi-icon">🧾</span>
                <div class="kpi-value c-sky">{{ $totalSalesCount }}</div>
                <div class="kpi-label">Total Sales</div>
            </div>
            <div class="kpi-card c-rose">
                <span class="kpi-icon">💸</span>
                <div class="kpi-value c-rose">Tsh {{ number_format($totalExpenses, 0) }}</div>
                <div class="kpi-label">Total Expenses</div>
            </div>
            <div class="kpi-card c-slate">
                <span class="kpi-icon">📦</span>
                <div class="kpi-value c-slate">{{ $availablePhones }}</div>
                <div class="kpi-label">Units in Stock</div>
            </div>
            <div class="kpi-card c-navy">
                <span class="kpi-icon">🏦</span>
                <div class="kpi-value c-navy">Tsh {{ number_format($inventoryValue, 0) }}</div>
                <div class="kpi-label">Inventory Value</div>
            </div>
            <div class="kpi-card c-teal">
                <span class="kpi-icon">⏳</span>
                <div class="kpi-value c-teal">Tsh {{ number_format($pendingInstallments, 0) }}</div>
                <div class="kpi-label">Pending Installments</div>
            </div>
        </div>

        {{-- Charts --}}
        <p class="section-label fade-up d3">Sales Analytics</p>
        <div class="chart-row fade-up d3">
            <div class="chart-card">
                <h3>📅 Daily Sales Trend</h3>
                <canvas id="salesTrendChart" height="90"></canvas>
            </div>
            <div class="chart-card">
                <h3>🥧 Revenue Breakdown</h3>
                <canvas id="revenueBreakdownChart" height="190"></canvas>
            </div>
        </div>

        {{-- P&L + Expenses --}}
        <p class="section-label fade-up d4">Financial Summary</p>
        <div class="two-col fade-up d4">
            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--emerald)"></span> Profit &amp; Loss
                </div>
                <table class="rpt-table">
                    <tbody>
                    <tr><td>Total Revenue</td><td class="r" style="color:var(--emerald);font-weight:700">Tsh {{ number_format($totalRevenue, 2) }}</td></tr>
                    <tr class="row-sub"><td>Cost of Goods Sold</td><td class="r" style="color:var(--rose)">– Tsh {{ number_format($totalCogs, 2) }}</td></tr>
                    <tr class="row-gross"><td>Gross Profit</td><td class="r">Tsh {{ number_format($grossProfit, 2) }}</td></tr>
                    <tr class="row-sub"><td>Operating Expenses</td><td class="r" style="color:var(--rose)">– Tsh {{ number_format($totalExpenses, 2) }}</td></tr>
                    <tr class="row-net"><td>Net Profit</td><td class="r" style="color:{{ $netProfit >= 0 ? 'var(--accent)' : 'var(--rose)' }}">Tsh {{ number_format($netProfit, 2) }}</td></tr>
                    <tr class="row-sub"><td>Discounts Given</td><td class="r">– Tsh {{ number_format($totalDiscounts, 2) }}</td></tr>
                    <tr><td style="font-weight:600">Profit Margin</td><td class="r" style="font-weight:700;color:var(--amber)">{{ $profitMargin }}%</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--rose)"></span> Expenses by Category
                </div>
                <table class="rpt-table">
                    <thead><tr><th>Category</th><th class="r">Amount</th><th class="r">Share</th></tr></thead>
                    <tbody>
                    @forelse($expensesByCategory as $exp)
                        <tr>
                            <td>
                                <span style="font-weight:500">{{ $exp->category }}</span>
                                <div class="prog-wrap">
                                    <div class="prog-bar" style="width:{{ $totalExpenses > 0 ? round(($exp->total/$totalExpenses)*100,1) : 0 }}%;background:var(--rose)"></div>
                                </div>
                            </td>
                            <td class="r">Tsh {{ number_format($exp->total, 2) }}</td>
                            <td class="r" style="color:var(--text-muted)">{{ $totalExpenses > 0 ? round(($exp->total/$totalExpenses)*100,1) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:28px">No expenses recorded.</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot><tr><td>Total</td><td class="r">Tsh {{ number_format($totalExpenses, 2) }}</td><td class="r">100%</td></tr></tfoot>
                </table>
            </div>
        </div>

        {{-- Sales + Top Brands --}}
        <div class="two-col fade-up">
            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--accent-2)"></span> Sales Overview
                </div>
                <table class="rpt-table">
                    <tbody>
                    <tr><td>Total Transactions</td><td class="r" style="font-weight:700">{{ $totalSalesCount }}</td></tr>
                    <tr><td>Full Payment Sales</td><td class="r">{{ $fullPaymentSales }}</td></tr>
                    <tr><td>Installment Sales</td><td class="r">{{ $installmentSales }}</td></tr>
                    <tr><td>Active Installment Plans</td><td class="r">{{ $activeInstallmentCount }}</td></tr>
                    <tr style="background:#fefce8"><td style="font-weight:600">Receivable (Installments)</td><td class="r" style="color:var(--amber);font-weight:700">Tsh {{ number_format($pendingInstallments, 2) }}</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--amber)"></span> Top Selling Products
                </div>
                <table class="rpt-table">
                    <thead><tr><th>#</th><th>Product</th><th class="r">Units</th><th class="r">Revenue</th></tr></thead>
                    <tbody>
                    @forelse($topBrands as $i => $brand)
                        <tr>
                            <td style="color:var(--text-muted);font-size:12px;font-weight:600">{{ $i + 1 }}</td>
                            <td style="font-weight:600">{{ $brand->product_name }}</td>
                            <td class="r">{{ $brand->units_sold }}</td>
                            <td class="r" style="color:var(--emerald)">Tsh {{ number_format($brand->revenue, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:28px">No sales data.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Inventory --}}
        <p class="section-label fade-up">Inventory Status</p>
        <div class="full-panel fade-up">
            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--navy)"></span> Stock by Product
                </div>
                <table class="rpt-table">
                    <thead><tr><th>Product</th><th class="r">Units in Stock</th><th class="r">Stock Value (Cost)</th></tr></thead>
                    <tbody>
                    @forelse($stockByBrand as $stock)
                        <tr>
                            <td style="font-weight:500">{{ optional($stock->product)->name ?? 'Unknown' }}</td>
                            <td class="r">{{ $stock->count }}</td>
                            <td class="r">Tsh {{ number_format($stock->value, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center;color:var(--text-muted);padding:28px">No inventory data.</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot><tr><td>Total</td><td class="r">{{ $availablePhones }}</td><td class="r">Tsh {{ number_format($inventoryValue, 2) }}</td></tr></tfoot>
                </table>
            </div>
        </div>

        @if($lowStockItems->isNotEmpty())
            <div class="alert-low-stock fade-up">
                ⚠️ <strong>Low Stock Alert:</strong>
                {{ $lowStockItems->map(fn($s) => optional($s->product)->name . ' (' . $s->quantity . ' units)')->implode(' · ') }}
            </div>
        @endif

        {{-- Recent Sales --}}
        <p class="section-label fade-up">Recent Transactions</p>
        <div class="full-panel fade-up">
            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--accent)"></span> Recent Sales (up to 10)
                </div>
                <div style="overflow-x:auto">
                    <table class="rpt-table">
                        <thead>
                        <tr>
                            <th>Date</th><th>Customer</th><th>Items</th>
                            <th class="r">Amount</th><th class="r">Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentSales as $sale)
                            <tr>
                                <td style="color:var(--text-muted);white-space:nowrap;font-size:12px">{{ \Carbon\Carbon::parse($sale->sale_date)->format('d M Y') }}</td>
                                <td style="font-weight:500">{{ $sale->customer_name }}</td>
                                <td style="color:var(--text-muted);font-size:12px;max-width:200px">
                                    {{ $sale->saleItems->map(fn($i) => (optional($i->product)->name ?? 'Unknown') . ' x ' . $i->quantity)->implode(', ') }}
                                </td>
                                <td class="r" style="color:var(--emerald);font-weight:600">Tsh {{ number_format($sale->final_amount, 2) }}</td>
                                <td class="r">
                                <span class="badge-pill {{ $sale->is_installment ? 'badge-install' : 'badge-full' }}">
                                    {{ $sale->is_installment ? 'Installment' : 'Full' }}
                                </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:32px">No sales in this period.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--stock adjustment report--}}
        <p class="section-label fade-up">Stock Adjustment Report</p>
        <div class="full-panel fade-up">
            <div class="rpt-panel">
                <div class="rpt-panel-header">
                    <span class="dot" style="background:var(--accent)"></span> Adjusted Products
                </div>
                <div style="overflow-x:auto">
                    <table class="rpt-table">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th style="text-align:right">Old Qty</th>
                            <th style="text-align:right">New Qty</th>
                            <th style="text-align:right">Cost</th>
                            <th>Adjusted By</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($stockAdjustments as $adjustment)
                            <tr>
                                <td style="color:var(--text-muted);white-space:nowrap;font-size:12px">
                                    {{ \Carbon\Carbon::parse($adjustment->created_at)->format('d M Y') }}
                                </td>
                                <td style="color:var(--emerald);font-weight:600">
                                    {{ $adjustment->cashew->product->name }}
                                </td>
                                <td style="text-align:right;color:var(--emerald);font-weight:600">
                                    {{ $adjustment->old_quantity }}
                                </td>
                                <td style="text-align:right;color:var(--emerald);font-weight:600">
                                    {{ $adjustment->new_quantity }}
                                </td>
                                <td style="text-align:right;color:var(--emerald);font-weight:600">
                                    Tsh {{ number_format((($adjustment->old_quantity) - ($adjustment->new_quantity)) * $adjustment->cashew->unit_price, 2) }}
                                </td>
                                <td style="color:var(--text-muted)">
                                    {{ $adjustment->adjustedBy->name }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;color:var(--text-muted);padding:32px">
                                    No adjustments in this period.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/chart.min.js') }}"></script>
        <script>
            Chart.defaults.font.family = "'DM Sans', sans-serif";
            Chart.defaults.color = '#64748b';

            new Chart(document.getElementById('salesTrendChart'), {
                type: 'line',
                data: {
                    labels: @json($dailySales->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))),
                    datasets: [{
                        label: 'Revenue (Tsh)',
                        data: @json($dailySales->pluck('total')),
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.07)',
                        fill: true, tension: 0.4, pointRadius: 4,
                        pointBackgroundColor: '#2563eb', borderWidth: 2.5,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: v => 'Tsh ' + v.toLocaleString() } }
                    }
                }
            });

            new Chart(document.getElementById('revenueBreakdownChart'), {
                type: 'doughnut',
                data: {
                    labels: ['COGS', 'Expenses', 'Net Profit'],
                    datasets: [{
                        data: [{{ $totalCogs }}, {{ $totalExpenses }}, {{ max($netProfit, 0) }}],
                        backgroundColor: ['#f43f5e', '#f59e0b', '#10b981'],
                        borderWidth: 0, hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true, cutout: '68%',
                    plugins: { legend: { position: 'bottom', labels: { padding: 16, font: { size: 12 } } } }
                }
            });
        </script>
    @endpush
@endsection
