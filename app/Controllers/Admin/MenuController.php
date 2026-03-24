<?php
namespace App\Controllers\Admin;
use App\Core\Auth; use App\Core\Request; use App\Core\Response;
use App\Core\Session; use App\Core\Database; use App\Models\Setting;

class MenuController {
    public function index(): void {
        $menus = Database::rows("SELECT * FROM menus");
        Response::view('Admin.menus.index', [
            'title'=>'Menü Yönetimi','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),'menus'=>$menus,
        ]);
    }
    public function edit(array $params): void {
        $id   = (int) $params['id'];
        $menu = Database::row("SELECT * FROM menus WHERE id=?", [$id]);
        if (!$menu) Response::abort(404);
        $lang = Session::get('admin_lang','tr');
        $items = Database::rows(
            "SELECT mi.*, mit.label
             FROM menu_items mi
             LEFT JOIN menu_item_translations mit ON mit.menu_item_id=mi.id AND mit.lang=?
             WHERE mi.menu_id=? AND mi.parent_id IS NULL ORDER BY mi.sort_order ASC",
            [$lang, $id]
        );
        $categories = Database::rows(
            "SELECT c.id, ct.name FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id=c.id AND ct.lang=?
             WHERE c.is_active=1 ORDER BY ct.name ASC", [$lang]
        );
        $pages = Database::rows(
            "SELECT p.id, p.slug, pt.title FROM pages p
             LEFT JOIN page_translations pt ON pt.page_id=p.id AND pt.lang=?
             WHERE p.is_active=1", [$lang]
        );
        Response::view('Admin.menus.edit', [
            'title'=>$menu['name'].' Menüsü','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),
            'menu'=>$menu,'items'=>$items,'categories'=>$categories,'pages'=>$pages,'lang'=>$lang,
        ]);
    }
    public function save(array $params): void {
        $menuId = (int) $params['id'];
        $data   = Request::json();
        $lang   = Session::get('admin_lang','tr');
        $items  = $data['items'] ?? [];
        Database::query("DELETE FROM menu_items WHERE menu_id=?", [$menuId]);
        foreach ($items as $i => $item) {
            Database::query(
                "INSERT INTO menu_items (menu_id,parent_id,type,target_id,url,sort_order,is_active) VALUES (?,?,?,?,?,?,1)",
                [$menuId,null,$item['type']??'url',$item['target_id']??null,$item['url']??null,$i]
            );
            $itemId = Database::lastId();
            Database::query(
                "INSERT INTO menu_item_translations (menu_item_id,lang,label) VALUES (?,?,?)",
                [$itemId,$lang,$item['label']??'']
            );
        }
        Response::success(null,'Menü kaydedildi.');
    }
}
