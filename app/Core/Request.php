<?php

namespace App\Core;

class Request
{
    // GET parametresi
    public static function get(string $key, mixed $default = null): mixed
    {
        return isset($_GET[$key]) ? self::clean($_GET[$key]) : $default;
    }

    // POST parametresi
    public static function post(string $key, mixed $default = null): mixed
    {
        return isset($_POST[$key]) ? self::clean($_POST[$key]) : $default;
    }

    // GET veya POST
    public static function input(string $key, mixed $default = null): mixed
    {
        return self::post($key) ?? self::get($key, $default);
    }

    // Tum POST verisi
    public static function all(): array
    {
        return array_map([self::class, 'clean'], $_POST);
    }

    // JSON body
    public static function json(): array
    {
        $body = file_get_contents('php://input');
        return json_decode($body, true) ?? [];
    }

    // Dosya
    public static function file(string $key): ?array
    {
        return $_FILES[$key] ?? null;
    }

    // HTTP metodu
    public static function method(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    // Ajax istegi mi?
    public static function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    // HTTPS mi?
    public static function isSecure(): bool
    {
        return isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    }

    // IP adresi
    public static function ip(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = trim(explode(',', $_SERVER[$key])[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return '0.0.0.0';
    }

    // Mevcut URL
    public static function url(): string
    {
        $protocol = self::isSecure() ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    // URI
    public static function uri(): string
    {
        return $_SERVER['REQUEST_URI'] ?? '/';
    }

    // CSRF dogrula
    public static function verifyCsrf(): bool
    {
        $token = self::post('_csrf_token') ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        return Session::verifyCsrf($token);
    }

    // Temizle
    private static function clean(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map([self::class, 'clean'], $value);
        }
        return htmlspecialchars(trim((string) $value), ENT_QUOTES, 'UTF-8');
    }

    // Ham deger (temizlemeden)
    public static function raw(string $key): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? null;
    }
}
