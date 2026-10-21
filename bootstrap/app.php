<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',   // API routes enabled
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    //API TOKEN
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'api.token' => \App\Http\Middleware\StaticApiToken::class,
        ]);

        // Trust the reverse proxy (Cloudflare -> Apache) so $request->ip()
        // returns the REAL client IP from the forwarded headers instead of
        // the local connection IP (127.0.0.1). Without this, every visitor
        // collapses to one IP and shares a single rate-limit bucket.
        //
        // SECURITY: trusting '*' is only safe because the origin must accept
        // connections ONLY from Cloudflare (lock the firewall/Security Group
        // to Cloudflare's published IP ranges). Otherwise a client reaching
        // the origin directly could spoof these headers.
        $middleware->trustProxies(
            at: '*',
            headers: Request::HEADER_X_FORWARDED_FOR
                | Request::HEADER_X_FORWARDED_HOST
                | Request::HEADER_X_FORWARDED_PORT
                | Request::HEADER_X_FORWARDED_PROTO
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {

    })
    ->create();