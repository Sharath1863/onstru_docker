<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // API broadcast routes (Sanctum token auth)
        Broadcast::routes([
            'prefix' => 'api',                 // Optional: /api/broadcasting/auth
            'middleware' => ['auth:sanctum'],  // Use Sanctum token auth
        ]);

        // Register broadcast routes with your custom middleware
        Broadcast::routes(['middleware' => [\App\Http\Middleware\CheckUserAuth::class]]);

        // Load channel authorization callbacks
        require base_path('routes/channels.php');
    }
}
