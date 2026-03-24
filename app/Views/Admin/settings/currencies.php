<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Para Birimleri & Döviz Kurları</span>
  <form method="POST" action="<?= adminUrl('ayarlar/para-birimleri/tcmb') ?>">
    <?= csrfField() ?>
    <button type="submit" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/></svg>
      TCMB'den Güncelle
    </button>
  </form>
</div>
<form method="POST" action="<?= adminUrl('ayarlar/para-birimleri/guncelle') ?>">
  <?= csrfField() ?>
  <div class="card" style="margin-bottom:16px;padding:0">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Para Birimi</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sembol</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Kur (1 TRY = ?)</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Aktif</th>
      </tr></thead>
      <tbody>
        <?php foreach ($currencies as $c): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:500">
            <?= e($c['name']) ?> 
            <?php if ($c['is_default']): ?><span class="badge b-primary" style="font-size:10px">Varsayılan</span><?php endif; ?>
          </td>
          <td style="padding:10px 16px;font-size:18px"><?= e($c['symbol']) ?></td>
          <td style="padding:10px 16px">
            <?php if ($c['is_default']): ?>
              <span style="color:#9ca3af;font-size:12px">Sabit: 1.000000</span>
            <?php else: ?>
              <input type="number" name="rate_<?= $c['code'] ?>" value="<?= $c['rate'] ?>" 
                step="0.000001" min="0" style="width:130px;padding:5px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;outline:none">
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px">
            <?php if (!$c['is_default']): ?>
              <input type="checkbox" name="active_<?= $c['code'] ?>" value="1" <?= $c['is_active']?'checked':'' ?> style="accent-color:#2563eb">
            <?php else: ?>
              <span style="color:#9ca3af;font-size:11px">Her zaman aktif</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <button type="submit" class="btn btn-primary">Kurları Kaydet</button>
</form>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
