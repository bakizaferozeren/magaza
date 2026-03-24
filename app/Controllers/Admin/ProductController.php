<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Validator;
use App\Core\Logger;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Setting;

class ProductController
{
    // Urun listesi
    public function index(array $params = []): void
    {
        $page    = (int) Request::get('page', 1);
        $search  = Request::get('search', '');
        $lang    = Session::get('admin_lang', 'tr');

        // AJAX ürün arama (değerlendirme modal için)
        if (Request::get('ajax') === '1') {
            $products = Database::rows(
                "SELECT p.id, pt.name, p.sku
                 FROM products p
                 LEFT JOIN product_translations pt ON pt.product_id=p.id AND pt.lang=?
                 WHERE p.is_active=1 AND (pt.name LIKE ? OR p.sku LIKE ?)
                 ORDER BY pt.name ASC LIMIT 20",
                [$lang, "%$search%", "%$search%"]
            );
            header('Content-Type: application/json');
            echo json_encode($products);
            exit;
        }

        $filters = [
            'search'       => $search,
            'category_id'  => Request::get('category_id'),
            'brand_id'     => Request::get('brand_id'),
            'is_active'    => Request::get('is_active'),
            'stock_status' => Request::get('stock_status'),
        ];

        $filters = array_filter($filters, fn($v) => $v !== null && $v !== '');

        $result     = Product::adminList($page, 20, $filters, $lang);
        $categories = Category::flatList($lang);
        $brands     = Brand::activeList($lang);

        Response::view('Admin.products.index', [
            'title'      => 'Ürünler',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'products'   => $result['items'],
            'pagination' => $result,
            'categories' => $categories,
            'brands'     => $brands,
            'filters'    => $filters,
            'search'     => $search,
        ]);
    }

    // Urun ekle formu
    public function create(array $params = []): void
    {
        $lang       = Session::get('admin_lang', 'tr');
        $categories = Category::flatList($lang);
        $brands     = Brand::activeList($lang);
        $languages  = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");
        $varTypes   = Database::rows("SELECT * FROM variation_types ORDER BY sort_order");

        Response::view('Admin.products.form', [
            'title'      => 'Yeni Ürün Ekle',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'product'    => null,
            'categories' => $categories,
            'brands'     => $brands,
            'languages'  => $languages,
            'varTypes'   => $varTypes,
            'images'     => [],
            'attributes' => [],
            'variations' => [],
            'translations' => [],
        ]);
    }

