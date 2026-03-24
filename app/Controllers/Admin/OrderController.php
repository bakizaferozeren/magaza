<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Order;
use App\Models\Setting;

class OrderController
{
    public function index(): void
    {
        $page    = max(1, (int) Request::get('page', 1));
        $search  = Request::get('search', '');
        $status  = Request::get('status', '');
        $payment = Request::get('payment', '');
        $dateFrom= Request::get('date_from', '');
        $dateTo  = Request::get('date_to', '');

        $where  = ['1=1'];
        $params = [];

        if ($search) {
            $where[]  = "(o.order_no LIKE ? OR u.email LIKE ? OR o.guest_email LIKE ? OR CONCAT(u.name,' ',u.surname) LIKE ? OR o.shipping_name LIKE ?)";
            $s = "%$search%";
            $params = array_merge($params, [$s,$s,$s,$s,$s]);
        }
        if ($status)  { $where[] = "o.status = ?";         $params[] = $status; }
        if ($payment) { $where[] = "o.payment_status = ?"; $params[] = $payment; }
        if ($dateFrom){ $where[] = "DATE(o.created_at) >= ?"; $params[] = $dateFrom; }
        if ($dateTo)  { $where[] = "DATE(o.created_at) <= ?"; $params[] = $dateTo; }

        $whereStr = implode(' AND ', $where);
        $perPage  = 20;
        $offset   = ($page - 1) * $perPage;

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE $whereStr", $params
        );

