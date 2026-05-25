<?php

namespace App\Console\Commands;

use App\Models\Cashew;
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

        $lowStockItems = Cashew::with('product')
            ->where('status', 'available')
            ->whereRaw('CAST(quantity AS INTEGER) <= low_stock_threshold')
            ->orderBy('quantity')
            ->get();

        if ($lowStockItems->isEmpty()) {
            Log::info('[CheckLowStock] No low stock items found.');
            return Command::SUCCESS;
        }

        $alerts = [];

        foreach ($lowStockItems as $item) {
            $productName = optional($item->product)->name ?? 'Unknown product';
            $message = "Low Stock Alert! Product: {$productName}. Current stock: {$item->quantity}, Threshold: {$item->low_stock_threshold}.";
            Log::warning("[CheckLowStock] {$message}");
            $alerts[] = $message;
        }

        // Cache all alerts together (optional: join as a single string or keep array)
        Cache::put('low_stock_alerts', $alerts, now()->addMinutes(5));

        Log::info('[CheckLowStock] Low stock check complete.');
        return Command::SUCCESS;
    }
}
