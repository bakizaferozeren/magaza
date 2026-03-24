<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Core\Cache;
use App\Models\Category;
use App\Models\Setting;

class CategoryController
{
    public function index(): void
    {
        $lang       = Session::get('admin_lang', 'tr');
        $categories = Database::rows(
            "SELECT c.*,
                    ct.name,
                    parent_ct.name as parent_name,
                    (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             LEFT JOIN categories parent_c ON parent_c.id = c.parent_id
             LEFT JOIN category_translations parent_ct ON parent_ct.category_id = parent_c.id AND parent_ct.lang = ?
             ORDER BY c.parent_id ASC, c.sort_order ASC, c.id ASC",
            [$lang, $lang]
        );

        Response::view('Admin.categories.index', [
            'title'      => 'Kategoriler',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'categories' => $categories,
        ]);
    }

    public function create(): void
    {
        $lang      = Session::get('admin_lang', 'tr');
        $languages = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");
        $parents   = Database::rows(
            "SELECT c.id, ct.name FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.parent_id IS NULL ORDER BY ct.name",
            [$lang]
        );

        Response::view('Admin.categories.form', [
            'title'        => 'Yeni Kategori',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'category'     => null,
            'translations' => [],
            'languages'    => $languages,
            'parents'      => $parents,
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
            Session::flash('error', 'En az bir dilde kategori adı zorunludur.');
            Response::back();
        }

        Database::beginTransaction();
        try {
            $slug = $this->generateSlug($data['name_tr'] ?? $data['name_en'] ?? 'kategori');

            $categoryId = Database::query(
                "INSERT INTO categories (parent_id, slug, image, is_active, sort_order, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    !empty($data['parent_id']) ? (int)$data['parent_id'] : null,
                    $slug,
                    $this->handleImage($_FILES['image'] ?? null),
                    !empty($data['is_active']) ? 1 : 0,
                    (int)($data['sort_order'] ?? 0),
                ]
            );
            $categoryId = Database::lastId();

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['name_' . $code])) continue;
                Database::query(
                    "INSERT INTO category_translations (category_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [
                        $categoryId, $code,
                        $data['name_' . $code],
                        $data['short_desc_' . $code] ?? null,
                        $data['meta_title_' . $code] ?? null,
                        $data['meta_desc_' . $code] ?? null,
                    ]
                );
            }

            Database::commit();
            Logger::activity('category_created', 'Category', $categoryId);

            \App\Core\Cache::delete('categories_tree_tr');
            \App\Core\Cache::delete('categories_tree_en');

            Session::flash('success', 'Kategori oluşturuldu.');
            Response::redirect(adminUrl('kategoriler'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Kategori oluşturulurken hata: ' . $e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function edit(array $params): void
    {
        $id        = (int) $params['id'];
        $lang      = Session::get('admin_lang', 'tr');
        $category  = Database::row("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) Response::abort(404);

        $languages    = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");
        $translations = [];
        foreach ($languages as $l) {
            $translations[$l['code']] = Database::row(
                "SELECT * FROM category_translations WHERE category_id = ? AND lang = ?",
                [$id, $l['code']]
            ) ?? [];
        }

        $parents = Database::rows(
            "SELECT c.id, ct.name FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.parent_id IS NULL AND c.id != ?
             ORDER BY ct.name",
            [$lang, $id]
        );

        Response::view('Admin.categories.form', [
            'title'        => 'Kategori Düzenle',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'category'     => $category,
            'translations' => $translations,
            'languages'    => $languages,
            'parents'      => $parents,
        ]);
    }

    public function update(array $params): void
    {
        $id        = (int) $params['id'];
        $data      = Request::all();
        $category  = Database::row("SELECT * FROM categories WHERE id = ?", [$id]);
        if (!$category) Response::abort(404);

        $languages = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        Database::beginTransaction();
        try {
            $slug = $data['slug'] ?? $category['slug'];

            // Gorsel
            $image = $category['image'];
            $newImage = $this->handleImage($_FILES['image'] ?? null);
            if ($newImage) $image = $newImage;

            Database::query(
                "UPDATE categories SET
                    parent_id = ?, slug = ?, image = ?, is_active = ?,
                    sort_order = ?
                 WHERE id = ?",
                [
                    !empty($data['parent_id']) ? (int)$data['parent_id'] : null,
                    $slug, $image,
                    !empty($data['is_active']) ? 1 : 0,
                    (int)($data['sort_order'] ?? 0),
                    $id,
                ]
            );

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['name_' . $code])) continue;
                Database::query(
                    "INSERT INTO category_translations (category_id, lang, name, description, meta_title, meta_desc)
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
            Logger::activity('category_updated', 'Category', $id);

            \App\Core\Cache::delete('categories_tree_tr');
            \App\Core\Cache::delete('categories_tree_en');

            Session::flash('success', 'Kategori güncellendi.');
            Response::redirect(adminUrl('kategoriler'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Kategori güncellenirken hata: ' . $e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function destroy(array $params): void
    {
        $id = (int) $params['id'];

        $productCount = Database::value(
            "SELECT COUNT(*) FROM products WHERE category_id = ?", [$id]
        );

        if ($productCount > 0) {
            Session::flash('error', "Bu kategoride $productCount ürün var. Önce ürünleri taşıyın.");
            Response::redirect(adminUrl('kategoriler'));
        }

        // Alt kategorileri ana kategori yap
        Database::query("UPDATE categories SET parent_id = NULL WHERE parent_id = ?", [$id]);

        // Gorsel sil
        $cat = Database::row("SELECT image FROM categories WHERE id = ?", [$id]);
        if ($cat && $cat['image']) {
            $path = PUB_PATH . '/uploads/categories/' . $cat['image'];
            if (file_exists($path)) unlink($path);
        }

        Database::query("DELETE FROM categories WHERE id = ?", [$id]);
        Logger::activity('category_deleted', 'Category', $id);
        Session::flash('success', 'Kategori silindi.');
        Response::redirect(adminUrl('kategoriler'));
    }

    // Sıralama AJAX
    public function updateSort(): void
    {
        $order = Request::json()['order'] ?? [];
        foreach ($order as $i => $id) {
            Database::query("UPDATE categories SET sort_order = ? WHERE id = ?", [$i, $id]);
        }
        Response::success(null, 'Sıralama güncellendi.');
    }

    private function handleImage(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;

        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','webp'];
        if (!in_array($ext, $allowed)) return null;

        $uploadDir = PUB_PATH . '/uploads/categories/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = uniqid('cat_') . '.' . $ext;
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
            "SELECT COUNT(*) FROM categories WHERE slug = ? AND id != ?",
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
                "INSERT INTO categories (slug,is_active,sort_order,created_at) VALUES (?,1,0,NOW())",
                [$slug]
            );
            $id = Database::lastId();
            $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
            foreach ($languages as $l) {
                Database::query(
                    "INSERT INTO category_translations (category_id,lang,name) VALUES (?,?,?)",
                    [$id, $l['code'], $name]
                );
            }
            Cache::delete('categories_tree_tr');
            Cache::delete('categories_flat_tr');
            Logger::activity('category_quick_created', 'Category', $id);
            echo json_encode(['success'=>true,'id'=>$id,'name'=>$name]);
        } catch (\Throwable $e) {
            Logger::error('Kategori hızlı ekle hatası: ' . $e->getMessage());
            echo json_encode(['success'=>false,'message'=>'Sunucu hatası: ' . $e->getMessage()]);
        }
        exit;
    }
}