    // Urun kaydet
    public function store(array $params = []): void
    {
        $data = Request::all();

        $validator = Validator::make($data, [
            'price' => 'required|numeric',
            'stock' => 'required|integer',
        ]);

        $hasName = false;
        foreach (Database::rows("SELECT code FROM languages WHERE is_active = 1") as $lang) {
            if (!empty($data['name_' . $lang['code']])) { $hasName = true; break; }
        }

        if (!$hasName || $validator->fails()) {
            if (Request::isAjax()) {
                Response::json(['success'=>false,'message'=>'Lütfen zorunlu alanları doldurun (ürün adı ve fiyat).']);
            }
            Session::flash('error', 'Lütfen zorunlu alanları doldurun.');
            Session::flash('old', $data);
            Response::back();
        }

        Database::beginTransaction();
        try {
            $slug = $this->generateSlug($data['name_tr'] ?? $data['name_en'] ?? 'urun');

            $productId = Product::createWithTranslation(
                $this->prepareProductData($data, $slug),
                $this->prepareTranslations($data)
            );

            $this->handleImages($productId, $_FILES);
            $this->handleImageUrls($productId, $data);
            $this->handleAttributes($productId, $data);

            if (!empty($data['has_variations'])) {
                $this->handleVariations($productId, $data);
            }

            Database::commit();
            Logger::activity('product_created', 'Product', $productId);

            if (Request::isAjax()) {
                // Yeni eklenen görselleri döndür
                $images = Database::rows(
                    "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order", [$productId]
                );
                Response::json([
                    'success'    => true,
                    'message'    => 'Ürün başarıyla eklendi.',
                    'product_id' => $productId,
                    'slug'       => $slug,
                    'edit_url'   => adminUrl('urunler/' . $productId . '/duzenle'),
                    'images'     => $images,
                ]);
            }

            Session::flash('success', 'Ürün başarıyla eklendi.');
            Response::redirect(adminUrl('urunler/' . $productId . '/duzenle'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Ürün eklenirken hata: ' . $e->getMessage());
            if (Request::isAjax()) {
                Response::json(['success'=>false,'message'=>'Hata: '.$e->getMessage()]);
            }
            Session::flash('error', 'Ürün eklenirken bir hata oluştu.');
            Response::back();
        }
    }

    // Urun duzenle formu
    public function edit(array $params = []): void
    {
        $id   = (int) $params['id'];
        $lang = Session::get('admin_lang', 'tr');

        $product = Product::find($id);
        if (!$product) Response::abort(404);

        $translations = [];
        $langs = Database::rows("SELECT * FROM languages WHERE is_active = 1");
        foreach ($langs as $l) {
            $translations[$l['code']] = Database::row(
                "SELECT * FROM product_translations WHERE product_id = ? AND lang = ?",
                [$id, $l['code']]
            ) ?? [];
        }

        $categories = Category::flatList($lang);
        $brands     = Brand::activeList($lang);
        $images     = Product::images($id);
        $attributes = Product::attributes($id);
        $variations = Product::variations($id);
        $varTypes   = Database::rows("SELECT * FROM variation_types ORDER BY sort_order");

        Response::view('Admin.products.form', [
            'title'        => 'Ürün Düzenle',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'product'      => $product,
            'translations' => $translations,
            'categories'   => $categories,
            'brands'       => $brands,
            'languages'    => $langs,
            'images'       => $images,
            'attributes'   => $attributes,
            'variations'   => $variations,
            'varTypes'     => $varTypes,
        ]);
    }

    // Urun guncelle
    public function update(array $params = []): void
    {
        $id   = (int) $params['id'];
        $data = Request::all();

        $product = Product::find($id);
        if (!$product) {
            if (Request::isAjax()) Response::json(['success'=>false,'message'=>'Ürün bulunamadı.']);
            Response::abort(404);
        }

        Database::beginTransaction();
        try {
            $slug = $data['slug'] ?? $product['slug'];
            if (Product::exists('slug', $slug, $id)) {
                $slug = $this->generateSlug($slug, $id);
            }

            Product::update($id, $this->prepareProductData($data, $slug));

            foreach ($this->prepareTranslations($data) as $lang => $trans) {
                Database::query(
                    "INSERT INTO product_translations (product_id, lang, name, short_desc, long_desc, meta_title, meta_desc, meta_keywords, og_title, og_desc)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     name = VALUES(name), short_desc = VALUES(short_desc), long_desc = VALUES(long_desc),
                     meta_title = VALUES(meta_title), meta_desc = VALUES(meta_desc),
                     meta_keywords = VALUES(meta_keywords), og_title = VALUES(og_title), og_desc = VALUES(og_desc)",
                    [$id, $lang, $trans['name'], $trans['short_desc'] ?? null, $trans['long_desc'] ?? null,
                     $trans['meta_title'] ?? null, $trans['meta_desc'] ?? null, $trans['meta_keywords'] ?? null,
                     $trans['og_title'] ?? null, $trans['og_desc'] ?? null]
                );
            }

            $this->handleImages($id, $_FILES);
            $this->handleImageUrls($id, $data);

            Database::query("DELETE FROM product_attributes WHERE product_id = ?", [$id]);
            $this->handleAttributes($id, $data);

            if (!empty($data['has_variations'])) {
                $this->handleVariations($id, $data);
            }

            Database::commit();
            Logger::activity('product_updated', 'Product', $id);

            if (Request::isAjax()) {
                $images = Database::rows(
                    "SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order", [$id]
                );
                Response::json([
                    'success'    => true,
                    'message'    => 'Ürün başarıyla güncellendi.',
                    'product_id' => $id,
                    'slug'       => $slug,
                    'edit_url'   => adminUrl('urunler/' . $id . '/duzenle'),
                    'images'     => $images,
                ]);
            }

            Session::flash('success', 'Ürün başarıyla güncellendi.');
            Response::redirect(adminUrl('urunler/' . $id . '/duzenle'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Ürün güncellenirken hata: ' . $e->getMessage());
            if (Request::isAjax()) {
                Response::json(['success'=>false,'message'=>'Hata: '.$e->getMessage()]);
            }
            Session::flash('error', 'Ürün güncellenirken bir hata oluştu.');
            Response::back();
        }
    }

    // Urun sil
    public function destroy(array $params = []): void
    {
        $id = (int) $params['id'];

        $product = Product::find($id);
        if (!$product) Response::abort(404);

        $pubPath = defined('PUB_PATH') ? PUB_PATH : dirname(__DIR__, 3) . '/public';

        // Gorselleri sil
        $images = Product::images($id);
        foreach ($images as $img) {
            $path = $pubPath . '/uploads/products/' . $img['path'];
            if (file_exists($path)) unlink($path);
        }

        Product::delete($id);
        Logger::activity('product_deleted', 'Product', $id);
        Session::flash('success', 'Ürün silindi.');
        Response::redirect(adminUrl('urunler'));
    }

    // Gorsel sil
    public function deleteImage(array $params = []): void
    {
        $imageId = (int) Request::post('image_id');
        $image   = Database::row("SELECT * FROM product_images WHERE id = ?", [$imageId]);
        $pubPath = defined('PUB_PATH') ? PUB_PATH : dirname(__DIR__, 3) . '/public';

        if ($image) {
            $path = $pubPath . '/uploads/products/' . $image['path'];
            if (file_exists($path)) unlink($path);
            Database::query("DELETE FROM product_images WHERE id = ?", [$imageId]);
        }

        Response::success(null, 'Görsel silindi.');
    }

    // Gorsel sirala
    public function sortImages(array $params = []): void
    {
        $order = Request::json()['order'] ?? [];
        foreach ($order as $i => $imageId) {
            Database::query(
                "UPDATE product_images SET sort_order = ? WHERE id = ?",
                [$i, $imageId]
            );
        }
        Response::success(null, 'Sıralama güncellendi.');
    }

    // Slug kontrol (AJAX)
    public function checkSlug(array $params = []): void
    {
        $slug      = slugify(Request::post('slug', ''));
        $productId = (int) Request::post('product_id', 0);
        $exists    = Product::exists('slug', $slug, $productId);

        Response::json([
            'slug'   => $exists ? $this->generateSlug($slug, $productId) : $slug,
            'exists' => $exists,
        ]);
    }

    // CSV Export
    public function exportCsv(array $params = []): void
    {
        $products = Database::rows(
            "SELECT p.*, pt.name, pt.short_desc, pt.long_desc, pt.meta_title, pt.meta_desc,
                    ct.name as category_name, bt.name as brand_name
             FROM products p
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = 'tr'
             LEFT JOIN brands b ON b.id = p.brand_id
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = 'tr'
             ORDER BY p.id ASC"
        );

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="urunler_' . date('Y-m-d') . '.csv"');
        header('Pragma: no-cache');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM

        fputcsv($output, [
            'ID', 'Ad', 'Slug', 'SKU', 'Barkod', 'Kategori', 'Marka',
            'Fiyat', 'İndirimli Fiyat', 'Vergi', 'Stok', 'Stok Durumu',
            'Kargo', 'Aktif', 'Kısa Açıklama', 'Meta Başlık', 'Meta Açıklama',
        ]);

        foreach ($products as $p) {
            fputcsv($output, [
                $p['id'], $p['name'], $p['slug'], $p['sku'], $p['barcode'],
                $p['category_name'], $p['brand_name'], $p['price'], $p['sale_price'],
                $p['tax_rate'], $p['stock'], $p['stock_status'], $p['shipping_type'],
                $p['is_active'], $p['short_desc'], $p['meta_title'], $p['meta_desc'],
            ]);
        }

        fclose($output);
        exit;
    }

    // CSV Import
    public function importCsv(array $params = []): void
    {
        $file = $_FILES['csv'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'CSV dosyası yüklenemedi.');
            Response::back();
        }

        $imported = 0;
        $errors   = 0;

        if (($handle = fopen($file['tmp_name'], 'r')) !== false) {
            $headers = fgetcsv($handle); // başlık satırını atla
            while (($row = fgetcsv($handle)) !== false) {
                try {
                    // Basit import - sadece fiyat ve stok guncelle
                    if (!empty($row[2])) { // slug
                        Database::query(
                            "UPDATE products SET price = ?, stock = ? WHERE slug = ?",
                            [$row[7] ?? 0, $row[10] ?? 0, $row[2]]
                        );
                        $imported++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                }
            }
            fclose($handle);
        }

        Session::flash('success', "{$imported} ürün güncellendi." . ($errors ? " {$errors} hata oluştu." : ''));
        Response::redirect(adminUrl('urunler'));
    }

    // --- YARDIMCI METODLAR ---

    private function prepareProductData(array $data, string $slug): array
    {
        return [
            'category_id'   => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'brand_id'      => !empty($data['brand_id']) ? (int)$data['brand_id'] : null,
            'slug'          => $slug,
            'sku'           => $data['sku'] ?? null,
            'barcode'       => $data['barcode'] ?? null,
            'price'         => (float)($data['price'] ?? 0),
            'sale_price'    => !empty($data['sale_price']) ? (float)$data['sale_price'] : null,
            'tax_rate'      => (int)($data['tax_rate'] ?? 20),
            'stock'         => (int)($data['stock'] ?? 0),
            'stock_status'  => $data['stock_status'] ?? 'in_stock',
            'stock_alert_qty' => !empty($data['stock_alert_qty']) ? (int)$data['stock_alert_qty'] : null,
            'order_limit_per_product'  => !empty($data['order_limit_per_product']) ? (int)$data['order_limit_per_product'] : null,
            'order_limit_per_customer' => !empty($data['order_limit_per_customer']) ? (int)$data['order_limit_per_customer'] : null,
            'has_variations'=> !empty($data['has_variations']) ? 1 : 0,
            'shipping_type' => $data['shipping_type'] ?? 'domestic',
            'shipping_days_min' => (int)($data['shipping_days_min'] ?? 1),
            'shipping_days_max' => (int)($data['shipping_days_max'] ?? 2),
            'shipping_note' => $data['shipping_note'] ?? null,
            'is_featured'   => !empty($data['is_featured']) ? 1 : 0,
            'is_best_seller'=> !empty($data['is_best_seller']) ? 1 : 0,
            'is_most_clicked'=> !empty($data['is_most_clicked']) ? 1 : 0,
            'is_recommended'=> !empty($data['is_recommended']) ? 1 : 0,
            'video_url'     => $data['video_url'] ?? null,
            'warranty_period' => $data['warranty_period'] ?? null,
            'warranty_terms'  => $data['warranty_terms'] ?? null,
            'compatible_with' => $data['compatible_with'] ?? null,
            'is_active'     => !empty($data['is_active']) ? 1 : 0,
        ];
    }

    private function prepareTranslations(array $data): array
    {
        $translations = [];
        $langs = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        foreach ($langs as $l) {
            $code = $l['code'];
            if (!empty($data['name_' . $code])) {
                $translations[$code] = [
                    'name'         => $data['name_' . $code],
                    'short_desc'   => $data['short_desc_' . $code] ?? null,
                    'long_desc'    => Request::raw('long_desc_' . $code) ?? null,
                    'meta_title'   => $data['meta_title_' . $code] ?? null,
                    'meta_desc'    => $data['meta_desc_' . $code] ?? null,
                    'meta_keywords'=> $data['meta_keywords_' . $code] ?? null,
                    'og_title'     => $data['og_title_' . $code] ?? null,
                    'og_desc'      => $data['og_desc_' . $code] ?? null,
                ];
            }
        }

        return $translations;
    }

    private function handleImages(int $productId, array $files): void
    {
        if (empty($files['images']['name'][0])) return;

        // PUB_PATH tanımlı değilse __DIR__'den hesapla
        $pubPath   = defined('PUB_PATH') ? PUB_PATH : dirname(__DIR__, 3) . '/public';
        $uploadDir = $pubPath . '/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $hascover = Database::value(
            "SELECT COUNT(*) FROM product_images WHERE product_id = ? AND is_cover = 1",
            [$productId]
        );

        foreach ($files['images']['tmp_name'] as $i => $tmpName) {
            if (!$tmpName || $files['images']['error'][$i] !== UPLOAD_ERR_OK) continue;

            $ext     = strtolower(pathinfo($files['images']['name'][$i], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (!in_array($ext, $allowed)) continue;

            $filename = uniqid('prod_') . '.' . $ext;
            $destPath = $uploadDir . $filename;

            if (move_uploaded_file($tmpName, $destPath)) {
                $isCover = (!$hascover && $i === 0) ? 1 : 0;
                Database::query(
                    "INSERT INTO product_images (product_id, path, is_cover, sort_order) VALUES (?, ?, ?, ?)",
                    [$productId, $filename, $isCover, $i]
                );
                if (!$hascover && $i === 0) $hascover = 1;
            }
        }
    }

    private function handleImageUrls(int $productId, array $data): void
    {
        if (empty($data['image_urls'])) return;

        $pubPath   = defined('PUB_PATH') ? PUB_PATH : dirname(__DIR__, 3) . '/public';
        $uploadDir = $pubPath . '/uploads/products/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $hasCover = (int) Database::value(
            "SELECT COUNT(*) FROM product_images WHERE product_id=? AND is_cover=1", [$productId]
        );
        $sort = (int) Database::value(
            "SELECT COALESCE(MAX(sort_order),0)+1 FROM product_images WHERE product_id=?", [$productId]
        );

        foreach ($data['image_urls'] as $url) {
            $url = trim($url);
            if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) continue;

            // Uzantıyı URL'den çek
            $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
            $ext = preg_replace('/[^a-z]/', '', $ext); // parametrelerden temizle
            if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) $ext = 'jpg';

            $filename = uniqid('url_') . '.' . $ext;
            $dest     = $uploadDir . $filename;

            // Dosyayı indir (file_get_contents veya cURL)
            $content = @file_get_contents($url, false, stream_context_create([
                'http' => ['timeout'=>10, 'user_agent'=>'Mozilla/5.0']
            ]));

            if ($content === false && function_exists('curl_init')) {
                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT        => 10,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_USERAGENT      => 'Mozilla/5.0',
                ]);
                $content = curl_exec($ch);
                curl_close($ch);
            }

            if (!$content) continue;

            file_put_contents($dest, $content);

            // Gerçekten görsel mi?
            if (!@getimagesize($dest)) { @unlink($dest); continue; }

            $isCover = !$hasCover ? 1 : 0;
            Database::query(
                "INSERT INTO product_images (product_id, path, is_cover, sort_order) VALUES (?,?,?,?)",
                [$productId, $filename, $isCover, $sort++]
            );
            if (!$hasCover) $hasCover = 1;
        }
    }

