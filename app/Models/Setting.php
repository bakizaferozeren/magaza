<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;

class Setting
{
    private static array $cache = [];

    // Tek ayar getir
    public static function get(string $key, mixed $default = null): mixed
    {
        // Bellek cache
        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $value = Cache::remember("setting_{$key}", 3600, function() use ($key) {
            return Database::value("SELECT `value` FROM settings WHERE `key` = ?", [$key]);
        });

        $result = $value ?? $default;
        self::$cache[$key] = $result;
        return $result;
    }

    // Tum ayarlari getir
    public static function all(): array
    {
        return Cache::remember('settings_all', 3600, function() {
            $rows = Database::rows("SELECT `key`, `value` FROM settings");
            $result = [];
            foreach ($rows as $row) {
                $result[$row['key']] = $row['value'];
            }
            return $result;
        });
    }

    // Ayar kaydet
    public static function set(string $key, mixed $value): void
    {
        Database::query(
            "INSERT INTO settings (`key`, `value`) VALUES (?, ?)
             ON DUPLICATE KEY UPDATE `value` = ?",
            [$key, $value, $value]
        );

        // Cache temizle
        self::$cache[$key] = $value;
        Cache::delete("setting_{$key}");
        Cache::delete('settings_all');
    }

    // Toplu kaydet
    public static function setMany(array $data): void
    {
        Database::beginTransaction();
        try {
            foreach ($data as $key => $value) {
                self::set($key, $value);
            }
            Database::commit();
        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }
}
