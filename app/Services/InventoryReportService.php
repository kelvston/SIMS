<?php

namespace App\Services;

use App\Models\Cashew;
use App\Models\Expense;
use App\Models\Sale;
use Carbon\CarbonInterface;

class InventoryReportService
{
    public function build(CarbonInterface $startDate, CarbonInterface $endDate): array
    {
        $sales = Sale::query()
            ->with(['saleItems.product', 'saleItems.cashews'])
            ->whereDate('sale_date', '>=', $startDate->toDateString())
            ->whereDate('sale_date', '<=', $endDate->toDateString())
            ->get();

        $soldProducts = $sales
            ->flatMap(function (Sale $sale) {
                $revenueFactor = (float) $sale->total_amount > 0
                    ? (float) $sale->final_amount / (float) $sale->total_amount
                    : 1;

                return $sale->saleItems->map(function ($item) use ($revenueFactor) {
                    $quantity = (int) $item->quantity;
                    $revenue = (float) $item->unit_price * $quantity * $revenueFactor;
                    $unitCost = $item->unit_cost !== null
                        ? (float) $item->unit_cost
                        : (float) ($item->cashews?->unit_price ?? 0);

                    return [
                        'product_id' => $item->product_id,
                        'name' => $item->product?->name ?? 'Unknown product',
                        'quantity' => $quantity,
                        'revenue' => $revenue,
                        'cost' => $unitCost * $quantity,
                    ];
                });
            })
            ->groupBy('product_id')
            ->map(function ($items) {
                $quantity = (int) $items->sum(fn ($item) => (int) $item['quantity']);
                $revenue = (float) $items->sum('revenue');
                $cost = (float) $items->sum('cost');

                return [
                    'name' => $items->first()['name'],
                    'quantity' => $quantity,
                    'revenue' => $revenue,
                    'profit' => $revenue - $cost,
                ];
            })
            ->sortByDesc('quantity')
            ->values()
            ->all();

        $receivedProducts = Cashew::query()
            ->with('product')
            ->where(function ($query) use ($startDate, $endDate) {
                $periodStart = $startDate->copy()->startOfDay();
                $periodEnd = $endDate->copy()->endOfDay();

                $query->whereBetween('received_at', [$periodStart, $periodEnd])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->whereNull('received_at')
                            ->whereBetween('created_at', [
                                $startDate->copy()->startOfDay(),
                                $endDate->copy()->endOfDay(),
                            ]);
                    });
            })
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                return [
                    'name' => $items->first()->product?->name ?? 'Unknown product',
                    'quantity' => (int) $items->sum(fn ($item) => (int) $item->quantity),
                    'cost' => (float) $items->sum(fn ($item) => (float) $item->unit_price * (int) $item->quantity),
                ];
            })
            ->sortByDesc('quantity')
            ->values()
            ->all();

        $revenue = (float) $sales->sum('final_amount');
        $costOfGoodsSold = (float) collect($soldProducts)->sum('revenue') - (float) collect($soldProducts)->sum('profit');
        $expenses = (float) Expense::query()
            ->whereDate('expense_date', '>=', $startDate->toDateString())
            ->whereDate('expense_date', '<=', $endDate->toDateString())
            ->sum('amount');
        $grossProfit = $revenue - $costOfGoodsSold;

        return [
            'period' => $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y'),
            'sold_products' => $soldProducts,
            'received_products' => $receivedProducts,
            'sold_quantity' => (int) collect($soldProducts)->sum('quantity'),
            'received_quantity' => (int) collect($receivedProducts)->sum('quantity'),
            'revenue' => $revenue,
            'cost_of_goods_sold' => $costOfGoodsSold,
            'gross_profit' => $grossProfit,
            'expenses' => $expenses,
            'net_profit' => $grossProfit - $expenses,
        ];
    }
}
