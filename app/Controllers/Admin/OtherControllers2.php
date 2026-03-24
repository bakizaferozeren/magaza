<?php
namespace App\Controllers\Admin;
use App\Core\Auth; use App\Core\Request; use App\Core\Response;
use App\Core\Session; use App\Core\Database; use App\Core\Cache;
use App\Models\Setting;

class WidgetController
{
    public function index(): void
    {
        $widgets = Database::rows("SELECT * FROM widgets ORDER BY location, sort_order ASC");
        Response::view('Admin.widgets.index', [
            'title'  => 'Widget Alanları', 'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'), 'user' => Auth::user(),
            'widgets' => $widgets,
        ]);
    }

    public function save(): void
    {
        $data = Request::json();
        $widgets = $data['widgets'] ?? [];

        foreach ($widgets as $w) {
            $id = (int)($w['id'] ?? 0);
            if ($id) {
                Database::query(
                    "UPDATE widgets SET data=?, is_active=?, sort_order=? WHERE id=?",
                    [json_encode($w['data'] ?? []), $w['is_active'] ? 1 : 0, $w['sort_order'] ?? 0, $id]
                );
            } else {
                Database::query(
                    "INSERT INTO widgets (location, type, data, sort_order, is_active) VALUES (?,?,?,?,?)",
                    [$w['location'], $w['type'], json_encode($w['data'] ?? []), $w['sort_order'] ?? 0, $w['is_active'] ? 1 : 0]
                );
            }
        }

        Cache::delete('widgets_all');
        Response::success(null, 'Widget\'lar kaydedildi.');
    }
}


class RedirectController
{
    public function index(): void
    {
        $redirects = Database::rows("SELECT * FROM redirects ORDER BY id DESC LIMIT 200");
        Response::view('Admin.redirects.index', [
            'title'     => '301 Yönlendirmeler', 'siteName' => Setting::get('site_name'),
            'siteLogo'  => Setting::get('site_logo'), 'user' => Auth::user(),
            'redirects' => $redirects,
        ]);
    }

    public function store(): void
    {
        $from = trim(Request::post('from_url', ''));
        $to   = trim(Request::post('to_url', ''));

        if (!$from || !$to) {
            Session::flash('error', 'Kaynak ve hedef URL zorunludur.');
            Response::back();
        }

        // Çift kayıt önle
        Database::query(
            "INSERT INTO redirects (from_url, to_url, type, is_active, created_at)
             VALUES (?,?,?,1,NOW())
             ON DUPLICATE KEY UPDATE to_url=VALUES(to_url)",
            [$from, $to, (int) Request::post('type', 301)]
        );

        Session::flash('success', 'Yönlendirme eklendi.');
        Response::redirect(adminUrl('yonlendirmeler'));
    }

    public function destroy(array $params): void
    {
        Database::query("DELETE FROM redirects WHERE id=?", [(int)$params['id']]);
        Session::flash('success', 'Yönlendirme silindi.');
        Response::redirect(adminUrl('yonlendirmeler'));
    }
}


class CacheController
{
    public function index(): void
    {
        $cacheDir  = STR_PATH . '/cache/';
        $cacheSize = 0;
        $fileCount = 0;
        if (is_dir($cacheDir)) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($cacheDir)) as $f) {
                if ($f->isFile()) { $cacheSize += $f->getSize(); $fileCount++; }
            }
        }

        Response::view('Admin.cache.index', [
            'title'     => 'Cache Yönetimi', 'siteName' => Setting::get('site_name'),
            'siteLogo'  => Setting::get('site_logo'), 'user' => Auth::user(),
            'cacheSize' => $cacheSize, 'fileCount' => $fileCount,
        ]);
    }

    public function flush(): void
    {
        $cacheDir = STR_PATH . '/cache/';
        if (is_dir($cacheDir)) {
            foreach (new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($cacheDir, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::CHILD_FIRST
            ) as $f) {
                $f->isDir() ? rmdir($f->getPathname()) : unlink($f->getPathname());
            }
        }
        Session::flash('success', 'Cache temizlendi.');
        Response::redirect(adminUrl('ayarlar/cache'));
    }
}


class LogController
{
    public function index(): void
    {
        $logDir = STR_PATH . '/logs/';
        $files  = [];
        if (is_dir($logDir)) {
            foreach (glob($logDir . '*/*.log') as $f) {
                $files[] = ['name' => basename(dirname($f)) . '/' . basename($f), 'size' => filesize($f), 'mtime' => filemtime($f)];
            }
            usort($files, fn($a,$b) => $b['mtime'] - $a['mtime']);
        }

        $dbErrors = Database::rows(
            "SELECT * FROM error_logs ORDER BY id DESC LIMIT 50"
        );

        Response::view('Admin.logs.index', [
            'title'    => 'Hata Logları', 'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'), 'user' => Auth::user(),
            'files'    => $files, 'dbErrors' => $dbErrors,
        ]);
    }

    public function show(array $params): void
    {
        $file    = preg_replace('/[^a-zA-Z0-9_\-\/\.]/', '', $params['file']);
        $path    = STR_PATH . '/logs/' . $file;
        $content = file_exists($path) ? file_get_contents($path) : 'Dosya bulunamadı.';

        Response::view('Admin.logs.show', [
            'title'   => 'Log: ' . $file, 'siteName' => Setting::get('site_name'),
            'siteLogo'=> Setting::get('site_logo'), 'user' => Auth::user(),
            'file'    => $file, 'content' => $content,
        ]);
    }

    public function flush(): void
    {
        Database::query("DELETE FROM error_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        Session::flash('success', 'Eski loglar temizlendi (30 günden eski).');
        Response::redirect(adminUrl('ayarlar/loglar'));
    }

    public function activity(): void
    {
        $page    = max(1, (int) Request::get('page', 1));
        $perPage = 30;
        $offset  = ($page - 1) * $perPage;
        $total   = (int) Database::value("SELECT COUNT(*) FROM activity_logs");
        $logs    = Database::rows(
            "SELECT al.*, CONCAT(u.name,' ',u.surname) as admin_name
             FROM activity_logs al
             LEFT JOIN users u ON u.id = al.user_id
             ORDER BY al.id DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        Response::view('Admin.logs.activity', [
            'title'    => 'Aktivite Logları', 'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'), 'user' => Auth::user(),
            'logs'     => $logs,
            'pagination' => ['total'=>$total,'current_page'=>$page,'last_page'=>(int)ceil($total/$perPage)],
        ]);
    }
}
