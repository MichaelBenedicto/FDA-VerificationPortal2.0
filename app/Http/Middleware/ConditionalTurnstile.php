<?php

namespace App\Http\Middleware;

use App\Services\TurnstileService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Conditional Cloudflare Turnstile challenge for the public search endpoint.
 *
 * Design goals for this government public-verification portal:
 *  - Normal human users are NEVER challenged on their first searches.
 *  - Verified search engines (Googlebot/Bingbot) are never challenged.
 *  - Only clients that exceed a generous soft threshold within a short window
 *    are asked to solve a one-click (often invisible) Turnstile challenge.
 *  - Solving the challenge "clears" the IP for a configurable window.
 *
 * When a challenge is required the request is NOT hard-blocked; instead it
 * returns HTTP 428 with { "challenge_required": true } so the frontend can
 * render the widget and retry with a token.
 */
class ConditionalTurnstile
{
    public function __construct(private TurnstileService $turnstile)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        // Feature flag — allows disabling without touching routes.
        if (! config('turnstile.enabled', true)) {
            return $next($request);
        }

        $ip = (string) $request->ip();

        // 1. Verified legitimate crawlers bypass the challenge entirely.
        if ($this->isVerifiedCrawler($request)) {
            return $next($request);
        }

        // 2. If this IP recently solved a challenge, let it through.
        if (Cache::get($this->clearedKey($ip))) {
            return $next($request);
        }

        // 3. If the client sent a Turnstile token, verify it now.
        $token = $request->input('cf-turnstile-response')
            ?? $request->header('CF-Turnstile-Response');

        if (! empty($token)) {
            if ($this->turnstile->verify($token, $ip)) {
                // Clear this IP for the configured window and reset its counter.
                Cache::put(
                    $this->clearedKey($ip),
                    true,
                    now()->addSeconds((int) config('turnstile.clearance_ttl', 1800))
                );
                Cache::forget($this->countKey($ip));

                return $next($request);
            }

            // Token present but invalid — keep challenging.
            return $this->challengeResponse('Verification failed. Please try again.');
        }

        // 4. No token: count this request and decide whether to challenge.
        $count = $this->incrementCount($ip);

        if ($count > (int) config('turnstile.challenge_threshold', 20)) {
            return $this->challengeResponse();
        }

        return $next($request);
    }

    /**
     * Build the 428 response that tells the frontend to render Turnstile.
     */
    protected function challengeResponse(?string $message = null)
    {
        return response()->json([
            'challenge_required' => true,
            'site_key' => config('turnstile.site_key'),
            'message' => $message
                ?? 'Automated access detected. Please complete the verification to continue.',
        ], 428);
    }

    /**
     * Increment and return the per-IP search count within the decay window.
     */
    protected function incrementCount(string $ip): int
    {
        $key = $this->countKey($ip);
        $window = (int) config('turnstile.challenge_window', 300);

        // Seed the key with its TTL on first hit so the window actually decays.
        if (! Cache::has($key)) {
            Cache::put($key, 0, now()->addSeconds($window));
        }

        return (int) Cache::increment($key);
    }

    protected function countKey(string $ip): string
    {
        return 'turnstile:count:'.md5($ip);
    }

    protected function clearedKey(string $ip): string
    {
        return 'turnstile:cleared:'.md5($ip);
    }

    /**
     * Verify a client claiming to be Googlebot/Bingbot via reverse + forward
     * DNS, so a spoofed User-Agent string cannot bypass the challenge.
     */
    protected function isVerifiedCrawler(Request $request): bool
    {
        $ua = (string) $request->userAgent();

        if (! preg_match('/googlebot|bingbot|google\.com\/bot/i', $ua)) {
            return false;
        }

        $ip = $request->ip();
        $host = gethostbyaddr($ip);

        if (! $host || $host === $ip) {
            return false;
        }

        // Host must belong to an official crawler domain.
        if (! preg_match('/\.(googlebot\.com|google\.com|search\.msn\.com)$/i', $host)) {
            return false;
        }

        // Forward-confirm: the hostname must resolve back to the same IP.
        return in_array($ip, gethostbynamel($host) ?: [], true);
    }
}
