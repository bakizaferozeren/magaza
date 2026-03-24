<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class ReviewController
{
    public function index(): void
    {
        $status = Request::get('status', 'pending');
        $search = Request::get('search', '');

        $where  = ['1=1'];
        $params = [];

        if ($status !== 'all') {
            $where[]  = 'r.is_approved = ?';
            $params[] = $status === 'approved' ? 1 : 0;
        }
        if ($search) {
            $where[]  = "(r.author_name LIKE ? OR r.comment LIKE ? OR pt.name LIKE ?)";
            $s        = "%$search%";
            $params   = array_merge($params, [$s, $s, $s]);
        }

        $whereStr = implode(' AND ', $where);

        $reviews = Database::rows(
            "SELECT r.*,
                    pt.name as product_name,
                    p.slug as product_slug
             FROM reviews r
             LEFT JOIN products p ON p.id = r.product_id
             LEFT JOIN product_translations pt ON pt.product_id = p.id AND pt.lang = 'tr'
             WHERE $whereStr
             ORDER BY r.created_at DESC
             LIMIT 50",
            $params
        );

        $counts = Database::row(
            "SELECT
                COUNT(*) as total,
                SUM(CASE WHEN is_approved=0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN is_approved=1 THEN 1 ELSE 0 END) as approved
             FROM reviews"
        );

        Response::view('Admin.reviews.index', [
            'title'   => 'Değerlendirmeler',
            'siteName'=> Setting::get('site_name'),
            'siteLogo'=> Setting::get('site_logo'),
            'user'    => Auth::user(),
            'reviews' => $reviews,
            'counts'  => $counts,
            'status'  => $status,
            'search'  => $search,
        ]);
    }

    public function approve(array $params): void
    {
        Database::query("UPDATE reviews SET is_approved=1 WHERE id=?", [(int)$params['id']]);
        Session::flash('success', 'Değerlendirme onaylandı.');
        Response::back();
    }

    public function reject(array $params): void
    {
        Database::query("UPDATE reviews SET is_approved=0 WHERE id=?", [(int)$params['id']]);
        Session::flash('success', 'Değerlendirme reddedildi.');
        Response::back();
    }

    public function destroy(array $params): void
    {
        Database::query("DELETE FROM reviews WHERE id=?", [(int)$params['id']]);
        Session::flash('success', 'Değerlendirme silindi.');
        Response::back();
    }

    public function store(): void
    {
        // Manuel değerlendirme ekleme (admin tarafından)
        $data = Request::all();

        if (empty($data['product_id']) || empty($data['comment'])) {
            Session::flash('error', 'Ürün ve yorum zorunludur.');
            Response::back();
        }

        Database::query(
            "INSERT INTO reviews (product_id, author_name, rating, comment, is_verified, is_manual, is_approved, created_at)
             VALUES (?, ?, ?, ?, 0, 1, 1, NOW())",
            [
                (int)$data['product_id'],
                $data['author_name'] ?? 'Admin',
                (int)($data['rating'] ?? 5),
                $data['comment'],
            ]
        );

        Session::flash('success', 'Değerlendirme eklendi.');
        Response::redirect(adminUrl('degerlendirmeler'));
    }
}
