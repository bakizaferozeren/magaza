<?php
namespace App\Controllers\Admin;
use App\Core\Auth; use App\Core\Request; use App\Core\Response;
use App\Core\Session; use App\Core\Database; use App\Models\Setting;

class LanguageController
{
    public function index(): void
    {
        $languages = Database::rows("SELECT * FROM languages ORDER BY sort_order ASC");
        Response::view('Admin.settings.languages', [
            'title'     => 'Dil Yönetimi', 'siteName' => Setting::get('site_name'),
            'siteLogo'  => Setting::get('site_logo'), 'user' => Auth::user(),
            'languages' => $languages,
        ]);
    }

    public function store(): void
    {
        $code = strtolower(trim(Request::post('code', '')));
        $name = trim(Request::post('name', ''));
        if (!$code || !$name) {
            Session::flash('error', 'Kod ve isim zorunludur.');
            Response::back();
        }
        if (Database::value("SELECT COUNT(*) FROM languages WHERE code=?", [$code])) {
            Session::flash('error', 'Bu dil kodu zaten mevcut.');
            Response::back();
        }
        Database::query(
            "INSERT INTO languages (code, name, flag, is_active, sort_order) VALUES (?,?,?,1,?)",
            [$code, $name, strtoupper($code), (int) Request::post('sort_order', 99)]
        );
        Session::flash('success', 'Dil eklendi.');
        Response::redirect(adminUrl('ayarlar/diller'));
    }

    public function update(array $params): void
    {
        $id = (int) $params['id'];
        Database::query(
            "UPDATE languages SET name=?, flag=?, is_active=?, sort_order=? WHERE id=?",
            [
                Request::post('name'), Request::post('flag'),
                !empty(Request::post('is_active')) ? 1 : 0,
                (int) Request::post('sort_order', 0), $id,
            ]
        );
        Session::flash('success', 'Dil güncellendi.');
        Response::redirect(adminUrl('ayarlar/diller'));
    }

    public function destroy(array $params): void
    {
        $id   = (int) $params['id'];
        $lang = Database::row("SELECT * FROM languages WHERE id=?", [$id]);
        if ($lang && $lang['is_default']) {
            Session::flash('error', 'Varsayılan dil silinemez.');
            Response::back();
        }
        Database::query("DELETE FROM languages WHERE id=?", [$id]);
        Session::flash('success', 'Dil silindi.');
        Response::redirect(adminUrl('ayarlar/diller'));
    }
}


class CurrencyController
{
    public function index(): void
    {
        $currencies = Database::rows("SELECT * FROM currencies ORDER BY is_default DESC, id ASC");
        Response::view('Admin.settings.currencies', [
            'title'      => 'Para Birimleri', 'siteName' => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'), 'user' => Auth::user(),
            'currencies' => $currencies,
        ]);
    }

    public function update(): void
    {
        $data = Request::all();
        $currencies = Database::rows("SELECT id, code FROM currencies");

        foreach ($currencies as $c) {
            $rate     = (float)($data['rate_' . $c['code']] ?? 1);
            $isActive = !empty($data['active_' . $c['code']]) ? 1 : 0;
            Database::query(
                "UPDATE currencies SET rate=?, is_active=? WHERE id=?",
                [$rate, $isActive, $c['id']]
            );
        }

        Setting::set('currency_last_updated', date('Y-m-d H:i:s'));
        Session::flash('success', 'Kurlar güncellendi.');
        Response::redirect(adminUrl('ayarlar/para-birimleri'));
    }

    public function syncTCMB(): void
    {
        try {
            $xml = @simplexml_load_file(Setting::get('tcmb_xml_url', 'https://www.tcmb.gov.tr/kurlar/today.xml'));
            if (!$xml) throw new \Exception('TCMB XML alınamadı.');

            $rates = [];
            foreach ($xml->Currency as $c) {
                $code = (string)$c['CurrencyCode'];
                $rate = (float)str_replace(',', '.', (string)$c->ForexSelling);
                if ($rate > 0) $rates[$code] = $rate;
            }

            foreach ($rates as $code => $rate) {
                // TRY bazlı: 1 USD = X TRY → 1 TRY = 1/X USD
                // Burada biz "1 TRY kaç yabancı para" saklıyoruz
                $tryRate = $rate > 0 ? round(1 / $rate, 6) : 0;
                Database::query(
                    "UPDATE currencies SET rate=?, updated_at=NOW() WHERE code=?",
                    [$tryRate, $code]
                );
            }

            Setting::set('currency_last_updated', date('Y-m-d H:i:s'));
            Session::flash('success', 'TCMB kurları güncellendi.');
        } catch (\Throwable $e) {
            Session::flash('error', 'TCMB bağlantısı başarısız: ' . $e->getMessage());
        }

        Response::redirect(adminUrl('ayarlar/para-birimleri'));
    }
}


class MailTemplateController
{
    public function index(): void
    {
        $lang      = Session::get('admin_lang', 'tr');
        $templates = Database::rows(
            "SELECT mt.*, mtt.subject
             FROM mail_templates mt
             LEFT JOIN mail_template_translations mtt ON mtt.template_id=mt.id AND mtt.lang=?
             ORDER BY mt.id ASC",
            [$lang]
        );

        Response::view('Admin.settings.mail_templates', [
            'title'     => 'Mail Şablonları', 'siteName' => Setting::get('site_name'),
            'siteLogo'  => Setting::get('site_logo'), 'user' => Auth::user(),
            'templates' => $templates,
        ]);
    }

    public function edit(array $params): void
    {
        $id        = (int) $params['id'];
        $template  = Database::row("SELECT * FROM mail_templates WHERE id=?", [$id]);
        if (!$template) Response::abort(404);

        $languages    = Database::rows("SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order");
        $translations = [];
        foreach ($languages as $l) {
            $translations[$l['code']] = Database::row(
                "SELECT * FROM mail_template_translations WHERE template_id=? AND lang=?",
                [$id, $l['code']]
            ) ?? [];
        }

        Response::view('Admin.settings.mail_template_edit', [
            'title'        => 'Mail Şablonu Düzenle', 'siteName' => Setting::get('site_name'),
            'siteLogo'     => Setting::get('site_logo'), 'user' => Auth::user(),
            'template'     => $template, 'translations' => $translations, 'languages' => $languages,
        ]);
    }

    public function update(array $params): void
    {
        $id        = (int) $params['id'];
        $data      = Request::all();
        $languages = Database::rows("SELECT code FROM languages WHERE is_active=1");

        Database::query("UPDATE mail_templates SET is_active=? WHERE id=?", [!empty($data['is_active'])?1:0, $id]);

        foreach ($languages as $l) {
            $code = $l['code'];
            if (empty($data['subject_' . $code])) continue;
            Database::query(
                "INSERT INTO mail_template_translations (template_id, lang, subject, body) VALUES (?,?,?,?)
                 ON DUPLICATE KEY UPDATE subject=VALUES(subject), body=VALUES(body)",
                [$id, $code, $data['subject_' . $code], Request::raw('body_' . $code) ?? '']
            );
        }

        Session::flash('success', 'Mail şablonu güncellendi.');
        Response::redirect(adminUrl('ayarlar/mail-sablonlari'));
    }
}
