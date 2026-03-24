<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class AttributeController
{
    public function index(): void
    {
        $types = Database::rows(
            "SELECT vt.*, COUNT(vo.id) as option_count
             FROM variation_types vt
             LEFT JOIN variation_options vo ON vo.variation_type_id = vt.id
             GROUP BY vt.id
             ORDER BY vt.sort_order ASC"
        );

        Response::view('Admin.attributes.index', [
            'title'    => 'Varyasyon Nitelikleri',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'types'    => $types,
        ]);
    }

    public function store(): void
    {
        $name = trim(Request::post('name', ''));
        if (!$name) {
            Session::flash('error', 'Nitelik adı zorunludur.');
            Response::back();
        }

        $slug = slugify($name);
        // Benzersiz slug
        $i = 1;
        $base = $slug;
        while (Database::value("SELECT COUNT(*) FROM variation_types WHERE slug=?", [$slug])) {
            $slug = $base . '-' . $i++;
        }

        Database::query(
            "INSERT INTO variation_types (name, slug, sort_order) VALUES (?, ?, ?)",
            [$name, $slug, (int) Request::post('sort_order', 0)]
        );

        Session::flash('success', 'Nitelik eklendi.');
        Response::redirect(adminUrl('nitelikler'));
    }

    public function update(array $params): void
    {
        $id   = (int) $params['id'];
        $name = trim(Request::post('name', ''));
        if (!$name) {
            Session::flash('error', 'Nitelik adı zorunludur.');
            Response::back();
        }

        Database::query(
            "UPDATE variation_types SET name=?, sort_order=? WHERE id=?",
            [$name, (int) Request::post('sort_order', 0), $id]
        );

        Session::flash('success', 'Nitelik güncellendi.');
        Response::redirect(adminUrl('nitelikler'));
    }

    public function destroy(array $params): void
    {
        $id = (int) $params['id'];
        $used = Database::value(
            "SELECT COUNT(*) FROM product_variation_options WHERE variation_type_id=?", [$id]
        );
        if ($used > 0) {
            Session::flash('error', 'Bu nitelik ürünlerde kullanılıyor, silinemez.');
            Response::redirect(adminUrl('nitelikler'));
        }
        Database::query("DELETE FROM variation_types WHERE id=?", [$id]);
        Session::flash('success', 'Nitelik silindi.');
        Response::redirect(adminUrl('nitelikler'));
    }

    // Seçenek ekle (AJAX + form)
    public function storeOption(array $params): void
    {
        $typeId = (int) $params['id'];
        $name   = trim(Request::post('name', ''));
        $value  = trim(Request::post('value', ''));

        if (!$name) {
            Session::flash('error', 'Seçenek adı zorunludur.');
            Response::back();
        }

        Database::query(
            "INSERT INTO variation_options (variation_type_id, name, value, sort_order) VALUES (?, ?, ?, ?)",
            [$typeId, $name, $value ?: null, (int) Request::post('sort_order', 0)]
        );

        Session::flash('success', 'Seçenek eklendi.');
        Response::redirect(adminUrl('nitelikler'));
    }

    public function destroyOption(array $params): void
    {
        $id   = (int) $params['id'];
        $used = Database::value(
            "SELECT COUNT(*) FROM product_variation_options WHERE variation_option_id=?", [$id]
        );
        if ($used > 0) {
            Session::flash('error', 'Bu seçenek ürünlerde kullanılıyor, silinemez.');
            Response::back();
        }
        Database::query("DELETE FROM variation_options WHERE id=?", [$id]);
        Session::flash('success', 'Seçenek silindi.');
        Response::back();
    }

    // Ürün formundan AJAX hızlı nitelik tipi ekleme
    public function storeAjax(): void
    {
        header('Content-Type: application/json');
        $name = trim(Request::post('name', ''));
        if (!$name) {
            echo json_encode(['success'=>false,'message'=>'Ad boş olamaz.']);
            exit;
        }
        $slug = slugify($name);
        $i = 1; $base = $slug;
        while (Database::value("SELECT COUNT(*) FROM variation_types WHERE slug=?", [$slug])) {
            $slug = $base . '-' . $i++;
        }
        Database::query(
            "INSERT INTO variation_types (name, slug, sort_order) VALUES (?,?,?)",
            [$name, $slug, 99]
        );
        $id = Database::lastId();
        echo json_encode(['success'=>true,'id'=>$id,'name'=>$name,'slug'=>$slug]);
        exit;
    }
}
