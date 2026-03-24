<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Cache;
use App\Models\Setting;

class BrandController
{
    public function index(): void
    {
        $lang   = Session::get('admin_lang', 'tr');
        $brands = Database::rows(
            "SELECT b.*,
                    bt.name,
                    (SELECT COUNT(*) FROM products WHERE brand_id = b.id) as product_count
             FROM brands b
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             ORDER BY b.sort_order ASC, b.id ASC",
            [$lang]
        );

        Response::view('Admin.brands.index', [
            'title'    => 'Markalar',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'brands'   => $brands,
        ]);
    }

    public function create(): void
    {
        $languages = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");

        Response::view('Admin.brands.form', [
            'title'        => 'Yeni Marka',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'brand'        => null,
            'translations' => [],
            'languages'    => $languages,
        ]);
    }

    public function store(): void
    {
        $data      = Request::all();
        $languages = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        $hasName = false;
        foreach ($languages as $l) {
            if (!empty($data['name_' . $l['code']])) { $hasName = true; break; }
        }

        if (!$hasName) {
            Session::flash('error', 'Marka adı zorunludur.');
            Response::back();
        }

        Database::beginTransaction();
        try {
            $slug = $this->generateSlug($data['name_tr'] ?? $data['name_en'] ?? 'marka');

            Database::query(
                "INSERT INTO brands (slug, logo, website, is_active, sort_order, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $slug,
                    $this->handleLogo($_FILES['logo'] ?? null),
                    $data['website'] ?? null,
                    !empty($data['is_active']) ? 1 : 0,
                    (int)($data['sort_order'] ?? 0),
                ]
            );
            $brandId = Database::lastId();

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['name_' . $code])) continue;
                Database::query(
                    "INSERT INTO brand_translations (brand_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $brandId, $code,
                        $data['name_' . $code],
                        $data['short_desc_' . $code] ?? null,
                        $data['meta_title_' . $code] ?? null,
                        $data['meta_desc_' . $code] ?? null,
                    ]
                );
            }

            Database::commit();
            Logger::activity('brand_created', 'Brand', $brandId);

            // Cache temizle
            \App\Core\Cache::delete('brands_active_tr');
            \App\Core\Cache::delete('brands_active_en');

            Session::flash('success', 'Marka oluşturuldu.');
            Response::redirect(adminUrl('markalar'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Marka oluşturulurken hata: ' . $e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function edit(array $params): void
    {
        $id        = (int) $params['id'];
        $brand     = Database::row("SELECT * FROM brands WHERE id = ?", [$id]);
        if (!$brand) Response::abort(404);

        $languages    = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");
        $translations = [];
        foreach ($languages as $l) {
            $translations[$l['code']] = Database::row(
                "SELECT * FROM brand_translations WHERE brand_id = ? AND lang = ?",
                [$id, $l['code']]
            ) ?? [];
        }

        Response::view('Admin.brands.form', [
            'title'        => 'Marka Düzenle',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'brand'        => $brand,
            'translations' => $translations,
            'languages'    => $languages,
        ]);
    }

    public function update(array $params): void
    {
        $id        = (int) $params['id'];
        $data      = Request::all();
        $brand     = Database::row("SELECT * FROM brands WHERE id = ?", [$id]);
        if (!$brand) Response::abort(404);

        $languages = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        Database::beginTransaction();
        try {
            $logo = $brand['logo'];
            $newLogo = $this->handleLogo($_FILES['logo'] ?? null);
            if ($newLogo) $logo = $newLogo;

            Database::query(
                "UPDATE brands SET slug = ?, logo = ?, website = ?, is_active = ?, sort_order = ?
                 WHERE id = ?",
                [
                    $data['slug'] ?? $brand['slug'],
                    $logo,
                    $data['website'] ?? null,
                    !empty($data['is_active']) ? 1 : 0,
                    (int)($data['sort_order'] ?? 0),
                    $id,
                ]
            );

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['name_' . $code])) continue;
                Database::query(
                    "INSERT INTO brand_translations (brand_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     name = VALUES(name), description = VALUES(description),
                     meta_title = VALUES(meta_title), meta_desc = VALUES(meta_desc)",
                    [
                        $id, $code,
                        $data['name_' . $code],
                        $data['short_desc_' . $code] ?? null,
                        $data['meta_title_' . $code] ?? null,
                        $data['meta_desc_' . $code] ?? null,
                    ]
                );
            }

            Database::commit();
            Logger::activity('brand_updated', 'Brand', $id);

            // Cache temizle
            \App\Core\Cache::delete('brands_active_tr');
            \App\Core\Cache::delete('brands_active_en');

            Session::flash('success', 'Marka güncellendi.');
            Response::redirect(adminUrl('markalar'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Marka güncellenirken hata: ' . $e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function destroy(array $params): void
    {
        $id = (int) $params['id'];

        $productCount = Database::value(
            "SELECT COUNT(*) FROM products WHERE brand_id = ?", [$id]
        );

        if ($productCount > 0) {
            Session::flash('error', "Bu markada $productCount ürün var. Önce ürünleri taşıyın.");
            Response::redirect(adminUrl('markalar'));
        }

        $brand = Database::row("SELECT logo FROM brands WHERE id = ?", [$id]);
        if ($brand && $brand['logo']) {
            $path = PUB_PATH . '/uploads/brands/' . $brand['logo'];
            if (file_exists($path)) unlink($path);
        }

        Database::query("DELETE FROM brands WHERE id = ?", [$id]);
        Logger::activity('brand_deleted', 'Brand', $id);
        Session::flash('success', 'Marka silindi.');
        Response::redirect(adminUrl('markalar'));
    }

    private function handleLogo(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','svg'];
        if (!in_array($ext, $allowed)) return null;

        $uploadDir = PUB_PATH . '/uploads/brands/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid('brand_') . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            return $filename;
        }
        return null;
    }

    private function generateSlug(string $text, int $exceptId = 0): string
    {
        $base = slugify($text);
        $slug = $base;
        $i    = 1;
        while (Database::value(
            "SELECT COUNT(*) FROM brands WHERE slug = ? AND id != ?",
            [$slug, $exceptId]
        )) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    // Ürün formundan AJAX hızlı ekle
    public function storeAjax(): void
    {
        header('Content-Type: application/json');
        try {
            $name = trim(Request::post('name', ''));
            if (!$name) {
                echo json_encode(['success'=>false,'message'=>'Ad boş olamaz.']);
                exit;
            }
            $slug = $this->generateSlug($name);
            Database::query(
                "INSERT INTO brands (slug,is_active,sort_order,created_at) VALUES (?,1,0,NOW())",
                [$slug]
            );
            $id = Database::lastId();
            $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
            foreach ($languages as $l) {
                Database::query(
                    "INSERT INTO brand_translations (brand_id,lang,name) VALUES (?,?,?)",
                    [$id, $l['code'], $name]
                );
            }
            Cache::delete('brands_active_tr');
            Logger::activity('brand_quick_created', 'Brand', $id);
            echo json_encode(['success'=>true,'id'=>$id,'name'=>$name]);
        } catch (\Throwable $e) {
            Logger::error('Marka hızlı ekle hatası: ' . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Sunucu hatası: ' . $e->getMessage()]);
        }
        exit;
    }
}
