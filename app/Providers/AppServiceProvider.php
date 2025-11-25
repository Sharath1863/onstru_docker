<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;
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
        if (env('APP_ENV') == 'production') { // Only run this when not on your local machine
            URL::forceScheme('https');
        }

        $this->app->register(RouteServiceProvider::class);

        Blade::directive('log', function ($expression) {
            return "<?php \\Log::info('Blade Log:', is_array($expression) ? $expression : [$expression]); ?>";
        });
    }
}
