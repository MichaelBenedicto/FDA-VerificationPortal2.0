<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Verifies Cloudflare Turnstile tokens server-side.
 *
 * @see https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
 */
class TurnstileService
{
    /**
     * Validate a Turnstile response token with Cloudflare.
     *
     * Returns true only when Cloudflare confirms the token is valid. Any
     * network/parse failure returns false (fail-closed) so a broken verifier
     * cannot be used to bypass the challenge.
     *
     * @param  string|null  $token  The cf-turnstile-response value from the client.
     * @param  string|null  $remoteIp  The client IP (optional but recommended).
     */
    public function verify(?string $token, ?string $remoteIp = null): bool
    {
        $token = trim((string) $token);

        if ($token === '') {
            return false;
        }

        $secret = (string) config('turnstile.secret_key');

        if ($secret === '') {
            // Misconfiguration: no secret key. Fail closed and log it.
            Log::warning('Turnstile verification skipped: missing secret key.');

            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post(config('turnstile.verify_url'), array_filter([
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $remoteIp,
                ]));
        } catch (\Throwable $e) {
            Log::error('Turnstile verification request failed', [
                'exception' => $e->getMessage(),
            ]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Turnstile verification returned non-2xx', [
                'status' => $response->status(),
            ]);

            return false;
        }

        $data = $response->json();

        return is_array($data) && ($data['success'] ?? false) === true;
    }
}
