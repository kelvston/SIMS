<?php

namespace App\Console\Commands;

use App\Models\StockLevel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckLowStock extends Command
{
    protected $signature = 'stock:check-low';
    protected $description = 'Checks for low stock levels and caches alerts for UI display.';

    public function handle(): int
    {
        Log::info('[CheckLowStock] Starting low stock check...');

        $lowStockItems = StockLevel::whereColumn('current_stock', '<=', 'low_stock_threshold')
            ->join('phones', 'phones.brand_id', '=', 'stock_levels.brand_id')
            ->with('brand')->where('status','=','available')
            ->get();

        if ($lowStockItems->isEmpty()) {
            Log::info('[CheckLowStock] No low stock items found.');
            return Command::SUCCESS;
        }

        $alerts = [];

        foreach ($lowStockItems as $item) {
            $message = "Low Stock Alert! Phone: {$item->brand->name} {$item->model} ({$item->color}). Current stock: {$item->current_stock}, Threshold: {$item->low_stock_threshold}.";
            Log::warning("[CheckLowStock] {$message}");
            $alerts[] = $message;
        }

        // Cache all alerts together
        Cache::put('low_stock_alerts', $alerts, now()->addMinutes(5));

        Log::info('[CheckLowStock] Low stock check complete.');
        return Command::SUCCESS;
    }
}
