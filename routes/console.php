<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\CheckLowStock;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('stock:check-low', function () {
    // Instantiate and call the handle method of your command
    (new CheckLowStock())->handle();
    $this->info('Stock check initiated from console routes.');
})->purpose('Checks for low stock levels and sends SMS notifications.');

Schedule::command('stock:check-low')->everyMinute();


