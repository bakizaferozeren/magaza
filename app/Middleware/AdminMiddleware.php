<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Session;

class AdminMiddleware
{
    public function handle(): void
    {
        // Giris yapilmamissa admin giris sayfasina yonlendir
        if (!Auth::check()) {
            Session::flash('error', 'Bu sayfaya erisim icin giris yapmaniz gerekiyor.');
            $adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';
            Response::redirect('/' . $adminPath . '/giris');
        }

        // Admin degil ise anasayfaya yonlendir
        if (!Auth::isAdmin()) {
            Response::redirect('/');
        }
    }
}
