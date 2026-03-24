<?php

namespace App\Core;

class Logger
{
    public static function error(string $message, array $context = []): void
    {
        self::write('error', $message, $context);
        self::saveToDb('error', $message);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::write('warning', $message, $context);
        self::saveToDb('warning', $message);
    }

    public static function info(string $message, array $context = []): void
    {
        self::write('info', $message, $context);
    }

    private static function write(string $level, string $message, array $context = []): void
    {
        $logDir = STR_PATH . '/logs/' . date('Y-m') . '/';

        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $file    = $logDir . date('Y-m-d') . '.log';
        $time    = date('Y-m-d H:i:s');
        $ip      = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $url     = $_SERVER['REQUEST_URI'] ?? '';
        $context = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';

        $line = "[{$time}] [{$level}] [{$ip}] {$url} | {$message}{$context}" . PHP_EOL;

        file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    private static function saveToDb(string $level, string $message): void
    {
        try {
            Database::query(
                "INSERT INTO error_logs (level, message, url, ip) VALUES (?, ?, ?, ?)",
                [
                    $level,
                    $message,
                    $_SERVER['REQUEST_URI'] ?? '',
                    $_SERVER['REMOTE_ADDR'] ?? '',
                ]
            );
        } catch (\Throwable) {
            // DB'ye yazamazsa sessizce devam et
        }
    }

    // Aktivite logu
    public static function activity(string $action, string $model = '', int $modelId = 0, array $oldData = [], array $newData = []): void
    {
        try {
            Database::query(
                "INSERT INTO activity_logs (user_id, action, model, model_id, old_data, new_data, ip, user_agent)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    \App\Core\Auth::id(),
                    $action,
                    $model,
                    $modelId ?: null,
                    !empty($oldData) ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null,
                    !empty($newData) ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null,
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                ]
            );
        } catch (\Throwable) {}
    }

    // Guvenlik logu
    public static function security(string $event, ?int $userId = null): void
    {
        try {
            Database::query(
                "INSERT INTO security_logs (user_id, event, ip, user_agent) VALUES (?, ?, ?, ?)",
                [
                    $userId,
                    $event,
                    $_SERVER['REMOTE_ADDR'] ?? '',
                    $_SERVER['HTTP_USER_AGENT'] ?? '',
                ]
            );
        } catch (\Throwable) {}
    }
}
