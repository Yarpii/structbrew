<?php
declare(strict_types=1);

namespace App\Services;

final class TurnstileService
{
    private SecurityConfigService $config;

    public function __construct()
    {
        $this->config = new SecurityConfigService();
    }

    public function siteKey(): string
    {
        return trim($this->config->getString('security.turnstile_site_key', ''));
    }

    public function isEnabled(): bool
    {
        return $this->config->getBool('security.turnstile_enabled', true)
            && $this->siteKey() !== ''
            && $this->secretKey() !== '';
    }

    public function verify(?string $token, ?string $ipAddress = null): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        $token = trim((string) $token);
        if ($token === '') {
            return false;
        }

        $payload = [
            'secret' => $this->secretKey(),
            'response' => $token,
        ];

        if (!empty($ipAddress)) {
            $payload['remoteip'] = $ipAddress;
        }

        $response = $this->requestVerification($payload);
        if ($response === null) {
            return false;
        }

        $decoded = json_decode($response, true);
        if (!is_array($decoded)) {
            return false;
        }

        return (bool) ($decoded['success'] ?? false);
    }

    private function secretKey(): string
    {
        return trim($this->config->getString('security.turnstile_secret_key', ''));
    }

    private function requestVerification(array $payload): ?string
    {
        $endpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            if ($ch === false) {
                return null;
            }

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => http_build_query($payload),
            ]);

            $response = curl_exec($ch);
            $statusCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return $statusCode === 200 && is_string($response) ? $response : null;
        }

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($payload),
                'timeout' => 10,
            ],
        ]);

        $response = @file_get_contents($endpoint, false, $context);
        return is_string($response) && $response !== '' ? $response : null;
    }
}
