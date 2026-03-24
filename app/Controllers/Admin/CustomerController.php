<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class CustomerController
{
    public function index(): void
    {
        $page   = max(1, (int) Request::get('page', 1));
        $search = Request::get('search', '');
        $status = Request::get('status', '');

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = "(u.name LIKE ? OR u.surname LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)";
            $s        = "%$search%";
            $params   = array_merge($params, [$s,$s,$s,$s]);
        }
        if ($status === 'verified')   { $where[] = 'u.email_verified = 1'; }
        if ($status === 'unverified') { $where[] = 'u.email_verified = 0'; }

        $where[] = "u.role = 'customer'";
        $whereStr = implode(' AND ', $where);
        $perPage  = 20;
        $offset   = ($page - 1) * $perPage;

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM users u WHERE $whereStr", $params
        );

        $customers = Database::rows(
            "SELECT u.*,
                    COUNT(DISTINCT o.id) as order_count,
                    COALESCE(SUM(o.total),0) as total_spent
             FROM users u
             LEFT JOIN orders o ON o.user_id = u.id AND o.payment_status = 'paid'
             WHERE $whereStr
             GROUP BY u.id
             ORDER BY u.id DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        Response::view('Admin.customers.index', [
            'title'      => 'Müşteriler',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'customers'  => $customers,
            'search'     => $search,
            'status'     => $status,
            'pagination' => [
                'total'        => $total,
                'per_page'     => $perPage,
                'current_page' => $page,
                'last_page'    => (int) ceil($total / $perPage),
                'from'         => $total > 0 ? $offset + 1 : 0,
                'to'           => min($offset + $perPage, $total),
            ],
        ]);
    }

    public function show(array $params): void
    {
        $id       = (int) $params['id'];
        $customer = Database::row("SELECT * FROM users WHERE id = ? AND role = 'customer'", [$id]);
        if (!$customer) Response::abort(404);

        $orders = Database::rows(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC LIMIT 10", [$id]
        );

        $addresses = Database::rows(
            "SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC", [$id]
        );

        $stats = Database::row(
            "SELECT COUNT(*) as total_orders,
                    COALESCE(SUM(total),0) as total_spent,
                    COALESCE(AVG(total),0) as avg_order,
                    MIN(created_at) as first_order,
                    MAX(created_at) as last_order
             FROM orders WHERE user_id = ? AND payment_status = 'paid'",
            [$id]
        );

        Response::view('Admin.customers.show', [
            'title'     => e($customer['name'].' '.$customer['surname']),
            'siteName'  => Setting::get('site_name'),
            'siteLogo'  => Setting::get('site_logo'),
            'user'      => Auth::user(),
            'customer'  => $customer,
            'orders'    => $orders,
            'addresses' => $addresses,
            'stats'     => $stats,
        ]);
    }

    public function destroy(array $params): void
    {
        $id = (int) $params['id'];
        Database::query("UPDATE users SET email = CONCAT('deleted_', id, '_', email), name = 'Silinmiş', surname = 'Kullanıcı' WHERE id = ?", [$id]);
        Logger::activity('customer_deleted', 'User', $id);
        Session::flash('success', 'Müşteri silindi.');
        Response::redirect(adminUrl('musteriler'));
    }

    public function guests(): void
    {
        $orders = Database::rows(
            "SELECT o.order_no, o.guest_email, o.shipping_name, o.total, o.status, o.created_at
             FROM orders o
             WHERE o.user_id IS NULL AND o.guest_email IS NOT NULL
             ORDER BY o.id DESC LIMIT 100"
        );

        Response::view('Admin.customers.guests', [
            'title'    => 'Misafir Siparişleri',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'orders'   => $orders,
        ]);
    }
}
