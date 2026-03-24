<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Setting;

class ReportController
{
    // Ortak filtreler
    private function getFilters(): array
    {
        $from = Request::get('from', date('Y-m-01'));
        $to   = Request::get('to',   date('Y-m-d'));
        return compact('from', 'to');
    }

    // Satış Raporları
    public function sales(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        // Günlük satış
        $daily = Database::rows(
            "SELECT DATE(created_at) as date,
                    COUNT(*) as order_count,
                    SUM(total) as revenue,
                    AVG(total) as avg_order
             FROM orders
             WHERE DATE(created_at) BETWEEN ? AND ?
               AND payment_status = 'paid'
             GROUP BY DATE(created_at)
             ORDER BY date ASC",
            [$from, $to]
        );

        // Özet
        $summary = Database::row(
            "SELECT COUNT(*) as total_orders,
                    COALESCE(SUM(total), 0) as total_revenue,
                    COALESCE(AVG(total), 0) as avg_order,
                    COALESCE(SUM(discount), 0) as total_discount
             FROM orders
             WHERE DATE(created_at) BETWEEN ? AND ?
               AND payment_status = 'paid'",
            [$from, $to]
        );

        // Durum dağılımı
        $byStatus = Database::rows(
            "SELECT status, COUNT(*) as count, COALESCE(SUM(total),0) as revenue
             FROM orders
             WHERE DATE(created_at) BETWEEN ? AND ?
             GROUP BY status",
            [$from, $to]
        );

        // Ödeme yöntemi dağılımı
        $byPayment = Database::rows(
            "SELECT payment_method, COUNT(*) as count, COALESCE(SUM(total),0) as revenue
             FROM orders
             WHERE DATE(created_at) BETWEEN ? AND ?
               AND payment_status = 'paid'
             GROUP BY payment_method",
            [$from, $to]
        );

        // Önceki dönem karşılaştırma
        $days = (strtotime($to) - strtotime($from)) / 86400 + 1;
        $prevFrom = date('Y-m-d', strtotime($from) - $days * 86400);
        $prevTo   = date('Y-m-d', strtotime($from) - 86400);

        $prevSummary = Database::row(
            "SELECT COALESCE(SUM(total), 0) as total_revenue, COUNT(*) as total_orders
             FROM orders
             WHERE DATE(created_at) BETWEEN ? AND ?
               AND payment_status = 'paid'",
            [$prevFrom, $prevTo]
        );

        Response::view('Admin.reports.sales', [
            'title'       => 'Satış Raporları',
            'siteName'    => Setting::get('site_name'),
            'siteLogo'    => Setting::get('site_logo'),
            'user'        => Auth::user(),
            'from'        => $from,
            'to'          => $to,
            'daily'       => $daily,
            'summary'     => $summary,
            'byStatus'    => $byStatus,
            'byPayment'   => $byPayment,
            'prevSummary' => $prevSummary,
        ]);
    }

    // Ürün Raporları
    public function products(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        // En çok satan ürünler
        $topProducts = Database::rows(
            "SELECT
                pt.name,
                p.slug,
                SUM(oi.quantity) as total_qty,
                SUM(oi.price * oi.quantity) as total_revenue,
                COUNT(DISTINCT oi.order_id) as order_count,
                (SELECT path FROM product_images WHERE product_id = p.id AND is_cover=1 LIMIT 1) as image
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE DATE(o.created_at) BETWEEN ? AND ?
               AND o.payment_status = 'paid'
             GROUP BY oi.product_id
             ORDER BY total_qty DESC
             LIMIT 20",
            [$from, $to]
        );

        // Kategori bazlı satış
        $byCategory = Database::rows(
            "SELECT
                ct.name as category_name,
                COUNT(DISTINCT oi.order_id) as order_count,
                SUM(oi.quantity) as total_qty,
                SUM(oi.price * oi.quantity) as total_revenue
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = 'tr'
             WHERE DATE(o.created_at) BETWEEN ? AND ?
               AND o.payment_status = 'paid'
             GROUP BY p.category_id
             ORDER BY total_revenue DESC
             LIMIT 10",
            [$from, $to]
        );

        // Marka bazlı satış
        $byBrand = Database::rows(
            "SELECT
                bt.name as brand_name,
                SUM(oi.quantity) as total_qty,
                SUM(oi.price * oi.quantity) as total_revenue
             FROM order_items oi
             JOIN orders o ON o.id = oi.order_id
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = 'tr'
             WHERE DATE(o.created_at) BETWEEN ? AND ?
               AND o.payment_status = 'paid'
             GROUP BY p.brand_id
             ORDER BY total_revenue DESC
             LIMIT 10",
            [$from, $to]
        );

        Response::view('Admin.reports.products', [
            'title'       => 'Ürün Raporları',
            'siteName'    => Setting::get('site_name'),
            'siteLogo'    => Setting::get('site_logo'),
            'user'        => Auth::user(),
            'from'        => $from,
            'to'          => $to,
            'topProducts' => $topProducts,
            'byCategory'  => $byCategory,
            'byBrand'     => $byBrand,
        ]);
    }

    // Stok Raporları
    public function stock(): void
    {
        $filter = Request::get('filter', 'low');

        $where = match($filter) {
            'low'      => "p.stock > 0 AND p.stock <= COALESCE(p.stock_alert_qty, 5)",
            'out'      => "p.stock_status = 'out_of_stock' OR p.stock = 0",
            'all'      => "1=1",
            default    => "p.stock > 0 AND p.stock <= COALESCE(p.stock_alert_qty, 5)",
        };

        $products = Database::rows(
            "SELECT p.id, p.sku, p.stock, p.stock_status, p.stock_alert_qty, p.price,
                    pt.name,
                    ct.name as category_name,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover=1 LIMIT 1) as image
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = 'tr'
             WHERE $where
             ORDER BY p.stock ASC
             LIMIT 100"
        );

