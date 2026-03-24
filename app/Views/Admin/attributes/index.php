<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Varyasyon Nitelikleri</span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<!-- Nitelik Listesi -->
<div>
  <?php foreach ($types as $type): ?>
  <div class="card" style="margin-bottom:12px">
    <div class="card-header">
      <span class="card-title"><?= e($type['name']) ?> <span style="font-size:11px;font-weight:400;color:#9ca3af">(<?= $type['option_count'] ?> seçenek)</span></span>
      <div style="display:flex;gap:6px">
        <form method="POST" action="<?= adminUrl('nitelikler/'.$type['id'].'/sil') ?>">
          <?= csrfField() ?>
          <button type="submit" onclick="return confirm('Silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
        </form>
      </div>
    </div>
    <div class="card-body">
      <!-- Mevcut seçenekler -->
      <?php
      $options = \App\Core\Database::rows(
          "SELECT * FROM variation_options WHERE variation_type_id=? ORDER BY sort_order ASC",
          [$type['id']]
      );
      ?>
      <?php if (!empty($options)): ?>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px">
          <?php foreach ($options as $opt): ?>
            <div style="display:flex;align-items:center;gap:4px;padding:3px 8px;background:#f3f4f6;border-radius:6px;font-size:12px">
              <?php if ($opt['value'] && preg_match('/^#[0-9a-fA-F]{3,6}$/', $opt['value'])): ?>
                <span style="width:12px;height:12px;border-radius:50%;background:<?= e($opt['value']) ?>;display:inline-block;border:1px solid #e5e7eb"></span>
              <?php endif; ?>
              <?= e($opt['name']) ?>
              <?php if ($opt['value'] && !preg_match('/^#/', $opt['value'])): ?>
                <span style="color:#9ca3af">(<?= e($opt['value']) ?>)</span>
              <?php endif; ?>
              <form method="POST" action="<?= adminUrl('nitelikler/secenek/'.$opt['id'].'/sil') ?>" style="display:inline">
                <?= csrfField() ?>
                <button type="submit" onclick="return confirm('Sil?')" style="background:none;border:none;cursor:pointer;color:#dc2626;padding:0;font-size:11px;line-height:1">×</button>
              </form>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Seçenek ekle -->
      <form method="POST" action="<?= adminUrl('nitelikler/'.$type['id'].'/secenek') ?>">
        <?= csrfField() ?>
        <div style="display:flex;gap:8px">
          <input type="text" name="name" placeholder="Seçenek adı (Örn: Kırmızı)" style="flex:1;padding:6px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;outline:none" required>
          <input type="text" name="value" placeholder="Değer (Örn: #FF0000)" style="width:120px;padding:6px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;outline:none">
          <button type="submit" class="btn btn-primary btn-sm">Ekle</button>
        </div>
      </form>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if (empty($types)): ?>
    <div class="card" style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz nitelik yok</p>
    </div>
  <?php endif; ?>
</div>

<!-- Yeni Nitelik Ekle -->
<div>
  <div class="card">
    <div class="card-header"><span class="card-title">Yeni Nitelik Ekle</span></div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('nitelikler/ekle') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label>Nitelik Adı <span style="color:#dc2626">*</span></label>
          <input type="text" name="name" placeholder="Örn: Renk, Beden, Malzeme..." required>
        </div>
        <div class="form-group" style="margin-bottom:12px">
          <label>Sıra No</label>
          <input type="number" name="sort_order" value="0" min="0">
        </div>
        <button type="submit" class="btn btn-primary">Nitelik Ekle</button>
      </form>

      <div style="margin-top:20px;padding-top:16px;border-top:1px solid #f3f4f6">
        <p style="font-size:12px;color:#6b7280;margin-bottom:8px">Hazır Nitelikler:</p>
        <div style="display:flex;flex-wrap:wrap;gap:6px">
          <?php foreach (['Renk', 'Beden', 'Malzeme', 'Kapasite', 'Ağırlık'] as $preset): ?>
            <button type="button"
              onclick="document.querySelector('[name=name]').value='<?= $preset ?>'"
              class="btn btn-outline btn-sm" style="font-size:11px">
              <?= $preset ?>
            </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</div>

</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
