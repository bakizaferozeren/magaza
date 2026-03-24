<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class SliderController
{
    public function index(): void
    {
        $lang    = Session::get('admin_lang', 'tr');
        $sliders = Database::rows(
            "SELECT s.*, st.title, st.subtitle, st.btn_text
             FROM sliders s
             LEFT JOIN slider_translations st ON st.slider_id = s.id AND st.lang = ?
             ORDER BY s.sort_order ASC",
            [$lang]
        );
        Response::view('Admin.sliders.index', [
            'title'=>'Slider & Banner','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),'sliders'=>$sliders,
        ]);
    }

    // Route var ama index sayfasında inline form — yönlendir
    public function create(): void { Response::redirect(adminUrl('sliderlar')); }

    public function store(): void
    {
        $data = Request::all();
        if (empty($_FILES['image']['name'])) {
            Session::flash('error', 'Görsel zorunludur.');
            Response::back();
        }
        $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $dir  = PUB_PATH.'/uploads/sliders/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file = uniqid('slider_').'.'.$ext;
        move_uploaded_file($_FILES['image']['tmp_name'], $dir.$file);

        Database::query(
            "INSERT INTO sliders (image, link, sort_order, is_active, created_at) VALUES (?,?,?,?,NOW())",
            [$file, $data['link']??null, (int)($data['sort_order']??0), !empty($data['is_active'])?1:0]
        );
        $id = Database::lastId();

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        foreach ($languages as $l) {
            $code = $l['code'];
            Database::query(
                "INSERT INTO slider_translations (slider_id,lang,title,subtitle,btn_text) VALUES (?,?,?,?,?)",
                [$id,$code,$data['title_'.$code]??null,$data['subtitle_'.$code]??null,$data['btn_text_'.$code]??null]
            );
        }
        Session::flash('success', 'Slider eklendi.');
        Response::redirect(adminUrl('sliderlar'));
    }

    // Route var ama inline düzenleme — yönlendir
    public function edit(array $params): void { Response::redirect(adminUrl('sliderlar')); }

    public function update(array $params): void
    {
        $id  = (int) $params['id'];
        $row = Database::row("SELECT * FROM sliders WHERE id=?", [$id]);
        if (!$row) Response::abort(404);

        $image = $row['image'];
        if (!empty($_FILES['image']['name'])) {
            $ext  = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $dir  = PUB_PATH.'/uploads/sliders/';
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $file = uniqid('slider_').'.'.$ext;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir.$file)) $image = $file;
        }

        $data = Request::all();
        Database::query(
            "UPDATE sliders SET image=?,link=?,sort_order=?,is_active=? WHERE id=?",
            [$image, $data['link']??null, (int)($data['sort_order']??0), !empty($data['is_active'])?1:0, $id]
        );

        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        foreach ($languages as $l) {
            $code = $l['code'];
            Database::query(
                "INSERT INTO slider_translations (slider_id,lang,title,subtitle,btn_text) VALUES (?,?,?,?,?)
                 ON DUPLICATE KEY UPDATE title=VALUES(title),subtitle=VALUES(subtitle),btn_text=VALUES(btn_text)",
                [$id,$code,$data['title_'.$code]??null,$data['subtitle_'.$code]??null,$data['btn_text_'.$code]??null]
            );
        }
        Session::flash('success','Slider güncellendi.');
        Response::redirect(adminUrl('sliderlar'));
    }

    public function destroy(array $params): void
    {
        $id  = (int) $params['id'];
        $row = Database::row("SELECT image FROM sliders WHERE id=?", [$id]);
        if ($row && $row['image']) {
            $p = PUB_PATH.'/uploads/sliders/'.$row['image'];
            if (file_exists($p)) unlink($p);
        }
        Database::query("DELETE FROM sliders WHERE id=?", [$id]);
        Session::flash('success','Slider silindi.');
        Response::redirect(adminUrl('sliderlar'));
    }

    public function sort(): void
    {
        $order = Request::json()['order'] ?? [];
        foreach ($order as $i => $id) {
            Database::query("UPDATE sliders SET sort_order=? WHERE id=?", [$i, $id]);
        }
        Response::success(null,'Sıralama güncellendi.');
    }
}
