<?php

namespace App\Controllers\Store;

use App\Core\Database;
use App\Core\Response;
use App\Core\Session;
use App\Core\Logger;
use App\Core\Request;
use App\Models\Setting;

class HomeController
{
    public function index(): void
    {
        $lang = Session::get('lang', 'tr');

        // Sliderlar
        $sliders = Database::rows(
            "SELECT * FROM sliders WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 5"
        );

        // Kategoriler
        $categories = Database::rows(
            "SELECT c.*, ct.name
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order ASC, c.id ASC
             LIMIT 12",
            [$lang]
        );

        // Öne çıkan ürünler (kapak + ikinci görsel hover için)
        $featuredProducts = $this->getProducts($lang, 'is_featured = 1', 8);

        // En çok satanlar
        $bestSellers = $this->getProducts($lang, 'is_best_seller = 1', 8);

        // Yeni gelenler
        $newArrivals = $this->getProducts($lang, '1=1', 8, 'p.created_at DESC');

        // Blog yazıları
        $blogs = Database::rows(
            "SELECT b.*, bt.title, bt.excerpt, b.slug as blog_slug
             FROM blogs b
             LEFT JOIN blog_translations bt ON bt.blog_id = b.id AND bt.lang = ?
             WHERE b.is_active = 1
             ORDER BY b.published_at DESC
             LIMIT 3",
            [$lang]
        );

        // Bannerlar (position, image, link, is_active)
        $banners = Database::rows(
            "SELECT * FROM banners WHERE is_active = 1 ORDER BY id ASC LIMIT 4"
        );

        // Site ayarları
        $settings = [
            'site_name'         => Setting::get('site_name', 'Mağazam'),
            'site_logo'         => Setting::get('site_logo'),
            'site_description'  => Setting::get('site_description'),
            'whatsapp_number'   => Setting::get('whatsapp_number'),
            'free_shipping_over'=> Setting::get('shipping_free_over', 500),
            'facebook_url'      => Setting::get('facebook_url'),
            'instagram_url'     => Setting::get('instagram_url'),
            'twitter_url'       => Setting::get('twitter_url'),
            'youtube_url'       => Setting::get('youtube_url'),
            'site_phone'        => Setting::get('site_phone'),
            'site_email'        => Setting::get('site_email'),
        ];

        // Navigasyon kategorileri (layout için)
        $navCategories = Database::rows(
            "SELECT c.id, c.slug, ct.name
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.is_active = 1
             ORDER BY c.sort_order ASC, c.id ASC
             LIMIT 8",
            [$lang]
        );

        Response::view('Store.home.index', compact(
            'sliders', 'categories', 'featuredProducts',
            'bestSellers', 'newArrivals', 'blogs', 'banners',
            'settings', 'lang', 'navCategories'
        ));
    }

    private function getProducts(string $lang, string $where, int $limit, string $order = 'p.id DESC'): array
    {
        $products = Database::rows(
            "SELECT p.id, p.slug, p.price, p.sale_price, p.stock, p.stock_status,
                    p.is_featured, p.is_best_seller,
                    pt.name, pt.short_desc,
                    ct.name as category_name, c.slug as category_slug
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = ?
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE p.is_active = 1 AND $where
             ORDER BY $order
             LIMIT $limit",
            [$lang, $lang]
        );

        // Her ürün için ilk 2 görseli çek (hover efekti için)
        foreach ($products as &$p) {
            $images = Database::rows(
                "SELECT path, is_cover FROM product_images WHERE product_id = ? ORDER BY is_cover DESC, sort_order ASC LIMIT 2",
                [$p['id']]
            );
            $p['cover_image']  = $images[0]['path'] ?? null;
            $p['hover_image']  = $images[1]['path'] ?? null;
            $p['discount_pct'] = ($p['sale_price'] && $p['price'] > 0)
                ? round((1 - $p['sale_price'] / $p['price']) * 100)
                : 0;
        }

        return $products;
    }