    private function handleAttributes(int $productId, array $data): void
    {
        if (empty($data['attr_name'])) return;

        foreach ($data['attr_name'] as $i => $name) {
            $value = $data['attr_value'][$i] ?? '';
            if (!$name || !$value) continue;

            Database::query(
                "INSERT INTO product_attributes (product_id, attr_name, attr_value, sort_order) VALUES (?, ?, ?, ?)",
                [$productId, $name, $value, $i]
            );
        }
    }

    private function handleVariations(int $productId, array $data): void
    {
        if (empty($data['variation'])) return;

        foreach ($data['variation'] as $varData) {
            $varId = Database::query(
                "INSERT INTO product_variations (product_id, sku, price, sale_price, stock, stock_status, is_active)
                 VALUES (?, ?, ?, ?, ?, ?, 1)",
                [
                    $productId,
                    $varData['sku'] ?? null,
                    !empty($varData['price']) ? (float)$varData['price'] : null,
                    !empty($varData['sale_price']) ? (float)$varData['sale_price'] : null,
                    (int)($varData['stock'] ?? 0),
                    $varData['stock_status'] ?? 'in_stock',
                ]
            );

            $varId = Database::lastId();

            if (!empty($varData['options'])) {
                foreach ($varData['options'] as $typeId => $optionId) {
                    Database::query(
                        "INSERT INTO product_variation_options (product_variation_id, variation_type_id, variation_option_id)
                         VALUES (?, ?, ?)",
                        [$varId, $typeId, $optionId]
                    );
                }
            }
        }
    }

    private function generateSlug(string $text, int $exceptId = 0): string
    {
        $base = slugify($text);
        $slug = $base;
        $i    = 1;

        while (Product::exists('slug', $slug, $exceptId)) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
