<?php

namespace App\Services;

use App\Models\Cashew;
use App\Models\Expense;
use App\Models\InstallmentPlan;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockAdjustment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class GeneralReportService
{
    public function buildData(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate ??= Carbon::now()->startOfMonth()->toDateString();
        $endDate ??= Carbon::now()->toDateString();

        $salesQuery = Sale::query()
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate);

        $totalRevenue = (clone $salesQuery)->sum('final_amount');
        $totalDiscounts = (clone $salesQuery)->sum('discount_amount');
        $totalSalesCount = (clone $salesQuery)->count();
        $installmentSales = (clone $salesQuery)->where('is_installment', true)->count();
        $fullPaymentSales = (clone $salesQuery)->where('is_installment', false)->count();

        $productCostMap = Cashew::query()
            ->select(
                'product_id',
                DB::raw('SUM(CAST(quantity AS REAL)) as stock_quantity'),
                DB::raw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as stock_cost')
            )
            ->groupBy('product_id')
            ->get()
            ->mapWithKeys(function ($stock) {
                $stockQuantity = (float) $stock->stock_quantity;
                $stockCost = (float) $stock->stock_cost;

                return [
                    $stock->product_id => $stockQuantity > 0 ? $stockCost / $stockQuantity : 0,
                ];
            });

        $soldItems = SaleItem::whereHas('sale', function ($q) use ($startDate, $endDate) {
            $q->whereDate('sale_date', '>=', $startDate)
                ->whereDate('sale_date', '<=', $endDate);
        })->with('product')->get();

        $totalCogs = $soldItems->sum(function ($item) use ($productCostMap) {
            $quantity = (int) $item->quantity;
            $unitCost = $item->unit_cost !== null
                ? (float) $item->unit_cost
                : (float) ($productCostMap[$item->product_id] ?? 0);

            return $unitCost * $quantity;
        });

        $expensesQuery = Expense::query()
            ->whereDate('expense_date', '>=', $startDate)
            ->whereDate('expense_date', '<=', $endDate);

        $totalExpenses = (clone $expensesQuery)->sum('amount');
        $expensesByCategory = (clone $expensesQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;
        $profitMargin = $totalRevenue > 0
            ? round(($netProfit / $totalRevenue) * 100, 2)
            : 0;

        $availableStockUnits = (int) Cashew::where('status', 'available')->sum('quantity');
        $soldUnitsInPeriod = (int) $soldItems->sum(fn ($item) => (int) $item->quantity);
        $inventoryValue = Cashew::where('status', 'available')
            ->selectRaw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as total_value')
            ->value('total_value') ?? 0;

        $stockByProduct = Cashew::where('status', 'available')
            ->select(
                'product_id',
                DB::raw('SUM(CAST(quantity AS REAL)) as count'),
                DB::raw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as value')
            )
            ->with('product')
            ->groupBy('product_id')
            ->get();

        $lowStockItems = Cashew::with('product')
            ->where('status', 'available')
            ->whereRaw('CAST(quantity AS INTEGER) <= low_stock_threshold')
            ->get();

        $activePlans = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        $pendingInstallments = 0;
        foreach ($activePlans as $plan) {
            $paid = $plan->installmentPayments->sum('amount_paid');
            $remaining = optional($plan->sale)->final_amount - $paid;
            if ($remaining > 0) {
                $pendingInstallments += $remaining;
            }
        }
        $activeInstallmentCount = $activePlans->count();

        $dailySales = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topProducts = $soldItems
            ->groupBy('product_id')
            ->map(function ($items) {
                $firstItem = $items->first();

                return (object) [
                    'product_name' => optional($firstItem->product)->name ?? 'Unknown',
                    'units_sold' => (int) $items->sum(fn ($item) => (int) $item->quantity),
                    'revenue' => (float) $items->sum(fn ($item) => (float) $item->unit_price * (int) $item->quantity),
                ];
            })
            ->sortByDesc('units_sold')
            ->take(5)
            ->values();

        $recentSales = Sale::with('saleItems.product')
            ->whereDate('sale_date', '>=', $startDate)
            ->whereDate('sale_date', '<=', $endDate)
            ->orderByDesc('sale_date')
            ->limit(10)
            ->get();

        $stockAdjustments = Schema::hasTable('stock_adjustments')
            ? StockAdjustment::with('cashew', 'cashew.product', 'adjustedBy')->get()
            : collect();

        return compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalDiscounts',
            'totalSalesCount',
            'installmentSales',
            'fullPaymentSales',
            'totalCogs',
            'totalExpenses',
            'expensesByCategory',
            'grossProfit',
            'netProfit',
            'profitMargin',
            'inventoryValue',
            'lowStockItems',
            'pendingInstallments',
            'activeInstallmentCount',
            'dailySales',
            'recentSales',
            'stockAdjustments'
        ) + [
            'availableStockUnits' => $availableStockUnits,
            'soldStockUnits' => $soldUnitsInPeriod,
            'stockByProduct' => $stockByProduct,
            'topProducts' => $topProducts,
        ];
    }

    public function pdfContent(array $data): string
    {
        return Pdf::loadView('reports.general_pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isRemoteEnabled' => false,
                'isHtml5ParserEnabled' => true,
            ])
            ->output();
    }

    public function filename(array $data): string
    {
        return 'general-report-' . $data['startDate'] . '-to-' . $data['endDate'] . '.pdf';
    }
}
