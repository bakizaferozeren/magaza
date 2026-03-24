<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class BlogController
{
    public function index(): void
    {
        $lang  = Session::get('admin_lang', 'tr');
        $posts = Database::rows(
            "SELECT b.*, bt.title, bt.excerpt,
                    u.name as author_name
             FROM blogs b
             LEFT JOIN blog_translations bt ON bt.blog_id = b.id AND bt.lang = ?
             LEFT JOIN users u ON u.id = b.author_id
             ORDER BY b.id DESC",
            [$lang]
        );

        Response::view('Admin.blog.index', [
            'title'    => 'Blog / Haberler',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'posts'    => $posts,
        ]);
    }

    public function create(): void
    {
        $languages = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");

        Response::view('Admin.blog.form', [
            'title'        => 'Yeni Yazı',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'post'         => null,
            'translations' => [],
            'languages'    => $languages,
        ]);
    }

    public function store(): void
    {
        $data      = Request::all();
        $languages = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        $hasTitle = false;
        foreach ($languages as $l) {
            if (!empty($data['title_'.$l['code']])) { $hasTitle = true; break; }
        }

        if (!$hasTitle) {
            Session::flash('error', 'Yazı başlığı zorunludur.');
            Response::back();
        }

        Database::beginTransaction();
        try {
            $slug = $this->generateSlug($data['title_tr'] ?? $data['title_en'] ?? 'yazi');

            // Kapak görseli
            $image = $this->handleImage($_FILES['image'] ?? null);

            Database::query(
                "INSERT INTO blogs (slug, image, author_id, is_active, published_at, created_at)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [
                    $slug, $image,
                    Auth::user()['id'],
                    !empty($data['is_active']) ? 1 : 0,
                    !empty($data['published_at']) ? $data['published_at'] : date('Y-m-d H:i:s'),
                ]
            );
            $postId = Database::lastId();

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['title_'.$code])) continue;
                Database::query(
                    "INSERT INTO blog_translations (blog_id, lang, title, excerpt, content, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?, ?)",
                    [
                        $postId, $code,
                        $data['title_'.$code],
                        $data['excerpt_'.$code] ?? null,
                        Request::raw('content_'.$code) ?? null,
                        $data['meta_title_'.$code] ?? null,
                        $data['meta_desc_'.$code] ?? null,
                    ]
                );
            }

            Database::commit();
            Logger::activity('blog_created', 'Blog', $postId);
            Session::flash('success', 'Yazı oluşturuldu.');
            Response::redirect(adminUrl('blog'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Blog yazısı oluşturulurken hata: '.$e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function edit(array $params): void
    {
        $id        = (int) $params['id'];
        $post      = Database::row("SELECT * FROM blogs WHERE id = ?", [$id]);
        if (!$post) Response::abort(404);

        $languages    = Database::rows("SELECT * FROM languages WHERE is_active = 1 ORDER BY sort_order");
        $translations = [];
        foreach ($languages as $l) {
            $translations[$l['code']] = Database::row(
                "SELECT * FROM blog_translations WHERE blog_id = ? AND lang = ?",
                [$id, $l['code']]
            ) ?? [];
        }

        Response::view('Admin.blog.form', [
            'title'        => 'Yazıyı Düzenle',
            'siteName'     => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'),
            'user'         => Auth::user(),
            'post'         => $post,
            'translations' => $translations,
            'languages'    => $languages,
        ]);
    }

    public function update(array $params): void
    {
        $id        = (int) $params['id'];
        $data      = Request::all();
        $post      = Database::row("SELECT * FROM blogs WHERE id = ?", [$id]);
        if (!$post) Response::abort(404);

        $languages = Database::rows("SELECT code FROM languages WHERE is_active = 1");

        Database::beginTransaction();
        try {
            $image = $post['image'];
            $newImage = $this->handleImage($_FILES['image'] ?? null);
            if ($newImage) $image = $newImage;

            Database::query(
                "UPDATE blogs SET slug=?, image=?, is_active=?, published_at=?, updated_at=NOW() WHERE id=?",
                [
                    $data['slug'] ?? $post['slug'],
                    $image,
                    !empty($data['is_active']) ? 1 : 0,
                    !empty($data['published_at']) ? $data['published_at'] : $post['published_at'],
                    $id,
                ]
            );

            foreach ($languages as $l) {
                $code = $l['code'];
                if (empty($data['title_'.$code])) continue;
                Database::query(
                    "INSERT INTO blog_translations (blog_id, lang, title, excerpt, content, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     title=VALUES(title), excerpt=VALUES(excerpt), content=VALUES(content),
                     meta_title=VALUES(meta_title), meta_desc=VALUES(meta_desc)",
                    [
                        $id, $code,
                        $data['title_'.$code],
                        $data['excerpt_'.$code] ?? null,
                        Request::raw('content_'.$code) ?? null,
                        $data['meta_title_'.$code] ?? null,
                        $data['meta_desc_'.$code] ?? null,
                    ]
                );
            }

            Database::commit();
            Logger::activity('blog_updated', 'Blog', $id);
            Session::flash('success', 'Yazı güncellendi.');
            Response::redirect(adminUrl('blog'));

        } catch (\Throwable $e) {
            Database::rollback();
            Logger::error('Blog güncellenirken hata: '.$e->getMessage());
            Session::flash('error', 'Bir hata oluştu.');
            Response::back();
        }
    }

    public function destroy(array $params): void
    {
        $id = (int) $params['id'];
        $post = Database::row("SELECT image FROM blogs WHERE id = ?", [$id]);
        if ($post && $post['image']) {
            $path = PUB_PATH.'/uploads/blog/'.$post['image'];
            if (file_exists($path)) unlink($path);
        }
        Database::query("DELETE FROM blogs WHERE id = ?", [$id]);
        Logger::activity('blog_deleted', 'Blog', $id);
        Session::flash('success', 'Yazı silindi.');
        Response::redirect(adminUrl('blog'));
    }

    private function handleImage(?array $file): ?string
    {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) return null;
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'])) return null;
        $dir = PUB_PATH.'/uploads/blog/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $filename = uniqid('blog_').'.'.$ext;
        return move_uploaded_file($file['tmp_name'], $dir.$filename) ? $filename : null;
    }

    private function generateSlug(string $text, int $exceptId = 0): string
    {
        $base = slugify($text);
        $slug = $base; $i = 1;
        while (Database::value("SELECT COUNT(*) FROM blogs WHERE slug=? AND id!=?", [$slug, $exceptId])) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
