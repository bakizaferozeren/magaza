<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <a href="<?= adminUrl('musteriler') ?>" class="btn btn-outline btn-sm">← Müşteriler</a>
  <span style="font-size:15px;font-weight:700">Misafir Siparişleri</span>
</div>
<div class="card" style="padding:0">
  <?php if (empty($orders)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Misafir siparişi yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sipariş No</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">E-posta</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Ad Soyad</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tutar</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
      </tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:600;color:#2563eb"><a href="<?= adminUrl('siparisler') ?>" style="color:inherit;text-decoration:none"><?= e($o['order_no']) ?></a></td>
          <td style="padding:10px 16px;font-size:12px;color:#6b7280"><?= e($o['guest_email']) ?></td>
          <td style="padding:10px 16px"><?= e($o['shipping_name'] ?? '—') ?></td>
          <td style="padding:10px 16px;font-weight:600"><?= formatPriceTRY($o['total']) ?></td>
          <td style="padding:10px 16px"><span class="badge b-<?= orderStatusColor($o['status']) ?>"><?= orderStatusLabel($o['status']) ?></span></td>
          <td style="padding:10px 16px;color:#9ca3af;font-size:12px"><?= formatDate($o['created_at']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
