<?php ob_start(); ?>
<div style="margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Cache Yönetimi</span>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Cache Dosyaları</div>
    <div style="font-size:28px;font-weight:700"><?= $fileCount ?></div>
    <div style="font-size:12px;color:#9ca3af;margin-top:4px"><?= formatFileSize($cacheSize) ?> toplam boyut</div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:12px">Cache Temizle</div>
    <form method="POST" action="<?= adminUrl('ayarlar/cache/temizle') ?>">
      <?= csrfField() ?>
      <p style="font-size:12px;color:#6b7280;margin-bottom:12px">Tüm önbelleği temizler. Sonraki ziyarette yeniden oluşturulur.</p>
      <button type="submit" class="btn btn-danger" onclick="return confirm('Cache temizlensin mi?')" style="width:100%;justify-content:center">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        Cache'i Temizle
      </button>
    </form>
  </div>
</div>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
