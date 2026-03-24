<?php
namespace App\Controllers\Admin;
use App\Core\Auth; use App\Core\Request; use App\Core\Response;
use App\Core\Session; use App\Core\Database; use App\Models\Setting;

class NewsletterController {
    public function index(): void {
        $subscribers = Database::rows("SELECT * FROM newsletters ORDER BY subscribed_at DESC");
        Response::view('Admin.newsletter.index', [
            'title'=>'Bülten Aboneleri','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),'subscribers'=>$subscribers,
        ]);
    }

    public function send(): void {
        $subject = trim(Request::post('subject', ''));
        $body    = Request::raw('body') ?? '';

        if (!$subject || !$body) {
            Session::flash('error', 'Konu ve içerik zorunludur.');
            Response::redirect(adminUrl('bulten'));
        }

        $subscribers = Database::rows("SELECT email, name FROM newsletters WHERE is_active=1");
        $sent = 0; $failed = 0;

        foreach ($subscribers as $sub) {
            try {
                $personalBody = str_replace(['{{name}}','{{email}}'], [$sub['name'] ?? 'Değerli Müşteri', $sub['email']], $body);
                $headers  = "MIME-Version: 1.0\r\nContent-type: text/html; charset=utf-8\r\n";
                $headers .= "From: " . Setting::get('site_name') . " <" . Setting::get('smtp_from') . ">\r\n";
                mail($sub['email'], $subject, $personalBody, $headers);
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
            }
        }

        Session::flash('success', "$sent abone'ye mail gönderildi." . ($failed > 0 ? " $failed başarısız." : ''));
        Response::redirect(adminUrl('bulten'));
    }

    public function export(): void {
        $rows = Database::rows("SELECT email,name,subscribed_at FROM newsletters WHERE is_active=1");
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="bulten-aboneleri.csv"');
        $out = fopen('php://output','w');
        fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($out, ['E-posta','Ad','Abone Tarihi']);
        foreach ($rows as $r) fputcsv($out, [$r['email'],$r['name'],$r['subscribed_at']]);
        fclose($out); exit;
    }

    public function destroy(array $params): void {
        Database::query("DELETE FROM newsletters WHERE id=?", [(int)$params['id']]);
        Session::flash('success','Abone silindi.');
        Response::redirect(adminUrl('bulten'));
    }
}
