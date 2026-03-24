<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Aktivite Logları</span>
  <a href="<?= adminUrl('ayarlar/loglar') ?>" class="btn btn-outline btn-sm">Hata Logları</a>
</div>
<div class="card" style="padding:0">
  <table style="width:100%;border-collapse:collapse;font-size:12px">
    <thead><tr>
      <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">İşlem</th>
      <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Model</th>
      <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Admin</th>
      <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">IP</th>
      <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
    </tr></thead>
    <tbody>
      <?php foreach ($logs as $l): ?>
      <tr style="border-bottom:1px solid #f9fafb">
        <td style="padding:8px 16px;font-family:monospace"><?= e($l['action']) ?></td>
        <td style="padding:8px 16px;color:#6b7280"><?= e($l['model'] ?? '—') ?> <?= $l['model_id']?'#'.$l['model_id']:'' ?></td>
        <td style="padding:8px 16px"><?= e($l['admin_name'] ?? '—') ?></td>
        <td style="padding:8px 16px;color:#9ca3af;font-family:monospace"><?= e($l['ip'] ?? '—') ?></td>
        <td style="padding:8px 16px;color:#9ca3af"><?= formatDate($l['created_at'],'d M H:i') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
