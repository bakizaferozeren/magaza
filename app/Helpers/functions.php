<?php

use App\Core\Session;
use App\Core\Auth;
use App\Core\Response;
use App\Models\Setting;

// ============================================
// URL & YON
// ============================================

function url(string $path = ''): string
{
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');
    return $base . '/' . ltrim($path, '/');
}

function adminUrl(string $path = ''): string
{
    $adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';
    return url($adminPath . '/' . ltrim($path, '/'));
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function uploadUrl(string $path): string
{
    return url('uploads/' . ltrim($path, '/'));
}

function redirect(string $url): void
{
    Response::redirect($url);
}

function back(): void
{
    Response::back();
}

// ============================================
// VIEW
// ============================================

function view(string $path, array $data = []): void
{
    Response::view($path, $data);
}

function partial(string $path, array $data = []): void
{
    extract($data);
    $file = APP_PATH . '/Views/' . str_replace('.', '/', $path) . '.php';
    if (file_exists($file)) require $file;
}

// ============================================
// SESSION & AUTH
// ============================================

function session(string $key, mixed $default = null): mixed
{
    return Session::get($key, $default);
}

function flash(string $key, mixed $value = null): mixed
{
    return Session::flash($key, $value);
}

function hasFlash(string $key): bool
{
    return Session::hasFlash($key);
}

function csrfToken(): string
{
    return Session::csrfToken();
}

function csrfField(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . Session::csrfToken() . '">';
}

function auth(): ?array
{
    return Auth::user();
}

function isLoggedIn(): bool
{
    return Auth::check();
}

function isAdmin(): bool
{
    return Auth::isAdmin();
}

// ============================================
// AYARLAR
// ============================================

function setting(string $key, mixed $default = null): mixed
{
    return Setting::get($key, $default);
}

function siteName(): string
{
    return Setting::get('site_name', 'Magazam');
}

function currencySymbol(): string
{
    $code = session('currency', Setting::get('currency_default', 'TRY'));
    $symbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
    return $symbols[$code] ?? '₺';
}

// ============================================
// GUVENLIK
// ============================================

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function clean(string $html): string
{
    return strip_tags($html, '<p><br><b><strong><i><em><ul><ol><li><h2><h3><h4><a><img>');
}

function generateToken(int $length = 32): string
{
    return bin2hex(random_bytes($length));
}

function generateOrderNo(): string
{
    return 'ORD-' . strtoupper(date('Ymd')) . '-' . strtoupper(substr(uniqid(), -6));
}

// ============================================
// TARIH & SAAT
// ============================================

function now(): string
{
    return date('Y-m-d H:i:s');
}

function today(): string
{
    return date('Y-m-d');
}

function formatDate(string $date, string $format = 'd.m.Y'): string
{
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime(string $date): string
{
    if (empty($date)) return '';
    return date('d.m.Y H:i', strtotime($date));
}

function timeAgo(string $date): string
{
    $diff = time() - strtotime($date);

    if ($diff < 60)     return $diff . ' saniye once';
    if ($diff < 3600)   return floor($diff / 60) . ' dakika once';
    if ($diff < 86400)  return floor($diff / 3600) . ' saat once';
    if ($diff < 604800) return floor($diff / 86400) . ' gun once';

    return formatDate($date);
}

// ============================================
// KARGO TARIHI HESAPLAMA
// ============================================

function calculateDeliveryDate(int $minDays, int $maxDays, bool $excludeWeekends = true): array
{
    $minDate = addBusinessDays(date('Y-m-d'), $minDays, $excludeWeekends);
    $maxDate = addBusinessDays(date('Y-m-d'), $maxDays, $excludeWeekends);

    return [
        'min'      => $minDate,
        'max'      => $maxDate,
        'min_text' => formatDate($minDate, 'd M'),
        'max_text' => formatDate($maxDate, 'd M'),
    ];
}

function addBusinessDays(string $startDate, int $days, bool $excludeWeekends = true): string
{
    $date    = new DateTime($startDate);
    $added   = 0;

    while ($added < $days) {
        $date->modify('+1 day');
        if ($excludeWeekends) {
            $dow = (int)$date->format('N');
            if ($dow >= 6) continue; // Cumartesi=6, Pazar=7
        }
        $added++;
    }

    return $date->format('Y-m-d');
}

// ============================================
// DIGER
// ============================================

function dd(mixed ...$vars): void
{
    echo '<pre style="background:#1e1e1e;color:#d4d4d4;padding:16px;border-radius:8px;overflow:auto;">';
    foreach ($vars as $var) {
        var_dump($var);
    }
    echo '</pre>';
    exit;
}

function isAjax(): bool
{
    return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
}

function abort(int $code = 404): void
{
    Response::abort($code);
}

function config(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $default;
}

function isDev(): bool
{
    return ($_ENV['APP_ENV'] ?? 'production') === 'development';
}