    public function newsletter(): void
    {
        $email = trim(Request::post('email', ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::json(['success' => false, 'message' => 'Geçersiz e-posta adresi.']);
        }

        $exists = Database::value(
            "SELECT COUNT(*) FROM newsletter_subscribers WHERE email = ?", [$email]
        );

        if (!$exists) {
            Database::query(
                "INSERT INTO newsletter_subscribers (email, token, created_at) VALUES (?, ?, NOW())",
                [$email, bin2hex(random_bytes(16))]
            );
        }

        Response::json(['success' => true, 'message' => 'Bültenimize başarıyla abone oldunuz!']);
    }

    public function cookieConsent(): void
    {
        Session::set('cookie_consent', true);
        Response::json(['success' => true]);
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        echo '<sitemap><loc>' . url('sitemap-products.xml') . '</loc></sitemap>';
        echo '<sitemap><loc>' . url('sitemap-categories.xml') . '</loc></sitemap>';
        echo '<sitemap><loc>' . url('sitemap-blog.xml') . '</loc></sitemap>';
        echo '</sitemapindex>';
        exit;
    }

    public function robots(): void
    {
        header('Content-Type: text/plain');
        echo "User-agent: *\nAllow: /\nDisallow: /yonetim/\nSitemap: " . url('sitemap.xml');
        exit;
    }

    public function sitemapProducts(): void { $this->outputSitemap('products'); }
    public function sitemapCategories(): void { $this->outputSitemap('categories'); }
    public function sitemapBlog(): void { $this->outputSitemap('blog'); }

    private function outputSitemap(string $type): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        if ($type === 'products') {
            $rows = Database::rows("SELECT slug, updated_at FROM products WHERE is_active = 1");
            foreach ($rows as $r) {
                echo "<url><loc>" . url('urun/' . $r['slug']) . "</loc><lastmod>" . date('Y-m-d', strtotime($r['updated_at'] ?? 'now')) . "</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>";
            }
        } elseif ($type === 'categories') {
            $rows = Database::rows("SELECT slug FROM categories WHERE is_active = 1");
            foreach ($rows as $r) {
                echo "<url><loc>" . url('kategori/' . $r['slug']) . "</loc><changefreq>weekly</changefreq><priority>0.7</priority></url>";
            }
        } elseif ($type === 'blog') {
            $rows = Database::rows("SELECT slug FROM blogs WHERE is_active = 1");
            foreach ($rows as $r) {
                echo "<url><loc>" . url('blog/' . $r['slug']) . "</loc><changefreq>monthly</changefreq><priority>0.5</priority></url>";
            }
        }

        echo '</urlset>';
        exit;
    }

    public function unsubscribe(array $params): void
    {
        $token = $params['token'] ?? '';
        Database::query(
            "UPDATE newsletter_subscribers SET is_active = 0 WHERE token = ?", [$token]
        );
        Response::redirect(url('?unsubscribed=1'));
    }

    public function merchantFeed(): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        $products = Database::rows(
            "SELECT p.id, p.slug, p.price, p.sale_price, pt.name, pt.short_desc,
                    (SELECT path FROM product_images WHERE product_id = p.id AND is_cover = 1 LIMIT 1) as cover
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE p.is_active = 1 LIMIT 1000"
        );
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"><channel>';
        foreach ($products as $p) {
            $price = $p['sale_price'] ?: $p['price'];
            echo "<item>";
            echo "<g:id>{$p['id']}</g:id>";
            echo "<g:title><![CDATA[" . htmlspecialchars($p['name'] ?? '') . "]]></g:title>";
            echo "<g:description><![CDATA[" . htmlspecialchars($p['short_desc'] ?? '') . "]]></g:description>";
            echo "<g:link>" . url('urun/' . $p['slug']) . "</g:link>";
            echo "<g:image_link>" . ($p['cover'] ? uploadUrl('products/' . $p['cover']) : '') . "</g:image_link>";
            echo "<g:price>" . number_format((float)$price, 2) . " TRY</g:price>";
            echo "<g:availability>in stock</g:availability>";
            echo "</item>";
        }
        echo '</channel></rss>';
        exit;
    }
}
