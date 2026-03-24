<?php

namespace App\Core;

class Response
{
    // JSON yaniti
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    // Basarili JSON
    public static function success(mixed $data = null, string $message = 'Islem basarili'): void
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    // Hata JSON
    public static function error(string $message = 'Bir hata olustu', int $status = 400, mixed $errors = null): void
    {
        self::json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ], $status);
    }

    // Yonlendir
    public static function redirect(string $url, int $status = 302): void
    {
        header("Location: {$url}", true, $status);
        exit;
    }

    // Geri don
    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? ($_ENV['APP_URL'] ?? '/');
        self::redirect($referer);
    }

    // View render
    public static function view(string $path, array $data = [], int $status = 200): void
    {
        http_response_code($status);
        extract($data);
        $viewFile = APP_PATH . '/Views/' . str_replace('.', '/', $path) . '.php';

        if (!file_exists($viewFile)) {
            Logger::error("View bulunamadi: {$viewFile}");
            self::abort(404);
        }

        require $viewFile;
        exit;
    }

    // Hata sayfasi
    public static function abort(int $code = 404): void
    {
        http_response_code($code);
        $viewFile = APP_PATH . "/Views/Store/errors/{$code}.php";

        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "<h1>{$code} - Hata</h1>";
        }
        exit;
    }

    // Dosya indirme
    public static function download(string $filePath, string $fileName = ''): void
    {
        if (!file_exists($filePath)) {
            self::abort(404);
        }

        if (!$fileName) {
            $fileName = basename($filePath);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }

    // HTTP status
    public static function status(int $code): void
    {
        http_response_code($code);
    }
}
