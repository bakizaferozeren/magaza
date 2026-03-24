<?php

namespace App\Core;

class Cache
{
    private static string $dir = '';

    private static function dir(): string
    {
        if (!self::$dir) {
            self::$dir = STR_PATH . '/cache/';
            if (!is_dir(self::$dir)) {
                mkdir(self::$dir, 0755, true);
            }
        }
        return self::$dir;
    }

    private static function path(string $key): string
    {
        return self::dir() . md5($key) . '.cache';
    }

    // Cache'e yaz
    public static function set(string $key, mixed $value, int $seconds = 3600): void
    {
        if (!self::isEnabled()) return;

        $data = [
            'expires' => time() + $seconds,
            'value'   => $value,
        ];

        file_put_contents(self::path($key), serialize($data), LOCK_EX);
    }

    // Cache'den oku
    public static function get(string $key): mixed
    {
        if (!self::isEnabled()) return null;

        $path = self::path($key);
        if (!file_exists($path)) return null;

        $data = unserialize(file_get_contents($path));
        if (!$data) return null;

        if ($data['expires'] < time()) {
            unlink($path);
            return null;
        }

        return $data['value'];
    }

    // Cache var mi?
    public static function has(string $key): bool
    {
        return self::get($key) !== null;
    }

    // Cache sil
    public static function delete(string $key): void
    {
        $path = self::path($key);
        if (file_exists($path)) unlink($path);
    }

    // Tum cache temizle
    public static function flush(): int
    {
        $count = 0;
        foreach (glob(self::dir() . '*.cache') as $file) {
            unlink($file);
            $count++;
        }
        return $count;
    }

    // Remember - cache yoksa callback calistir
    public static function remember(string $key, int $seconds, callable $callback): mixed
    {
        $cached = self::get($key);
        if ($cached !== null) return $cached;

        $value = $callback();
        self::set($key, $value, $seconds);
        return $value;
    }

    private static function isEnabled(): bool
    {
        return ($_ENV['CACHE_ENABLED'] ?? 'true') === 'true';
    }
}
