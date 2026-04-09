<?php
declare(strict_types=1);

namespace App\Services;

final class TwoFactorService
{
    public function isFeatureEnabled(): bool
    {
        return (new SecurityConfigService())->getBool('security.customer_two_factor_enabled', true);
    }

    public function generateSecret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $max = strlen($alphabet) - 1;
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, $max)];
        }

        return $secret;
    }

    public function provisioningUri(string $issuer, string $accountLabel, string $secret): string
    {
        $issuer = trim($issuer);
        $accountLabel = trim($accountLabel);
        $label = rawurlencode($issuer . ':' . $accountLabel);

        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            $label,
            rawurlencode($secret),
            rawurlencode($issuer)
        );
    }

    public function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $secret = trim($secret);
        $code = preg_replace('/\D+/', '', $code) ?? '';

        if ($secret === '' || strlen($code) !== 6) {
            return false;
        }

        $timeSlice = (int) floor(time() / 30);
        for ($offset = -$window; $offset <= $window; $offset++) {
            if (hash_equals($this->totp($secret, $timeSlice + $offset), $code)) {
                return true;
            }
        }

        return false;
    }

    private function totp(string $secret, int $timeSlice): string
    {
        $key = $this->base32Decode($secret);
        if ($key === '') {
            return '';
        }

        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $key, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $truncated = substr($hash, $offset, 4);

        $value = unpack('N', $truncated)[1] & 0x7FFFFFFF;
        $otp = (string) ($value % 1000000);

        return str_pad($otp, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode(string $secret): string
    {
        $secret = strtoupper(trim($secret));
        $secret = str_replace('=', '', $secret);

        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';

        for ($i = 0, $len = strlen($secret); $i < $len; $i++) {
            $position = strpos($alphabet, $secret[$i]);
            if ($position === false) {
                continue;
            }
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $binary = '';
        for ($i = 0, $len = strlen($bits); $i + 8 <= $len; $i += 8) {
            $binary .= chr(bindec(substr($bits, $i, 8)));
        }

        return $binary;
    }
}
