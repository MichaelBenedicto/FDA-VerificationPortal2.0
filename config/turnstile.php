<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudflare Turnstile
    |--------------------------------------------------------------------------
    |
    | Conditional CAPTCHA for the public /api/search endpoint. The challenge is
    | only presented to clients that look suspicious (e.g. they exceeded the
    | soft request threshold); normal human users and verified search-engine
    | crawlers are never prompted.
    |
    | Get real keys from: https://dash.cloudflare.com/ > Turnstile.
    |
    | The defaults below are Cloudflare's official TEST keys, which are safe for
    | local development: the site key renders a widget that always passes and
    | the secret key always verifies as valid. Replace them in production via
    | the TURNSTILE_SITE_KEY / TURNSTILE_SECRET_KEY env vars.
    |
    */

    // Public key rendered in the browser widget.
    'site_key' => env('TURNSTILE_SITE_KEY', '1x00000000000000000000AA'),

    // Secret key used for server-side verification. Never expose to the client.
    'secret_key' => env('TURNSTILE_SECRET_KEY', '1x0000000000000000000000000000000AA'),

    // Cloudflare's server-side verification endpoint.
    'verify_url' => env(
        'TURNSTILE_VERIFY_URL',
        'https://challenges.cloudflare.com/turnstile/v0/siteverify'
    ),

    /*
    | Soft threshold: how many searches a single IP may make within the decay
    | window before a Turnstile challenge is required. Kept generous so real
    | humans effectively never hit it.
    */
    'challenge_threshold' => (int) env('TURNSTILE_CHALLENGE_THRESHOLD', 20),

    // Window (seconds) over which the search count above is accumulated.
    'challenge_window' => (int) env('TURNSTILE_CHALLENGE_WINDOW', 300),

    /*
    | Once an IP solves a challenge it is "cleared" for this many seconds and
    | will not be challenged again during that period.
    */
    'clearance_ttl' => (int) env('TURNSTILE_CLEARANCE_TTL', 1800),

    // Master switch so the whole feature can be disabled without code changes.
    'enabled' => (bool) env('TURNSTILE_ENABLED', true),

];
