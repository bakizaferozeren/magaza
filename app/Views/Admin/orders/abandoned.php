<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Terk Edilmiş Sepetler</span>
</div>
<div class="card" style="padding:0">
  <?php if (empty($carts)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Terk edilmiş sepet yok 🎉</div>
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
          <td style="padding:10px 16px">
            <span class="badge <?= $c['recovered']?'b-success':'b-warning' ?>"><?= $c['recovered']?'Kurtarıldı':'Terk Edildi' ?></span>
          </td>
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
