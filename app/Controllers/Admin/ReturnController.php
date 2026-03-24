<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class ReturnController
{
    public function index(): void
    {
        $page    = max(1, (int) Request::get('page', 1));
        $status  = Request::get('status', '');
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $where  = ['1=1'];
        $params = [];
        if ($status) { $where[] = 'r.status = ?'; $params[] = $status; }
        $whereStr = implode(' AND ', $where);

        $total   = (int) Database::value("SELECT COUNT(*) FROM returns r WHERE $whereStr", $params);
        $returns = Database::rows(
            "SELECT r.*,
                    o.order_no,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name
             FROM returns r
             JOIN orders o ON o.id = r.order_id
             LEFT JOIN users u ON u.id = r.user_id
             WHERE $whereStr
             ORDER BY r.id DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        Response::view('Admin.returns.index', [
            'title'      => 'İadeler',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'returns'    => $returns,
            'status'     => $status,
            'pagination' => ['total'=>$total,'current_page'=>$page,'last_page'=>(int)ceil($total/$perPage),'from'=>$total>0?$offset+1:0,'to'=>min($offset+$perPage,$total)],
        ]);
    }

    public function show(array $params): void
    {
        $id     = (int) $params['id'];
        $return = Database::row(
            "SELECT r.*, o.order_no, o.total,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name,
                    COALESCE(u.email, o.guest_email) as customer_email
             FROM returns r
             JOIN orders o ON o.id = r.order_id
             LEFT JOIN users u ON u.id = r.user_id
             WHERE r.id = ?", [$id]
        );
        if (!$return) Response::abort(404);

        $orderItems = Database::rows(
            "SELECT oi.*, pt.name as product_name
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE oi.order_id = ?", [$return['order_id']]
        );

        Response::view('Admin.returns.show', [
            'title'      => 'İade #'.$id,
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'return'     => $return,
            'orderItems' => $orderItems,
        ]);
    }

    public function updateStatus(array $params): void
    {
        $id = (int) $params['id'];
        Database::query(
            "UPDATE returns SET status=?, admin_note=?, updated_at=NOW() WHERE id=?",
            [Request::post('status'), Request::post('admin_note',''), $id]
        );
        Logger::activity('return_updated', 'Return', $id);
        Session::flash('success', 'İade güncellendi.');
        Response::redirect(adminUrl('iadeler/'.$id));
    }

    public function approve(array $params): void
    {
        $id = (int) $params['id'];
        Database::query(
            "UPDATE returns SET status='approved', updated_at=NOW() WHERE id=?", [$id]
        );
        Session::flash('success', 'İade onaylandı.');
        Response::redirect(adminUrl('iadeler/' . $id));
    }

    public function reject(array $params): void
    {
        $id = (int) $params['id'];
        Database::query(
            "UPDATE returns SET status='rejected', admin_note=?, updated_at=NOW() WHERE id=?",
            [Request::post('admin_note', ''), $id]
        );
        Session::flash('success', 'İade reddedildi.');
        Response::redirect(adminUrl('iadeler/' . $id));
    }
}
