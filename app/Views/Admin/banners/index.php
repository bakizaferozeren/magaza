<?php
// BANNERLAR INDEX VIEW
// Save to: Views/Admin/banners/index.php
ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Bannerlar</span>
</div>

<!-- Mevcut Bannerlar -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><span class="card-title">Mevcut Bannerlar</span></div>
  <?php if (empty($banners)): ?>
    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Henüz banner yok</div>
  <?php else: ?>
    <div style="padding:12px;display:flex;flex-direction:column;gap:8px">
      <?php foreach ($banners as $b): ?>
        <div style="display:flex;align-items:center;gap:12px;padding:10px;background:#f9fafb;border-radius:8px">
          <img src="<?= uploadUrl('banners/'.$b['image']) ?>" style="width:100px;height:50px;object-fit:cover;border-radius:5px;flex-shrink:0">
          <div style="flex:1">
            <div style="font-size:13px;font-weight:500"><?= e($b['title'] ?? 'Başlıksız') ?></div>
            <div style="font-size:11px;color:#9ca3af">Pozisyon: <?= e($b['position']) ?></div>
            <?php if ($b['link']): ?><div style="font-size:11px;color:#2563eb"><?= e($b['link']) ?></div><?php endif; ?>
          </div>
          <span class="badge <?= $b['is_active']?'b-success':'b-gray' ?>"><?= $b['is_active']?'Aktif':'Pasif' ?></span>
          <form method="POST" action="<?= adminUrl('bannerlar/'.$b['id'].'/sil') ?>">
            <?= csrfField() ?>
            <button type="submit" onclick="return confirm('Sil?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
          </form>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Yeni Banner -->
<div class="card">
  <div class="card-header"><span class="card-title">Yeni Banner Ekle</span></div>
  <div class="card-body">
    <form method="POST" action="<?= adminUrl('bannerlar/ekle') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <div class="form-group">
            <label>Görsel <span style="color:#dc2626">*</span></label>
            <input type="file" name="image" accept="image/*" required style="font-size:13px">
          </div>
          <div class="form-group">
            <label>Pozisyon</label>
            <select name="position">
              <option value="home_top">Anasayfa Üst</option>
              <option value="home_mid">Anasayfa Orta</option>
              <option value="sidebar">Kenar Çubuğu</option>
              <option value="category_top">Kategori Üst</option>
            </select>
          </div>
          <div class="form-group">
            <label>Bağlantı URL</label>
            <input type="text" name="link" placeholder="/urunler veya https://...">
          </div>
          <label style="display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" checked style="accent-color:#2563eb"> Aktif
          </label>
        </div>
        <div>
          <?php $languages = \App\Core\Database::rows("SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order"); ?>
          <?php foreach ($languages as $i => $lang): ?>
            <div style="display:<?= $i===0?'block':'none' ?>">
              <div class="form-group">
                <label>Başlık (<?= strtoupper($lang['code']) ?>)</label>
                <input type="text" name="title_<?= $lang['code'] ?>" placeholder="Banner başlığı...">
              </div>
              <div class="form-group">
                <label>Alt Başlık (<?= strtoupper($lang['code']) ?>)</label>
                <input type="text" name="subtitle_<?= $lang['code'] ?>" placeholder="Alt başlık...">
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="margin-top:12px">
        <button type="submit" class="btn btn-primary btn-sm">Banner Ekle</button>
      </div>
    </form>
  </div>
</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
