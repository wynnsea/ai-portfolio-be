<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        $tooManyAttempts = fn (): JsonResponse => response()->json([
            'message' => 'Terlalu banyak percobaan. Coba lagi dalam beberapa saat.',
        ], 429);

        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by($request->ip())->response($tooManyAttempts));
        RateLimiter::for('messages', fn (Request $request) => Limit::perMinute(3)->by($request->ip())->response($tooManyAttempts));
        RateLimiter::for('track', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
    }
}
