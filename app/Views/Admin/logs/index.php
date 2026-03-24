<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Hata Logları</span>
  <div style="display:flex;gap:8px">
    <a href="<?= adminUrl('ayarlar/aktiviteler') ?>" class="btn btn-outline btn-sm">Aktivite Logları</a>
    <form method="POST" action="<?= adminUrl('ayarlar/loglar/temizle') ?>">
      <?= csrfField() ?>
      <button type="submit" onclick="return confirm('30 günden eski loglar silinsin mi?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Eski Logları Temizle</button>
    </form>
  </div>
</div>

<!-- DB Hata Logları -->
<div class="card" style="margin-bottom:16px;padding:0">
  <div class="card-header"><span class="card-title">Son Hatalar (DB)</span></div>
  <?php if (empty($dbErrors)): ?>
    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Hata kaydı yok 🎉</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:12px">
      <thead><tr>
        <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Seviye</th>
        <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Mesaj</th>
        <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">URL</th>
        <th style="text-align:left;padding:8px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
      </tr></thead>
      <tbody>
        <?php foreach ($dbErrors as $e): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:8px 16px"><span class="badge <?= $e['level']==='error'?'b-danger':($e['level']==='warning'?'b-warning':'b-info') ?>"><?= $e['level'] ?></span></td>
          <td style="padding:8px 16px;max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= e($e['message']) ?>"><?= e(substr($e['message'], 0, 80)) ?></td>
          <td style="padding:8px 16px;color:#9ca3af;font-family:monospace"><?= e(substr($e['url']??'',0,40)) ?></td>
          <td style="padding:8px 16px;color:#9ca3af"><?= formatDate($e['created_at'], 'd M H:i') ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Log Dosyaları -->
<?php if (!empty($files)): ?>
<div class="card" style="padding:0">
  <div class="card-header"><span class="card-title">Log Dosyaları</span></div>
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead><tr>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Dosya</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Boyut</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
      <th style="border-bottom:1px solid #f3f4f6"></th>
    </tr></thead>
    <tbody>
      <?php foreach ($files as $f): ?>
      <tr style="border-bottom:1px solid #f9fafb">
        <td style="padding:10px 16px;font-family:monospace;font-size:12px"><?= e($f['name']) ?></td>
        <td style="padding:10px 16px;color:#6b7280"><?= formatFileSize($f['size']) ?></td>
        <td style="padding:10px 16px;color:#9ca3af"><?= date('d M Y H:i', $f['mtime']) ?></td>
        <td style="padding:10px 16px">
          <a href="<?= adminUrl('ayarlar/loglar/'.urlencode($f['name'])) ?>" class="btn btn-outline btn-sm">Görüntüle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
