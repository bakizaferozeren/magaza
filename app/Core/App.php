<?php

namespace App\Core;

class App
{
    private Router $router;

    public function __construct()
    {
        $this->configure();
        $this->router = new Router();
        $this->loadRoutes();
    }

    private function configure(): void
    {
        // Timezone
        date_default_timezone_set('Europe/Istanbul');

        // Hata raporlama
        if ($_ENV['APP_ENV'] === 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
        }

        // Session baslat
        Session::start();

        // Bakim modu kontrolu
        $this->checkMaintenance();
    }

    private function checkMaintenance(): void
    {
        $maintenance = \App\Models\Setting::get('maintenance_mode', '0');
        if ($maintenance !== '1') return;

        $adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Admin URL'sine izin ver
        if (str_contains($uri, $adminPath)) return;

        // Izin verilen IP'ler
        $allowedIps = \App\Models\Setting::get('maintenance_allowed_ips', '');
        $allowedList = array_filter(array_map('trim', explode(',', $allowedIps)));
        $clientIp = $_SERVER['REMOTE_ADDR'] ?? '';

        if (in_array($clientIp, $allowedList)) return;

        // Bakim sayfasi goster
        http_response_code(503);
        require APP_PATH . '/Views/Store/errors/maintenance.php';
        exit;
    }

    private function loadRoutes(): void
    {
        require APP_PATH . '/Routes/admin.php';
        require APP_PATH . '/Routes/store.php';
    }

    public function run(): void
    {
        $this->router->dispatch();
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
