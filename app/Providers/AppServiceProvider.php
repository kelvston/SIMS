<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if ($this->app->runningUnitTests()) {
                $view->with('settings', collect());

                return;
            }

            $settings = Cache::remember('settings.all', now()->addMinutes(10), function () {
                return Setting::pluck('value', 'key');
            });

            $view->with('settings', $settings);
        });
    }
}
