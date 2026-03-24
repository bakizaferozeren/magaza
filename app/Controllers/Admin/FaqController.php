<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Models\Setting;

class FaqController
{
    public function index(): void
    {
        $lang = Session::get('admin_lang', 'tr');
        $faqs = Database::rows(
            "SELECT f.*, ft.question, ft.answer
             FROM faqs f
             LEFT JOIN faq_translations ft ON ft.faq_id = f.id AND ft.lang = ?
             ORDER BY f.sort_order ASC",
            [$lang]
        );
        Response::view('Admin.faq.index', [
            'title'=>'Sık Sorulan Sorular','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),'faqs'=>$faqs,
        ]);
    }

    public function create(): void { Response::redirect(adminUrl('sss')); }

    public function store(): void
    {
        $data = Request::all();
        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        Database::query("INSERT INTO faqs (sort_order,is_active,created_at) VALUES (?,?,NOW())",
            [(int)($data['sort_order']??0), !empty($data['is_active'])?1:0]);
        $faqId = Database::lastId();
        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['question_'.$code])) continue;
            Database::query("INSERT INTO faq_translations (faq_id,lang,question,answer) VALUES (?,?,?,?)",
                [$faqId,$code,$data['question_'.$code],$data['answer_'.$code]??'']);
        }
        Session::flash('success','SSS eklendi.');
        Response::redirect(adminUrl('sss'));
    }

    public function edit(array $params): void { Response::redirect(adminUrl('sss')); }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        $data = Request::all();
        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");
        Database::query("UPDATE faqs SET sort_order=?,is_active=? WHERE id=?",
            [(int)($data['sort_order']??0), !empty($data['is_active'])?1:0, $id]);
        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['question_'.$code])) continue;
            Database::query(
                "INSERT INTO faq_translations (faq_id,lang,question,answer) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE question=VALUES(question),answer=VALUES(answer)",
                [$id,$code,$data['question_'.$code],$data['answer_'.$code]??'']);
        }
        Session::flash('success','SSS güncellendi.');
        Response::redirect(adminUrl('sss'));
    }

    public function destroy(array $params): void
    {
        Database::query("DELETE FROM faqs WHERE id=?", [(int)$params['id']]);
        Session::flash('success','SSS silindi.');
        Response::redirect(adminUrl('sss'));
    }

    public function updateSort(): void
    {
        $order = Request::json()['order'] ?? [];
        foreach ($order as $i => $id) {
            Database::query("UPDATE faqs SET sort_order=? WHERE id=?", [$i, $id]);
        }
        Response::success(null,'Sıralama güncellendi.');
    }
}
