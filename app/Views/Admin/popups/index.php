<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Popup Yönetimi</span>
</div>

<!-- Mevcut Popuplar -->
<?php if (!empty($popups)): ?>
<div class="card" style="margin-bottom:16px;padding:0">
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead><tr>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Popup</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Başlık</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Gecikme</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
      <th style="border-bottom:1px solid #f3f4f6"></th>
    </tr></thead>
    <tbody>
      <?php foreach ($popups as $p): ?>
      <tr style="border-bottom:1px solid #f9fafb">
        <td style="padding:10px 16px">
          <?php if ($p['image']): ?>
            <img src="<?= uploadUrl('popups/'.$p['image']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:5px">
          <?php else: ?>
            <div style="width:60px;height:40px;background:#f3f4f6;border-radius:5px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#9ca3af">Görselsiz</div>
          <?php endif; ?>
        </td>
        <td style="padding:10px 16px;font-weight:500"><?= e($p['title'] ?? '—') ?></td>
        <td style="padding:10px 16px;color:#6b7280"><?= $p['delay'] ?>sn</td>
        <td style="padding:10px 16px"><span class="badge <?= $p['is_active']?'b-success':'b-gray' ?>"><?= $p['is_active']?'Aktif':'Pasif' ?></span></td>
        <td style="padding:10px 16px">
          <form method="POST" action="<?= adminUrl('popuplar/'.$p['id'].'/sil') ?>">
            <?= csrfField() ?>
            <button type="submit" onclick="return confirm('Sil?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endif; ?>

<!-- Yeni Popup -->
<div class="card">
  <div class="card-header"><span class="card-title">Yeni Popup Ekle</span></div>
  <div class="card-body">
    <form method="POST" action="<?= adminUrl('popuplar/ekle') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <div class="form-group">
            <label>Görsel</label>
            <input type="file" name="image" accept="image/*" style="font-size:13px">
          </div>
          <div class="form-group">
            <label>Bağlantı URL</label>
            <input type="text" name="link" placeholder="https://...">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div class="form-group">
              <label>Gecikme (saniye)</label>
              <input type="number" name="delay" value="3" min="0">
            </div>
            <div class="form-group">
              <label>Başlangıç</label>
              <input type="datetime-local" name="starts_at">
            </div>
          </div>
          <div style="display:flex;gap:12px;font-size:13px">
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-weight:400">
              <input type="checkbox" name="show_once" value="1" checked style="accent-color:#2563eb"> Bir kez göster
            </label>
            <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-weight:400">
              <input type="checkbox" name="is_active" value="1" checked style="accent-color:#2563eb"> Aktif
            </label>
          </div>
        </div>
        <div>
          <?php $languages = \App\Core\Database::rows("SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order"); ?>
          <?php foreach ($languages as $lang): ?>
            <div class="form-group">
              <label>Başlık (<?= strtoupper($lang['code']) ?>)</label>
              <input type="text" name="title_<?= $lang['code'] ?>" placeholder="Popup başlığı...">
            </div>
            <div class="form-group">
              <label>İçerik (<?= strtoupper($lang['code']) ?>)</label>
              <textarea name="content_<?= $lang['code'] ?>" rows="3" placeholder="Popup içeriği..." style="width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none"></textarea>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="margin-top:12px">
        <button type="submit" class="btn btn-primary btn-sm">Popup Ekle</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