        $orders = Database::rows(
            "SELECT o.*,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, o.guest_email, 'Misafir') as customer_name,
                    COALESCE(u.email, o.guest_email) as customer_email,
                    (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE $whereStr
             ORDER BY o.id DESC
             LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        // Ozet istatistikler
        $stats = [
            'pending'    => Database::value("SELECT COUNT(*) FROM orders WHERE status = 'pending'"),
            'processing' => Database::value("SELECT COUNT(*) FROM orders WHERE status = 'processing'"),
            'shipped'    => Database::value("SELECT COUNT(*) FROM orders WHERE status = 'shipped'"),
            'today'      => Database::value("SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()"),
        ];

        Response::view('Admin.orders.index', [
            'title'       => 'Siparişler',
            'siteName'    => Setting::get('site_name'),
            'siteLogo'    => Setting::get('site_logo'),
            'user'        => Auth::user(),
            'orders'      => $orders,
            'stats'       => $stats,
            'search'      => $search,
            'filters'     => compact('status','payment','dateFrom','dateTo'),
            'pagination'  => [
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
        $id    = (int) $params['id'];
        $order = Database::row(
            "SELECT o.*,
                    u.name, u.surname, u.email as user_email, u.phone as user_phone,
                    COALESCE(u.email, o.guest_email) as customer_email,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = ?", [$id]
        );

        if (!$order) Response::abort(404);

        $items = Database::rows(
            "SELECT oi.*,
                    pt.name as product_name,
                    p.slug as product_slug,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover=1 LIMIT 1) as product_image
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE oi.order_id = ?", [$id]
        );

        $statusHistory = Database::rows(
            "SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at ASC", [$id]
        );

        $invoices = Database::rows(
            "SELECT * FROM invoices WHERE order_id = ? ORDER BY created_at DESC", [$id]
        );

        $shippingAddr = json_decode($order['shipping_address'] ?? '{}', true);
        $billingAddr  = json_decode($order['billing_address']  ?? '{}', true);

        Response::view('Admin.orders.show', [
            'title'         => 'Sipariş #' . $order['order_no'],
            'siteName'      => Setting::get('site_name'),
            'siteLogo'      => Setting::get('site_logo'),
            'user'          => Auth::user(),
            'order'         => $order,
            'items'         => $items,
            'statusHistory' => $statusHistory,
            'invoices'      => $invoices,
            'shippingAddr'  => $shippingAddr,
            'billingAddr'   => $billingAddr,
        ]);
    }

    public function updateStatus(array $params): void
    {
        $id     = (int) $params['id'];
        $status = Request::post('status');
        $note   = Request::post('note', '');

        $order = Order::find($id);
        if (!$order) Response::abort(404);

        $validStatuses = ['pending','processing','shipped','delivered','cancelled','refunded'];
        if (!in_array($status, $validStatuses)) {
            Session::flash('error', 'Geçersiz sipariş durumu.');
            Response::back();
        }

        Database::beginTransaction();
        try {
            // Durumu güncelle
            Database::query(
                "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?",
                [$status, $id]
            );

            // Gecmise ekle
            Database::query(
                "INSERT INTO order_status_history (order_id, status, note, created_by, created_at)
                 VALUES (?, ?, ?, ?, NOW())",
                [$id, $status, $note, Auth::user()['id']]
            );

            // Stok iptalde geri ekle
            if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
                $items = Database::rows(
                    "SELECT * FROM order_items WHERE order_id = ?", [$id]
                );
                foreach ($items as $item) {
                    Database::query(
                        "UPDATE products SET stock = stock + ? WHERE id = ?",
                        [$item['qty'], $item['product_id']]
                    );
                }
            }

            Database::commit();
            Logger::activity('order_status_updated', 'Order', $id, "Status: $status");
            Session::flash('success', 'Sipariş durumu güncellendi.');
        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Sipariş durumu güncellenirken hata: ' . $e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
        }

        Response::redirect(adminUrl('siparisler/' . $id));
    }

    public function updateCargo(array $params): void
    {
        $id             = (int) $params['id'];
        $cargoCompany   = Request::post('cargo_company');
        $trackingCode   = Request::post('tracking_code');
        $trackingUrl    = Request::post('tracking_url');

        Database::query(
            "UPDATE orders SET
                cargo_company  = ?,
                tracking_code  = ?,
                tracking_url   = ?,
                status         = IF(status = 'processing', 'shipped', status),
                updated_at     = NOW()
             WHERE id = ?",
            [$cargoCompany, $trackingCode, $trackingUrl, $id]
        );

        if ($trackingCode) {
            Database::query(
                "INSERT INTO order_status_history (order_id, status, note, created_by, created_at)
                 VALUES (?, 'shipped', ?, ?, NOW())",
                [$id, "Kargo: $cargoCompany — Takip: $trackingCode", Auth::user()['id']]
            );
        }

        Logger::activity('order_cargo_updated', 'Order', $id);
        Session::flash('success', 'Kargo bilgileri güncellendi.');
        Response::redirect(adminUrl('siparisler/' . $id));
    }

    public function bulkUpdate(): void
    {
        $ids    = Request::post('ids', []);
        $action = Request::post('action');

        if (empty($ids) || !$action) {
            Session::flash('error', 'Lütfen sipariş ve işlem seçin.');
            Response::back();
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $validActions = ['processing','shipped','delivered','cancelled'];
        if (!in_array($action, $validActions)) {
            Session::flash('error', 'Geçersiz işlem.');
            Response::back();
        }

        Database::query(
            "UPDATE orders SET status = ?, updated_at = NOW() WHERE id IN ($placeholders)",
            array_merge([$action], $ids)
        );

        Logger::activity('orders_bulk_updated', 'Order', null, "Action: $action, Count: ".count($ids));
        Session::flash('success', count($ids) . ' sipariş güncellendi.');
        Response::redirect(adminUrl('siparisler'));
    }

    public function printInvoice(array $params): void
    {
        $id    = (int) $params['id'];
        $order = Database::row(
            "SELECT o.*,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name,
                    COALESCE(u.email, o.guest_email) as customer_email
             FROM orders o LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id = ?", [$id]
        );

        if (!$order) Response::abort(404);

        $items = Database::rows(
            "SELECT oi.*, pt.name as product_name
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE oi.order_id = ?", [$id]
        );

        $shippingAddr = json_decode($order['shipping_address'] ?? '{}', true);
        $billingAddr  = json_decode($order['billing_address']  ?? '{}', true);

        Response::view('Admin.orders.invoice_print', [
            'title'       => 'Fatura #' . $order['order_no'],
            'siteName'    => Setting::get('site_name'),
            'order'       => $order,
            'items'       => $items,
            'shippingAddr'=> $shippingAddr,
            'billingAddr' => $billingAddr,
        ]);
    }

    public function abandonedCarts(): void
    {
        $carts = Database::rows(
            "SELECT ac.*,
                    COALESCE(CONCAT(u.name,' ',u.surname), ac.email, 'Misafir') as customer_name
             FROM abandoned_carts ac
             LEFT JOIN users u ON u.id = ac.user_id
             WHERE ac.recovered = 0
             ORDER BY ac.created_at DESC LIMIT 100"
        );

        Response::view('Admin.orders.abandoned', [
            'title'    => 'Terk Edilmiş Sepetler',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'carts'    => $carts,
        ]);
    }

    public function addNote(array $params): void
    {
        $id   = (int) $params['id'];
        $note = trim(Request::post('note', ''));
        if ($note) {
            Database::query(
                "INSERT INTO order_status_history (order_id, status, note, created_by, created_at)
                 VALUES (?, 'note', ?, ?, NOW())",
                [$id, $note, Auth::user()['id']]
            );
        }
        Session::flash('success', 'Not eklendi.');
        Response::redirect(adminUrl('siparisler/' . $id));
    }

    public function cargoLabel(array $params): void
    {
        $id    = (int) $params['id'];
        $order = Database::row(
            "SELECT o.*,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name) as customer_name
             FROM orders o LEFT JOIN users u ON u.id = o.user_id
             WHERE o.id=?", [$id]
        );
        if (!$order) Response::abort(404);

        Response::view('Admin.orders.cargo_label', [
            'title' => 'Kargo Etiketi #' . $order['order_no'],
            'order' => $order,
        ]);
    }
}
