<?php

namespace App\Middleware;

use App\Core\Request;
use App\Models\Setting;

class MaintenanceMiddleware
{
    public function handle(): void
    {
        $maintenance = Setting::get('maintenance_mode', '0');
        if ($maintenance !== '1') return;

        $adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);

        // Admin paneline izin ver
        if (str_contains($uri, $adminPath)) return;

        // Izin verilen IP'ler
        $allowedIps  = Setting::get('maintenance_allowed_ips', '');
        $allowedList = array_filter(array_map('trim', explode(',', $allowedIps)));
        $clientIp    = Request::ip();

        if (in_array($clientIp, $allowedList)) return;

        // Bakim sayfasini goster
        http_response_code(503);
        header('Retry-After: 3600');

        $viewFile = APP_PATH . '/Views/Store/errors/maintenance.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo '<h1>Site bakimda</h1><p>Kisa sure sonra tekrar deneyin.</p>';
        }
        exit;
    }
}
