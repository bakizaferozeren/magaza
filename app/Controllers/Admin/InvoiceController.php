<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class InvoiceController
{
    public function index(): void
    {
        $page    = max(1, (int) Request::get('page', 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;
        $total   = (int) Database::value("SELECT COUNT(*) FROM invoices");

        $invoices = Database::rows(
            "SELECT i.*, o.order_no,
                    COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name
             FROM invoices i
             JOIN orders o ON o.id = i.order_id
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY i.id DESC LIMIT ? OFFSET ?",
            [$perPage, $offset]
        );

        Response::view('Admin.invoices.index', [
            'title'      => 'Faturalar',
            'siteName'   => Setting::get('site_name'),
            'siteLogo'   => Setting::get('site_logo'),
            'user'       => Auth::user(),
            'invoices'   => $invoices,
            'pagination' => ['total'=>$total,'per_page'=>$perPage,'current_page'=>$page,
                'last_page'=>(int)ceil($total/$perPage),'from'=>$total>0?$offset+1:0,'to'=>min($offset+$perPage,$total)],
        ]);
    }

    public function upload(array $params): void
    {
        $orderId = (int) $params['order_id'];
        $type    = Request::post('type', 'e_invoice');

        if (empty($_FILES['invoice']['name'])) {
            Session::flash('error', 'Dosya seçilmedi.'); Response::back();
        }

        $ext = strtolower(pathinfo($_FILES['invoice']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf','xml'])) {
            Session::flash('error', 'Sadece PDF ve XML yüklenebilir.'); Response::back();
        }

        $dir = PUB_PATH.'/uploads/invoices/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $file = 'inv_'.$orderId.'_'.time().'.'.$ext;
        move_uploaded_file($_FILES['invoice']['tmp_name'], $dir.$file);

        $invoiceNo = Request::post('invoice_no',
            'INV-'.date('Y').'-'.str_pad($orderId, 6, '0', STR_PAD_LEFT)
        );

        Database::query(
            "INSERT INTO invoices (order_id, type, invoice_no, path, issued_at, created_at)
             VALUES (?, ?, ?, ?, NOW(), NOW())",
            [$orderId, $type, $invoiceNo, $file]
        );

        Logger::activity('invoice_uploaded', 'Invoice', $orderId);
        Session::flash('success', 'Fatura yüklendi.');
        Response::redirect(adminUrl('siparisler/'.$orderId));
    }

    public function download(array $params): void
    {
        $id      = (int) $params['id'];
        $invoice = Database::row("SELECT * FROM invoices WHERE id = ?", [$id]);
        if (!$invoice) Response::abort(404);

        $path = PUB_PATH.'/uploads/invoices/'.$invoice['path'];
        if (!file_exists($path)) {
            Session::flash('error', 'Dosya bulunamadı.'); Response::back();
        }

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="fatura-'.$invoice['invoice_no'].'.pdf"');
        readfile($path);
        exit;
    }

    public function destroy(array $params): void
    {
        $id      = (int) $params['id'];
        $invoice = Database::row("SELECT * FROM invoices WHERE id = ?", [$id]);
        if ($invoice) {
            $path = PUB_PATH.'/uploads/invoices/'.$invoice['path'];
            if (file_exists($path)) unlink($path);
            Database::query("DELETE FROM invoices WHERE id = ?", [$id]);
        }
        Session::flash('success', 'Fatura silindi.');
        Response::back();
    }
}
