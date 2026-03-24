<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class PopupController
{
    public function index(): void
    {
        $lang   = Session::get('admin_lang', 'tr');
        $popups = Database::rows(
            "SELECT p.*, pt.title, pt.content
             FROM popups p
             LEFT JOIN popup_translations pt ON pt.popup_id=p.id AND pt.lang=?
             ORDER BY p.id DESC",
            [$lang]
        );

        Response::view('Admin.popups.index', [
            'title'    => 'Popup Yönetimi',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'popups'   => $popups,
        ]);
    }

    public function store(): void
    {
        $data = Request::all();

        $image = null;
        if (!empty($_FILES['image']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $dir   = PUB_PATH . '/uploads/popups/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = uniqid('popup_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fname)) {
                $image = $fname;
            }
        }

        Database::query(
            "INSERT INTO popups (image, link, delay, show_once, is_active, starts_at, ends_at, created_at)
             VALUES (?,?,?,?,?,?,?,NOW())",
            [
                $image,
                $data['link'] ?? null,
                (int)($data['delay'] ?? 3),
                !empty($data['show_once']) ? 1 : 0,
                !empty($data['is_active']) ? 1 : 0,
                !empty($data['starts_at']) ? $data['starts_at'] : null,
                !empty($data['ends_at'])   ? $data['ends_at']   : null,
            ]
        );
        $popupId = Database::lastId();

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['title_' . $code]) && empty($data['content_' . $code])) continue;
            Database::query(
                "INSERT INTO popup_translations (popup_id, lang, title, content) VALUES (?,?,?,?)",
                [$popupId, $code, $data['title_' . $code] ?? null, $data['content_' . $code] ?? null]
            );
        }

        Session::flash('success', 'Popup eklendi.');
        Response::redirect(adminUrl('popuplar'));
    }

    public function update(array $params): void
    {
        $id    = (int) $params['id'];
        $popup = Database::row("SELECT * FROM popups WHERE id=?", [$id]);
        if (!$popup) Response::abort(404);

        $data  = Request::all();
        $image = $popup['image'];

        if (!empty($_FILES['image']['name'])) {
            $ext   = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $dir   = PUB_PATH . '/uploads/popups/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $fname = uniqid('popup_') . '.' . $ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $fname)) {
                $image = $fname;
            }
        }

        Database::query(
            "UPDATE popups SET image=?, link=?, delay=?, show_once=?, is_active=?, starts_at=?, ends_at=? WHERE id=?",
            [
                $image, $data['link'] ?? null, (int)($data['delay'] ?? 3),
                !empty($data['show_once'])?1:0, !empty($data['is_active'])?1:0,
                !empty($data['starts_at']) ? $data['starts_at'] : null,
                !empty($data['ends_at'])   ? $data['ends_at']   : null,
                $id,
            ]
        );

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        foreach ($languages as $l) {
            $code = $l['code'];
            Database::query(
                "INSERT INTO popup_translations (popup_id, lang, title, content) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE title=VALUES(title), content=VALUES(content)",
                [$id, $code, $data['title_' . $code] ?? null, $data['content_' . $code] ?? null]
            );
        }

        Session::flash('success', 'Popup güncellendi.');
        Response::redirect(adminUrl('popuplar'));
    }

    public function destroy(array $params): void
    {
        $id    = (int) $params['id'];
        $popup = Database::row("SELECT image FROM popups WHERE id=?", [$id]);
        if ($popup && $popup['image']) {
            $p = PUB_PATH . '/uploads/popups/' . $popup['image'];
            if (file_exists($p)) unlink($p);
        }
        Database::query("DELETE FROM popups WHERE id=?", [$id]);
        Session::flash('success', 'Popup silindi.');
        Response::redirect(adminUrl('popuplar'));
    }
}
