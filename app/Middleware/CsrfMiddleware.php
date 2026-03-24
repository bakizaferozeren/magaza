<?php

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Logger;

class CsrfMiddleware
{
    // CSRF dogrulaması gerekmeyen URL'ler
    private array $except = [
        '/webhook/paytr',
    ];

    public function handle(): void
    {
        // Sadece POST isteklerini kontrol et
        if (Request::method() !== 'POST') return;

        // Istisna URL'leri atla
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        foreach ($this->except as $except) {
            if (str_contains($uri, $except)) return;
        }

        // CSRF token dogrula
        if (!Request::verifyCsrf()) {
            Logger::security('csrf_failure');

            if (Request::isAjax()) {
                Response::error('Gecersiz istek. Sayfayi yenileyip tekrar deneyin.', 419);
            }

            Session::flash('error', 'Gecersiz istek. Lutfen sayfayi yenileyip tekrar deneyin.');
            Response::back();
        }
    }
}
