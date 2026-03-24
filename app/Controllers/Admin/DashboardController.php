<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Response;
use App\Models\Order;
use App\Models\Setting;

class DashboardController
{
    public function index(): void
    {
        $stats = Order::stats();

        $data = [
            'title'       => 'Dashboard',
            'stats'       => $stats,
            'siteName'    => Setting::get('site_name', 'Magazam'),
            'siteLogo'    => Setting::get('site_logo'),
            'user'        => Auth::user(),
        ];

        Response::view('Admin.dashboard.index', $data);
    }
}
