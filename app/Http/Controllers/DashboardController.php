<?php

namespace App\Http\Controllers;

use App\Models\Cashew;
use App\Models\Expense;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        $productCount = Cashew::count();
        $availableStockUnits = (int) Cashew::where('status', 'available')->sum('quantity');
        $inventoryValue = (float) (Cashew::where('status', 'available')
            ->selectRaw('SUM(CAST(unit_price AS REAL) * CAST(quantity AS REAL)) as total_value')
            ->value('total_value') ?? 0);

        $monthlySales = (float) Sale::whereBetween('sale_date', [$monthStart, $monthEnd])
            ->sum('final_amount');

        $monthlyExpenses = (float) Expense::whereBetween('expense_date', [
            $monthStart->toDateString(),
            $monthEnd->toDateString(),
        ])->sum('amount');

        $monthlyItems = SaleItem::whereHas('sale', function ($query) use ($monthStart, $monthEnd) {
            $query->whereBetween('sale_date', [$monthStart, $monthEnd]);
        })->get();

        $monthlyCogs = (float) $monthlyItems->sum(function ($item) {
            return (float) ($item->cashews->unit_price ?? 0) * (int) $item->quantity;
        });
        $grossProfit = $monthlySales - $monthlyCogs;
        $netProfit = $grossProfit - $monthlyExpenses;
        $profitMarginPercentage = $monthlySales > 0 ? ($netProfit / $monthlySales) * 100 : 0;

        $activeInstallmentPlans = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        $pendingInstallmentsAmount = (float) $activeInstallmentPlans->sum(function ($plan) {
            $saleTotal = (float) optional($plan->sale)->final_amount;
            $paid = (float) $plan->installmentPayments->sum('amount_paid');

            return max($saleTotal - $paid, 0);
        });

        $salesData = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total_sales')
        )
            ->where('sale_date', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesChartLabels = $salesData->pluck('date')
            ->map(fn ($date) => Carbon::parse($date)->format('j M'))
            ->values()
            ->toArray();
        $salesChartData = $salesData->pluck('total_sales')
            ->map(fn ($value) => (float) $value)
            ->values()
            ->toArray();

        $inventoryDistribution = Cashew::where('status', 'available')
            ->select('product_id', DB::raw('SUM(CAST(quantity AS REAL)) as count'))
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('count')
            ->get();

        $inventoryChartLabels = $inventoryDistribution
            ->map(fn ($stock) => optional($stock->product)->name ?? 'Unknown')
            ->values()
            ->toArray();
        $inventoryChartData = $inventoryDistribution
            ->map(fn ($stock) => (int) $stock->count)
            ->values()
            ->toArray();

        $lowStockProducts = Cashew::with('product')
            ->where('status', 'available')
            ->whereRaw('CAST(quantity AS INTEGER) <= low_stock_threshold')
            ->orderBy('quantity')
            ->get();

        $notificationCount = $lowStockProducts->count();

        $recentSales = Sale::with('saleItems.product')
            ->latest('sale_date')
            ->take(5)
            ->get()
            ->map(function ($sale) {
                $items = $sale->saleItems
                    ->map(fn ($item) => (optional($item->product)->name ?? 'Unknown') . ' x ' . $item->quantity)
                    ->implode(', ');

                return [
                    'type' => 'sale',
                    'description' => "{$items} sold to " . ($sale->customer_name ?: 'Walk-in customer') . ' - Tsh ' . number_format($sale->final_amount, 2),
                    'date' => $sale->sale_date,
                    'link' => route('sales.show', $sale->id),
                ];
            });

        $recentReceivedStock = Cashew::with('product')
            ->latest('received_at')
            ->take(5)
            ->get()
            ->map(fn ($stock) => [
                'type' => 'received',
                'description' => (optional($stock->product)->name ?? 'Unknown') . ' received - ' . number_format($stock->quantity) . ' units',
                'date' => $stock->received_at ?? $stock->created_at,
                'link' => route('cashews.index'),
            ]);

        $recentInstallmentPayments = InstallmentPayment::with('installmentPlan.sale')
            ->latest('payment_date')
            ->take(5)
            ->get()
            ->map(fn ($payment) => [
                'type' => 'payment',
                'description' => 'Installment payment received - Tsh ' . number_format($payment->amount_paid, 2),
                'date' => $payment->payment_date,
                'link' => $payment->installmentPlan?->sale
                    ? route('sales.show', $payment->installmentPlan->sale->id)
                    : route('installments.index'),
            ]);

        $recentActivities = collect()
            ->concat($recentSales)
            ->concat($recentReceivedStock)
            ->concat($recentInstallmentPayments)
            ->sortByDesc('date')
            ->take(8)
            ->values();

        return view('dashboard', [
            'product_count' => $productCount,
            'totalProducts' => $productCount,
            'availableStockUnits' => $availableStockUnits,
            'inventoryValue' => $inventoryValue,
            'monthlySales' => $monthlySales,
            'monthlyExpenses' => $monthlyExpenses,
            'monthlyCogs' => $monthlyCogs,
            'netProfit' => $netProfit,
            'pendingInstallmentsAmount' => $pendingInstallmentsAmount,
            'profitMarginPercentage' => $profitMarginPercentage,
            'salesChartLabels' => $salesChartLabels,
            'salesChartData' => $salesChartData,
            'inventoryChartLabels' => $inventoryChartLabels,
            'inventoryChartData' => $inventoryChartData,
            'lowStockProducts' => $lowStockProducts,
            'notificationCount' => $notificationCount,
            'recentActivities' => $recentActivities,
        ]);
    }
}
