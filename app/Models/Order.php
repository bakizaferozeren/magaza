<?php

namespace App\Models;

use App\Core\Database;

class Order extends BaseModel
{
    protected static string $table = 'orders';

    // Siparis olustur
    public static function createOrder(array $data): int
    {
        Database::beginTransaction();
        try {
            $orderId = self::create([
                'order_no'          => generateOrderNo(),
                'user_id'           => $data['user_id'] ?? null,
                'guest_email'       => $data['guest_email'] ?? null,
                'guest_token'       => $data['guest_token'] ?? generateToken(16),
                'status'            => 'pending',
                'subtotal'          => $data['subtotal'],
                'tax_amount'        => $data['tax_amount'] ?? 0,
                'shipping_cost'     => $data['shipping_cost'] ?? 0,
                'discount'          => $data['discount'] ?? 0,
                'total'             => $data['total'],
                'coupon_id'         => $data['coupon_id'] ?? null,
                'coupon_code'       => $data['coupon_code'] ?? null,
                'currency'          => $data['currency'] ?? 'TRY',
                'currency_rate'     => $data['currency_rate'] ?? 1,
                'payment_method'    => $data['payment_method'] ?? null,
                'installment'       => $data['installment'] ?? 1,
                'shipping_name'     => $data['shipping_name'],
                'shipping_phone'    => $data['shipping_phone'],
                'shipping_city'     => $data['shipping_city'],
                'shipping_district' => $data['shipping_district'] ?? null,
                'shipping_address'  => $data['shipping_address'],
                'shipping_zip'      => $data['shipping_zip'] ?? null,
                'billing_same'      => $data['billing_same'] ?? 1,
                'billing_name'      => $data['billing_name'] ?? null,
                'billing_address'   => $data['billing_address'] ?? null,
                'billing_tax_no'    => $data['billing_tax_no'] ?? null,
                'billing_company'   => $data['billing_company'] ?? null,
                'notes'             => $data['notes'] ?? null,
                'ip_address'        => $_SERVER['REMOTE_ADDR'] ?? null,
            ]);

            // Siparis kalemleri ekle
            foreach ($data['items'] as $item) {
                Database::query(
                    "INSERT INTO order_items
                     (order_id, product_id, product_variation_id, name, sku, variation_info, price, tax_rate, quantity, shipping_type)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $orderId,
                        $item['product_id'],
                        $item['variation_id'] ?? null,
                        $item['name'],
                        $item['sku'] ?? null,
                        $item['variation_info'] ?? null,
                        $item['price'],
                        $item['tax_rate'] ?? 20,
                        $item['quantity'],
                        $item['shipping_type'] ?? 'domestic',
                    ]
                );

                // Stok duş
                Database::query(
                    "UPDATE products SET stock = stock - ?, sale_count = sale_count + ? WHERE id = ?",
                    [$item['quantity'], $item['quantity'], $item['product_id']]
                );
            }

            // Durum gecmisi
            self::addStatusHistory($orderId, 'pending', 'Siparis alindi');

            // Kupon kullanim sayisini artir
            if (!empty($data['coupon_id'])) {
                Database::query(
                    "UPDATE coupons SET usage_count = usage_count + 1 WHERE id = ?",
                    [$data['coupon_id']]
                );
            }

            Database::commit();
            return $orderId;

        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    // Durum guncelle
    public static function updateStatus(int $orderId, string $status, string $note = '', ?int $adminId = null): void
    {
        self::update($orderId, ['status' => $status]);
        self::addStatusHistory($orderId, $status, $note, $adminId);
    }

    // Kargo bilgisi guncelle
    public static function updateCargo(int $orderId, string $company, string $tracking, string $trackingUrl = ''): void
    {
        self::update($orderId, [
            'cargo_company'  => $company,
            'cargo_tracking' => $tracking,
            'cargo_url'      => $trackingUrl,
            'status'         => 'shipped',
        ]);

        self::addStatusHistory($orderId, 'shipped', "Kargo firması: {$company}, Takip No: {$tracking}");
    }

    // Durum gecmisi ekle
    public static function addStatusHistory(int $orderId, string $status, string $note = '', ?int $createdBy = null): void
    {
        Database::query(
            "INSERT INTO order_status_history (order_id, status, note, created_by) VALUES (?, ?, ?, ?)",
            [$orderId, $status, $note, $createdBy]
        );
    }

    // Durum gecmisi getir
    public static function statusHistory(int $orderId): array
    {
        return Database::rows(
            "SELECT osh.*, u.name, u.surname
             FROM order_status_history osh
             LEFT JOIN users u ON u.id = osh.created_by
             WHERE osh.order_id = ?
             ORDER BY osh.created_at ASC",
            [$orderId]
        );
    }

    // Siparis kalemleri
    public static function items(int $orderId): array
    {
        return Database::rows(
            "SELECT oi.*,
                    (SELECT path FROM product_images WHERE product_id = oi.product_id AND is_cover = 1 LIMIT 1) as cover_image
             FROM order_items oi
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }

    // Kullanici siparisleri
    public static function userOrders(int $userId, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM orders WHERE user_id = ?",
            [$userId]
        );

        $items = Database::rows(
            "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
            [$userId, $perPage, $offset]
        );

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    // Admin siparis listesi
    public static function adminList(int $page = 1, int $perPage = 20, array $filters = []): array
    {
        $offset  = ($page - 1) * $perPage;
        $where   = [];
        $params  = [];

        if (!empty($filters['search'])) {
            $where[]  = "(o.order_no LIKE ? OR o.shipping_name LIKE ? OR o.guest_email LIKE ?)";
            $s = "%{$filters['search']}%";
            $params[] = $s; $params[] = $s; $params[] = $s;
        }

        if (!empty($filters['status'])) {
            $where[]  = "o.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['payment_status'])) {
            $where[]  = "o.payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        if (!empty($filters['date_from'])) {
            $where[]  = "DATE(o.created_at) >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $where[]  = "DATE(o.created_at) <= ?";
            $params[] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM orders o {$whereClause}",
            $params
        );

        $items = Database::rows(
            "SELECT o.*, CONCAT(u.name, ' ', u.surname) as customer_name
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             {$whereClause}
             ORDER BY o.id DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    // Siparis no ile bul
    public static function findByOrderNo(string $orderNo): ?array
    {
        return Database::row("SELECT * FROM orders WHERE order_no = ?", [$orderNo]);
    }

    // Misafir siparisi bul
    public static function findGuestOrder(string $orderNo, string $email): ?array
    {
        return Database::row(
            "SELECT * FROM orders WHERE order_no = ? AND guest_email = ?",
            [$orderNo, $email]
        );
    }

    // Dashboard istatistikleri
    public static function stats(): array
    {
        return [
            'today_total'    => (float) Database::value(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE DATE(created_at) = CURDATE() AND payment_status = 'paid'"
            ),
            'today_count'    => (int) Database::value(
                "SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()"
            ),
            'pending_count'  => (int) Database::value(
                "SELECT COUNT(*) FROM orders WHERE status = 'pending'"
            ),
            'monthly_total'  => (float) Database::value(
                "SELECT COALESCE(SUM(total), 0) FROM orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND payment_status = 'paid'"
            ),
        ];
    }
}
