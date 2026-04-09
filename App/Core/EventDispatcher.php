<?php

declare(strict_types=1);

namespace App\Core;

class EventDispatcher
{
    private static array $listeners = [];

    public static function listen(string $event, callable $listener, int $priority = 0): void
    {
        self::$listeners[$event][] = [
            'callback' => $listener,
            'priority' => $priority,
        ];
    }

    public static function dispatch(string $event, mixed $data = null): mixed
    {
        if (!isset(self::$listeners[$event])) return $data;

        // Sort by priority (higher first)
        usort(self::$listeners[$event], fn($a, $b) => $b['priority'] <=> $a['priority']);

        foreach (self::$listeners[$event] as $listener) {
            $result = ($listener['callback'])($data);
            if ($result !== null) {
                $data = $result;
            }
        }

        return $data;
    }

    public static function hasListeners(string $event): bool
    {
        return !empty(self::$listeners[$event]);
    }

    public static function forget(string $event): void
    {
        unset(self::$listeners[$event]);
    }
}
