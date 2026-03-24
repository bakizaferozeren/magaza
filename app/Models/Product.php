<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;

class Product extends BaseModel
{
    protected static string $table = 'products';

    // Dil ile urun getir
    public static function findWithLang(int $id, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT p.*,
                    pt.name, pt.short_desc, pt.long_desc,
                    pt.meta_title, pt.meta_desc, pt.meta_keywords,
                    pt.og_title, pt.og_desc,
                    c.slug as category_slug,
                    ct.name as category_name,
                    bt.name as brand_name,
                    b.slug as brand_slug, b.logo as brand_logo
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             WHERE p.id = ?",
            [$lang, $lang, $lang, $id]
        );
    }

    // Slug ile bul
    public static function findBySlug(string $slug, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT p.*,
                    pt.name, pt.short_desc, pt.long_desc,
                    pt.meta_title, pt.meta_desc, pt.meta_keywords,
                    ct.name as category_name, c.slug as category_slug,
                    bt.name as brand_name, b.slug as brand_slug
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             WHERE p.slug = ? AND p.is_active = 1",
            [$lang, $lang, $lang, $slug]
        );
    }

    // Urun listesi (admin)
    public static function adminList(int $page = 1, int $perPage = 20, array $filters = [], string $lang = 'tr'): array
    {
        $offset      = ($page - 1) * $perPage;
        $where       = [];
        $filterParams = [];

        if (!empty($filters['search'])) {
            $where[]       = "(pt.name LIKE ? OR p.sku LIKE ? OR p.barcode LIKE ?)";
            $s = "%{$filters['search']}%";
            $filterParams[] = $s;
            $filterParams[] = $s;
            $filterParams[] = $s;
        }

        if (!empty($filters['category_id'])) {
            $where[]        = "p.category_id = ?";
            $filterParams[] = $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[]        = "p.brand_id = ?";
            $filterParams[] = $filters['brand_id'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[]        = "p.is_active = ?";
            $filterParams[] = $filters['is_active'];
        }

        if (!empty($filters['stock_status'])) {
            $where[]        = "p.stock_status = ?";
            $filterParams[] = $filters['stock_status'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        // COUNT sorgusu: lang x1 + filterParams
        $total = (int) Database::value(
            "SELECT COUNT(DISTINCT p.id) FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             {$whereClause}",
            array_merge([$lang], $filterParams)
        );

        // LIST sorgusu: lang x3 (pt, ct, bt) + filterParams + limit + offset
        $items = Database::rows(
            "SELECT p.id, p.slug, p.sku, p.price, p.sale_price, p.stock,
                    p.stock_status, p.is_active, p.is_featured, p.is_best_seller,
                    p.is_most_clicked, p.is_recommended, p.shipping_type, p.created_at,
                    pt.name,
                    ct.name as category_name,
                    bt.name as brand_name,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover = 1 LIMIT 1) as cover_image
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             {$whereClause}
             ORDER BY p.id DESC LIMIT ? OFFSET ?",
            array_merge([$lang, $lang, $lang], $filterParams, [$perPage, $offset])
        );

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'from'         => $total > 0 ? $offset + 1 : 0,
            'to'           => min($offset + $perPage, $total),
        ];
    }

    // Urun gorselleri
    public static function images(int $productId): array
    {
        return Database::rows(
            "SELECT * FROM product_images WHERE product_id = ? ORDER BY is_cover DESC, sort_order ASC",
            [$productId]
        );
    }

    // Kapak gorsel
    public static function coverImage(int $productId): ?string
    {
        return Database::value(
            "SELECT path FROM product_images WHERE product_id = ? AND is_cover = 1 LIMIT 1",
            [$productId]
        );
    }

    // Teknik ozellikler
    public static function attributes(int $productId): array
    {
        return Database::rows(
            "SELECT * FROM product_attributes WHERE product_id = ? ORDER BY sort_order ASC",
            [$productId]
        );
    }

    // Varyasyonlar
    public static function variations(int $productId): array
    {
        $variations = Database::rows(
            "SELECT pv.*,
                    pi.path as image_path
             FROM product_variations pv
             LEFT JOIN product_images pi ON pi.id = pv.image_id
             WHERE pv.product_id = ? AND pv.is_active = 1
             ORDER BY pv.sort_order ASC",
            [$productId]
        );

        foreach ($variations as &$variation) {
            $variation['options'] = Database::rows(
                "SELECT pvo.*, vt.name as type_name, vo.name as option_name, vo.value as option_value
                 FROM product_variation_options pvo
                 JOIN variation_types vt ON vt.id = pvo.variation_type_id
                 JOIN variation_options vo ON vo.id = pvo.variation_option_id
                 WHERE pvo.product_variation_id = ?",
                [$variation['id']]
            );
        }

        return $variations;
    }

    // Baglantili urunler
    public static function related(int $productId, string $type = 'similar', string $lang = 'tr'): array
    {
        return Database::rows(
            "SELECT p.*, pt.name,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover = 1 LIMIT 1) as cover_image
             FROM product_relations pr
             JOIN products p ON p.id = pr.related_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             WHERE pr.product_id = ? AND pr.type = ? AND p.is_active = 1
             LIMIT 8",
            [$lang, $productId, $type]
        );
    }

    // Bunu alan sunu da aldi
    public static function boughtTogether(int $productId, string $lang = 'tr', int $limit = 6): array
    {
        $items = Database::rows(
            "SELECT p.*, pt.name,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover = 1 LIMIT 1) as cover_image,
                    COUNT(*) as frequency
             FROM order_items oi1
             JOIN order_items oi2 ON oi2.order_id = oi1.order_id AND oi2.product_id != oi1.product_id
             JOIN products p ON p.id = oi2.product_id AND p.is_active = 1
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             WHERE oi1.product_id = ?
             GROUP BY p.id
             ORDER BY frequency DESC
             LIMIT ?",
            [$lang, $productId, $limit]
        );

        // Veri yoksa ayni kategorideki urunleri getir
        if (empty($items)) {
            $product = self::find($productId);
            if ($product && $product['category_id']) {
                $items = Database::rows(
                    "SELECT p.*, pt.name,
                            (SELECT path FROM product_images WHERE product_id = p.id AND is_cover = 1 LIMIT 1) as cover_image
                     FROM products p
                     LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
                     WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
                     ORDER BY p.sale_count DESC
                     LIMIT ?",
                    [$lang, $product['category_id'], $productId, $limit]
                );
            }
        }

        return $items;
    }

    // Tiklama sayisini artir
    public static function incrementClick(int $id): void
    {
        Database::query(
            "UPDATE products SET click_count = click_count + 1, view_count = view_count + 1 WHERE id = ?",
            [$id]
        );
    }

    // Stok azaldiysa uyari gonder
    public static function checkStockAlert(int $id): void
    {
        $product = self::find($id);
        if (!$product) return;

        $alertQty = $product['stock_alert_qty'];
        if ($alertQty === null) return;

        if ($product['stock'] <= $alertQty) {
            // Mail gonder
            \App\Core\Logger::info("Stok uyarisi: Urun #{$id} stok seviyesi {$product['stock']} adete dustu");
        }
    }

    // Urun olustur
    public static function createWithTranslation(array $data, array $translations): int
    {
        // Transaction dışarıdan (controller) yönetiliyor
        $id = self::create([
            'category_id'   => $data['category_id'] ?? null,
            'brand_id'      => $data['brand_id'] ?? null,
            'slug'          => $data['slug'],
            'sku'           => $data['sku'] ?? null,
            'barcode'       => $data['barcode'] ?? null,
            'price'         => $data['price'],
            'sale_price'    => $data['sale_price'] ?? null,
            'tax_rate'      => $data['tax_rate'] ?? 20,
            'stock'         => $data['stock'] ?? 0,
            'stock_status'  => $data['stock_status'] ?? 'in_stock',
            'stock_alert_qty' => $data['stock_alert_qty'] ?? null,
            'order_limit_per_product'  => $data['order_limit_per_product'] ?? null,
            'order_limit_per_customer' => $data['order_limit_per_customer'] ?? null,
            'has_variations'=> $data['has_variations'] ?? 0,
            'shipping_type' => $data['shipping_type'] ?? 'domestic',
            'shipping_days_min' => $data['shipping_days_min'] ?? 1,
            'shipping_days_max' => $data['shipping_days_max'] ?? 2,
            'shipping_note' => $data['shipping_note'] ?? null,
            'is_featured'   => $data['is_featured'] ?? 0,
            'is_best_seller'=> $data['is_best_seller'] ?? 0,
            'is_most_clicked'=> $data['is_most_clicked'] ?? 0,
            'is_recommended'=> $data['is_recommended'] ?? 0,
            'video_url'     => $data['video_url'] ?? null,
            'video_file'    => $data['video_file'] ?? null,
            'warranty_period' => $data['warranty_period'] ?? null,
            'warranty_terms'  => $data['warranty_terms'] ?? null,
            'compatible_with' => $data['compatible_with'] ?? null,
            'is_active'     => $data['is_active'] ?? 1,
        ]);

        foreach ($translations as $lang => $trans) {
            Database::query(
                "INSERT INTO product_translations
                 (product_id, lang, name, short_desc, long_desc, meta_title, meta_desc, meta_keywords, og_title, og_desc)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $id, $lang,
                    $trans['name'],
                    $trans['short_desc'] ?? null,
                    $trans['long_desc'] ?? null,
                    $trans['meta_title'] ?? null,
                    $trans['meta_desc'] ?? null,
                    $trans['meta_keywords'] ?? null,
                    $trans['og_title'] ?? null,
                    $trans['og_desc'] ?? null,
                ]
            );
        }

        return $id;

        return $id;
    }

    // Fiyat dustu mu? (fiyat alarmi icin)
    public static function checkPriceAlerts(int $productId, float $newPrice): void
    {
        $alerts = Database::rows(
            "SELECT * FROM price_alerts WHERE product_id = ? AND is_notified = 0",
            [$productId]
        );

        foreach ($alerts as $alert) {
            if ($alert['target_price'] === null || $newPrice <= $alert['target_price']) {
                // Mail gonder
                $email = $alert['guest_email'];
                if (!$email && $alert['user_id']) {
                    $user  = User::find($alert['user_id']);
                    $email = $user['email'] ?? null;
                }

                if ($email) {
                    Database::query(
                        "UPDATE price_alerts SET is_notified = 1 WHERE id = ?",
                        [$alert['id']]
                    );
                }
            }
        }
    }
}
