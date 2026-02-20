<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\InstallmentPayment;
use App\Models\InstallmentPlan;
use App\Models\Phone;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPhones = Phone::where('status', 'available')->count();

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Monthly Sales
        $monthlySales = Sale::whereMonth('sale_date', $currentMonth)
            ->whereYear('sale_date', $currentYear)
            ->sum('final_amount');

        // Pending Installments
        $pendingInstallmentsAmount = 0;
        $activeInstallmentPlans = InstallmentPlan::where('status', 'active')
            ->with(['sale', 'installmentPayments'])
            ->get();

        foreach ($activeInstallmentPlans as $plan) {
            $totalPaid = $plan->installmentPayments->sum('amount_paid');
            $remainingAmount = $plan->sale->final_amount - $totalPaid;
            if ($remainingAmount > 0) {
                $pendingInstallmentsAmount += $remainingAmount;
            }
        }

        // Profit Margin
        $totalRevenueThisMonth = $monthlySales;
        $totalCogsThisMonth = 0;

        $soldPhonesThisMonth = SaleItem::whereHas('sale', function ($query) use ($currentMonth, $currentYear) {
            $query->whereMonth('sale_date', $currentMonth)
                ->whereYear('sale_date', $currentYear);
        })->with('phone')->get();

        foreach ($soldPhonesThisMonth as $saleItem) {
            if ($saleItem->phone) {
                $totalCogsThisMonth += $saleItem->phone->purchase_price;
            }
        }

        $grossProfit = $totalRevenueThisMonth - $totalCogsThisMonth;
        $profitMarginPercentage = $totalRevenueThisMonth > 0 ? ($grossProfit / $totalRevenueThisMonth) * 100 : 0;

        // Sales chart (last 30 days)
        $salesData = Sale::select(
            DB::raw('DATE(sale_date) as date'),
            DB::raw('SUM(final_amount) as total_sales')
        )
            ->where('sale_date', '>=', Carbon::now()->subDays(30)->startOfDay())
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $salesChartLabels = $salesData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('j M'))->toArray();
        $salesChartData = $salesData->pluck('total_sales')->toArray();

        // Inventory chart
        $inventoryDistribution = Phone::where('status', 'available')
            ->select('brand_id', DB::raw('count(*) as count'))
            ->with('brand')
            ->groupBy('brand_id')
            ->get();

        $inventoryChartLabels = $inventoryDistribution->pluck('brand.name')->toArray();
        $inventoryChartData = $inventoryDistribution->pluck('count')->toArray();

        // Low stock products
        $lowStockProducts = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->with('brand')
            ->get();

        // Notifications count
        $notificationCount = $lowStockProducts->count();

        // Recent Activities
        $recentSales = Sale::with('saleItems.phone.brand')->latest('sale_date')->take(5)->get()->map(function($sale) {
            $phoneNames = $sale->saleItems->map(fn($item) => $item->phone->brand->name . ' ' . $item->phone->model)->implode(', ');
            return [
                'type' => 'sale',
                'description' => "✔️ {$phoneNames} sold to {$sale->customer_name} - $" . number_format($sale->final_amount, 2),
                'date' => $sale->sale_date,
                'link' => route('sales.show', $sale->id)
            ];
        });

        $recentReceivedPhones = Phone::with('brand')->latest('received_at')->take(5)->get()->map(function($phone) {
            return [
                'type' => 'received',
                'description' => "📦 1 {$phone->brand->name} {$phone->model} ({$phone->color}) received (IMEI: {$phone->imei})",
                'date' => $phone->received_at,
                'link' => route('phones.index')
            ];
        });

        $recentInstallmentPayments = InstallmentPayment::with('installmentPlan.sale.saleItems.phone')
            ->latest('payment_date')->take(5)->get()->map(function($payment) {
                $phoneName = $payment->installmentPlan?->sale?->saleItems->first()?->phone->brand->name ?? 'N/A';
                $phoneModel = $payment->installmentPlan?->sale?->saleItems->first()?->phone->model ?? '';
                return [
                    'type' => 'payment',
                    'description' => "💵 Installment payment received for {$phoneName} {$phoneModel} - $" . number_format($payment->amount_paid, 2),
                    'date' => $payment->payment_date,
                    'link' => route('sales.show', $payment->installmentPlan->sale->id)
                ];
            });

        $recentActivities = collect()
            ->concat($recentSales)
            ->concat($recentReceivedPhones)
            ->concat($recentInstallmentPayments)
            ->sortByDesc('date')
            ->take(8);

        return view('dashboard', compact(
            'totalPhones',
            'monthlySales',
            'pendingInstallmentsAmount',
            'profitMarginPercentage',
            'salesChartLabels',
            'salesChartData',
            'inventoryChartLabels',
            'inventoryChartData',
            'lowStockProducts',
            'notificationCount',
            'recentActivities'
        ));
    }

    // Optional: Edit phone action
    public function editPhone(Phone $phone)
    {
        $this->authorize('edit phones');
        $brands = Brand::all(); // Fetch all brands
        return view('phones.edit', compact('phone', 'brands'));
    }

    // Optional: Delete phone action
    public function deletePhone(Phone $phone)
    {
        $this->authorize('delete phones');
        $phone->delete();
        return redirect()->back()->with('success', 'Phone deleted successfully');
    }

    public function updatePhone(Request $request, Phone $phone)
    {
        $this->authorize('edit phones');

        $validated = $request->validate([
            'brand_id' => 'required|exists:brands,id',
            'model' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'storage_capacity' => 'required|string|max:255',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'imei' => 'required|string|max:255|unique:phones,imei,' . $phone->id,
        ]);

        $phone->update($validated);

        return redirect()->back()->with('success', 'Phone updated successfully');
    }
}
