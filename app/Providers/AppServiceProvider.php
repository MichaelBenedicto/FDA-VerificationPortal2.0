<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
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
        $this->configureRateLimiters();
    }

    /**
     * Register named rate limiters used by the "throttle" middleware.
     *
     * These provide application-layer (Layer 7) protection against abuse and
     * high-volume request floods. Network-layer (Layer 3/4) DDoS must still be
     * handled at the infrastructure edge (CDN/WAF + reverse-proxy limits).
     */
    protected function configureRateLimiters(): void
    {
        // Public, unauthenticated, and expensive: the /api/search endpoint fans
        // out across ~30 database connections per request, so keep this tight.
        RateLimiter::for('search', function (Request $request) {
            return Limit::perMinute(15)->by($request->ip())->response(function () {
                return response()->json([
                    'error' => 'Too many requests. Please slow down and try again shortly.',
                ], 429);
            });
        });

        // Login: limit brute-force / credential-stuffing per IP + username.
        RateLimiter::for('login', function (Request $request) {
            $username = (string) $request->input('userName');

            return Limit::perMinute(5)->by($request->ip().'|'.mb_strtolower($username))
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts. Please try again in a minute.',
                    ], 429);
                });
        });
    }
}
