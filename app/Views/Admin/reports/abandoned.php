<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Terk Edilmiş Sepet Raporu</span>
  <form method="GET" style="display:flex;gap:6px;align-items:center">
    <input type="date" name="from" value="<?= $from ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
    <span style="font-size:12px;color:#9ca3af">—</span>
    <input type="date" name="to" value="<?= $to ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
  </form>
</div>
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Toplam Terk</div>
    <div style="font-size:24px;font-weight:700"><?= $summary['total'] ?? 0 ?></div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Kurtarılan</div>
    <div style="font-size:24px;font-weight:700;color:#16a34a"><?= $summary['recovered'] ?? 0 ?></div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Kaybedilen Ciro</div>
    <div style="font-size:22px;font-weight:700;color:#dc2626"><?= formatPriceTRY($summary['lost_revenue'] ?? 0) ?></div>
  </div>
</div>
<div class="card" style="padding:0">
  <?php if (empty($carts)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Bu dönemde terk edilmiş sepet yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Müşteri</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tutar</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Bildirim</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
      </tr></thead>
      <tbody>
        <?php foreach ($carts as $c): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:500"><?= e($c['customer_name']) ?></td>
          <td style="padding:10px 16px;font-weight:600"><?= formatPriceTRY($c['total'] ?? 0) ?></td>
          <td style="padding:10px 16px;color:#6b7280;font-size:12px"><?= $c['notified_at'] ? formatDate($c['notified_at']) : '—' ?></td>
          <td style="padding:10px 16px;color:#9ca3af;font-size:12px"><?= formatDate($c['created_at']) ?></td>
          <td style="padding:10px 16px"><span class="badge <?= $c['recovered']?'b-success':'b-warning' ?>"><?= $c['recovered']?'Kurtarıldı':'Terk Edildi' ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
