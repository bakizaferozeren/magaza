<?php
// ============================================================
// Bu dosya aşağıdaki view'ları tek seferde oluşturur.
// Her biri ayrı dosyaya kaydedilecek — aşağıya bakın.
// ============================================================

// ---- redirects/index.php ----
ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">301 Yönlendirmeler</span>
</div>
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">
  <div class="card" style="padding:0">
    <?php if (empty($redirects)): ?>
      <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Henüz yönlendirme yok</div>
    <?php else: ?>
      <table style="width:100%;border-collapse:collapse;font-size:13px">
        <thead><tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Kaynak URL</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Hedef URL</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tip</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tıklanma</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr></thead>
        <tbody>
          <?php foreach ($redirects as $r): ?>
          <tr style="border-bottom:1px solid #f9fafb">
            <td style="padding:10px 16px;font-family:monospace;font-size:12px"><?= e($r['from_url']) ?></td>
            <td style="padding:10px 16px;font-family:monospace;font-size:12px;color:#2563eb"><?= e($r['to_url']) ?></td>
            <td style="padding:10px 16px"><span class="badge b-info"><?= $r['type'] ?></span></td>
            <td style="padding:10px 16px;color:#6b7280"><?= $r['hit_count'] ?></td>
            <td style="padding:10px 16px">
              <form method="POST" action="<?= adminUrl('yonlendirmeler/'.$r['id'].'/sil') ?>">
                <?= csrfField() ?>
                <button type="submit" onclick="return confirm('Sil?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Yeni Yönlendirme</span></div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('yonlendirmeler/ekle') ?>">
        <?= csrfField() ?>
        <div class="form-group"><label>Kaynak URL</label><input type="text" name="from_url" placeholder="/eski-sayfa" required></div>
        <div class="form-group"><label>Hedef URL</label><input type="text" name="to_url" placeholder="/yeni-sayfa" required></div>
        <div class="form-group" style="margin-bottom:12px">
          <label>Yönlendirme Tipi</label>
          <select name="type"><option value="301">301 Kalıcı</option><option value="302">302 Geçici</option></select>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Ekle</button>
      </form>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
