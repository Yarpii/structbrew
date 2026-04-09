<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Database;

final class SecurityConfigService
{
    public function getString(string $path, string $fallback = ''): string
    {
        $value = $this->getScopedValue($path);
        if ($value !== null) {
            return (string) $value;
        }

        return (string) Config::get($path, $fallback);
    }

    public function getBool(string $path, bool $fallback = false): bool
    {
        $value = $this->getScopedValue($path);
        if ($value === null) {
            $value = Config::get($path, $fallback ? '1' : '0');
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    private function getScopedValue(string $path): ?string
    {
        try {
            $db = Database::getInstance();
            if (!$db->tableExists('configurations')) {
                return null;
            }

            $row = $db->table('configurations')
                ->where('path', $path)
                ->where('scope', 'global')
                ->where('scope_id', 0)
                ->first();

            if (!$row) {
                return null;
            }

            return array_key_exists('value', $row) ? (string) $row['value'] : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
