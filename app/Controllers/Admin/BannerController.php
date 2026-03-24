<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class BannerController
{
    public function index(): void
    {
        $lang    = Session::get('admin_lang', 'tr');
        $banners = Database::rows(
            "SELECT b.*, bt.title, bt.subtitle
             FROM banners b
             LEFT JOIN banner_translations bt ON bt.banner_id=b.id AND bt.lang=?
             ORDER BY b.id DESC",
            [$lang]
        );

        Response::view('Admin.banners.index', [
            'title'    => 'Bannerlar',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'banners'  => $banners,
        ]);
    }

    public function store(): void
    {
        if (empty($_FILES['image']['name'])) {
            Session::flash('error', 'Görsel zorunludur.');
            Response::back();
        }

        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $dir  = PUB_PATH . '/uploads/banners/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file = uniqid('banner_') . '.' . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir . $file);

        Database::query(
            "INSERT INTO banners (position, image, link, is_active, created_at) VALUES (?,?,?,?,NOW())",
            [
                Request::post('position', 'home_top'),
                $file,
                Request::post('link', null),
                !empty(Request::post('is_active')) ? 1 : 0,
            ]
        );
        $bannerId = Database::lastId();

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        $data      = Request::all();
        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['title_' . $code])) continue;
            Database::query(
                "INSERT INTO banner_translations (banner_id, lang, title, subtitle) VALUES (?,?,?,?)",
                [$bannerId, $code, $data['title_' . $code], $data['subtitle_' . $code] ?? null]
            );
        }

        Session::flash('success', 'Banner eklendi.');
        Response::redirect(adminUrl('bannerlar'));
    }

    public function update(array $params): void
    {
        $id     = (int) $params['id'];
        $banner = Database::row("SELECT * FROM banners WHERE id=?", [$id]);
        if (!$banner) Response::abort(404);

        $image = $banner['image'];
        if (!empty($_FILES['image']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $dir  = PUB_PATH . '/uploads/banners/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file = uniqid('banner_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $file)) {
                $image = $file;
            }
        }

        Database::query(
            "UPDATE banners SET position=?, image=?, link=?, is_active=? WHERE id=?",
            [Request::post('position','home_top'), $image, Request::post('link'), !empty(Request::post('is_active'))?1:0, $id]
        );

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        $data      = Request::all();
        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['title_' . $code])) continue;
            Database::query(
                "INSERT INTO banner_translations (banner_id, lang, title, subtitle) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE title=VALUES(title), subtitle=VALUES(subtitle)",
                [$id, $code, $data['title_' . $code], $data['subtitle_' . $code] ?? null]
            );
        }

        Session::flash('success', 'Banner güncellendi.');
        Response::redirect(adminUrl('bannerlar'));
    }

    public function destroy(array $params): void
    {
        $id     = (int) $params['id'];
        $banner = Database::row("SELECT image FROM banners WHERE id=?", [$id]);
        if ($banner && $banner['image']) {
            $p = PUB_PATH . '/uploads/banners/' . $banner['image'];
            if (file_exists($p)) unlink($p);
        }
        Database::query("DELETE FROM banners WHERE id=?", [$id]);
        Session::flash('success', 'Banner silindi.');
        Response::redirect(adminUrl('bannerlar'));
    }
}
