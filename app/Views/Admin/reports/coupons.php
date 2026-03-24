<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Kupon Raporları</span>
  <form method="GET" style="display:flex;gap:6px;align-items:center">
    <input type="date" name="from" value="<?= $from ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
    <span style="font-size:12px;color:#9ca3af">—</span>
    <input type="date" name="to" value="<?= $to ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
  </form>
</div>
<div class="card" style="padding:0">
  <?php if (empty($coupons)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Bu dönemde kupon kullanımı yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Kupon Kodu</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">İndirim</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Kullanım</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Toplam İndirim</th>
      </tr></thead>
      <tbody>
        <?php foreach ($coupons as $c): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-family:monospace;font-weight:700;font-size:12px;background:#f9fafb"><?= e($c['code']) ?></td>
          <td style="padding:10px 16px"><?= $c['type']==='percent'?'%'.$c['value']:formatPriceTRY($c['value']) ?></td>
          <td style="padding:10px 16px;font-weight:600"><?= $c['used_count'] ?> kez</td>
          <td style="padding:10px 16px;font-weight:600;color:#16a34a"><?= formatPriceTRY($c['total_discount'] ?? 0) ?></td>
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
