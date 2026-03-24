<?php
namespace App\Controllers\Admin;
use App\Core\Auth; use App\Core\Request; use App\Core\Response;
use App\Core\Session; use App\Core\Database; use App\Models\Setting;

class GdprController {
    public function index(): void {
        $requests = Database::rows(
            "SELECT g.*, COALESCE(CONCAT(u.name,' ',u.surname), g.guest_email) as requester
             FROM gdpr_requests g
             LEFT JOIN users u ON u.id = g.user_id
             ORDER BY g.created_at DESC"
        );
        Response::view('Admin.gdpr.index', [
            'title'=>'KVKK Talepleri','siteName'=>Setting::get('site_name'),
            'siteLogo'=>Setting::get('site_logo'),'user'=>Auth::user(),'requests'=>$requests,
        ]);
    }
    public function approve(array $params): void {
        Database::query(
            "UPDATE gdpr_requests SET status='approved', scheduled_at=DATE_ADD(NOW(), INTERVAL 30 DAY) WHERE id=?",
            [(int)$params['id']]
        );
        Session::flash('success','Talep onaylandı. 30 gün sonra işlenecek.');
        Response::redirect(adminUrl('kvkk'));
    }
    public function reject(array $params): void {
        Database::query(
            "UPDATE gdpr_requests SET status='rejected', admin_note=? WHERE id=?",
            [Request::post('note',''), (int)$params['id']]
        );
        Session::flash('success','Talep reddedildi.');
        Response::redirect(adminUrl('kvkk'));
    }
}
