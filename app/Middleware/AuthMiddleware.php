<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check()) {
            Session::flash('error', 'Bu sayfaya erisim icin giris yapmaniz gerekiyor.');
            Session::set('redirect_after_login', $_SERVER['REQUEST_URI'] ?? '/');
            Response::redirect('/giris');
        }
    }
}
