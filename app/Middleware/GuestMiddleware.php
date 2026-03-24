<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(): void
    {
        // Zaten giris yapilmissa yonlendir
        if (Auth::check()) {
            if (Auth::isAdmin()) {
                $adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';
                Response::redirect('/' . $adminPath);
            } else {
                Response::redirect('/hesabim');
            }
        }
    }
}