        // Stok özeti
        $stockSummary = Database::row(
            "SELECT
                COUNT(*) as total_products,
                SUM(CASE WHEN stock = 0 OR stock_status='out_of_stock' THEN 1 ELSE 0 END) as out_of_stock,
                SUM(CASE WHEN stock > 0 AND stock <= COALESCE(stock_alert_qty,5) THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock > COALESCE(stock_alert_qty,5) THEN 1 ELSE 0 END) as in_stock,
                SUM(stock * price) as stock_value
             FROM products WHERE is_active = 1"
        );

        Response::view('Admin.reports.stock', [
            'title'        => 'Stok Raporları',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'products'     => $products,
            'stockSummary' => $stockSummary,
            'filter'       => $filter,
        ]);
    }

    // CSV Export
    public function exportSalesCsv(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        $orders = Database::rows(
            "SELECT o.order_no, o.created_at, o.status, o.payment_status, o.payment_method,
                    o.subtotal, o.discount, o.shipping_cost, o.total,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer,
                    COALESCE(u.email, o.guest_email) as email
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             WHERE DATE(o.created_at) BETWEEN ? AND ?
             ORDER BY o.id DESC",
            [$from, $to]
        );

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="satis_raporu_'.$from.'_'.$to.'.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, ['Sipariş No','Tarih','Durum','Ödeme','Yöntem','Ara Toplam','İndirim','Kargo','Toplam','Müşteri','E-posta']);
        foreach ($orders as $o) {
            fputcsv($out, [
                $o['order_no'], $o['created_at'], $o['status'], $o['payment_status'],
                $o['payment_method'], $o['subtotal'], $o['discount'], $o['shipping_cost'],
                $o['total'], $o['customer'], $o['email'],
            ]);
        }
        fclose($out);
        exit;
    }

    public function exportStockCsv(): void
    {
        $products = Database::rows(
            "SELECT p.sku, pt.name, ct.name as category, p.stock, p.stock_status, p.price
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = 'tr'
             ORDER BY p.stock ASC"
        );

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="stok_raporu_'.date('Y-m-d').'.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, ['SKU','Ürün Adı','Kategori','Stok','Stok Durumu','Fiyat']);
        foreach ($products as $p) {
            fputcsv($out, [$p['sku'],$p['name'],$p['category'],$p['stock'],$p['stock_status'],$p['price']]);
        }
        fclose($out);
        exit;
    }

    public function customers(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        $topCustomers = Database::rows(
            "SELECT u.name, u.surname, u.email,
                    COUNT(o.id) as order_count,
                    SUM(o.total) as total_spent
             FROM users u
             JOIN orders o ON o.user_id = u.id
             WHERE DATE(o.created_at) BETWEEN ? AND ?
               AND o.payment_status = 'paid'
             GROUP BY u.id
             ORDER BY total_spent DESC
             LIMIT 20",
            [$from, $to]
        );

        $newCustomers = Database::rows(
            "SELECT DATE(created_at) as date, COUNT(*) as count
             FROM users
             WHERE role='customer' AND DATE(created_at) BETWEEN ? AND ?
             GROUP BY DATE(created_at) ORDER BY date ASC",
            [$from, $to]
        );

        Response::view('Admin.reports.customers', [
            'title'        => 'Müşteri Raporları',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'from'         => $from, 'to' => $to,
            'topCustomers' => $topCustomers,
            'newCustomers' => $newCustomers,
        ]);
    }

    public function coupons(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        $coupons = Database::rows(
            "SELECT c.code, c.type, c.value,
                    COUNT(o.id) as used_count,
                    SUM(o.discount) as total_discount
             FROM coupons c
             LEFT JOIN orders o ON o.coupon_code = c.code
               AND DATE(o.created_at) BETWEEN ? AND ?
             GROUP BY c.id
             ORDER BY used_count DESC",
            [$from, $to]
        );

        Response::view('Admin.reports.coupons', [
            'title'    => 'Kupon Raporları',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'from'     => $from, 'to' => $to,
            'coupons'  => $coupons,
        ]);
    }

    public function abandonedCarts(): void
    {
        ['from' => $from, 'to' => $to] = $this->getFilters();

        $carts = Database::rows(
            "SELECT ac.*,
                    COALESCE(CONCAT(u.name,' ',u.surname), ac.email, 'Misafir') as customer_name
             FROM abandoned_carts ac
             LEFT JOIN users u ON u.id = ac.user_id
             WHERE DATE(ac.created_at) BETWEEN ? AND ?
             ORDER BY ac.created_at DESC LIMIT 100",
            [$from, $to]
        );

        $summary = Database::row(
            "SELECT COUNT(*) as total, SUM(CASE WHEN recovered=1 THEN 1 ELSE 0 END) as recovered,
                    COALESCE(SUM(total),0) as lost_revenue
             FROM abandoned_carts
             WHERE DATE(created_at) BETWEEN ? AND ?",
            [$from, $to]
        );

        Response::view('Admin.reports.abandoned', [
            'title'    => 'Terk Edilmiş Sepetler',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'from'     => $from, 'to' => $to,
            'carts'    => $carts, 'summary' => $summary,
        ]);
    }

    public function download(): void
    {
        $type = Request::get('type', 'sales');
        ['from' => $from, 'to' => $to] = $this->getFilters();

        // CSV indirme için ilgili metoda yönlendir
        match($type) {
            'sales' => $this->exportSalesCsv(),
            'stock' => $this->exportStockCsv(),
            default => Response::abort(404),
        };
    }
}
