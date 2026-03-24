<?php
use App\Core\Session;
ob_start();
$activeLang = Session::get('admin_lang', 'tr');
$languages  = \App\Core\Database::rows("SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order");
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Slider & Banner</span>
</div>

<!-- Mevcut Sliderlar -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header"><span class="card-title">Mevcut Sliderlar</span></div>
  <?php if (empty($sliders)): ?>
    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Henüz slider yok</div>
  <?php else: ?>
    <div style="padding:12px">
      <div id="sliderList" style="display:flex;flex-direction:column;gap:8px">
        <?php foreach ($sliders as $s): ?>
          <div style="display:flex;align-items:center;gap:12px;padding:10px 12px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb" data-id="<?= $s['id'] ?>">
            <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:16px;height:16px;flex-shrink:0;cursor:grab">
              <path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/>
            </svg>
            <img src="<?= uploadUrl('sliders/'.$s['image']) ?>" style="width:80px;height:45px;object-fit:cover;border-radius:5px;flex-shrink:0">
            <div style="flex:1">
              <div style="font-size:13px;font-weight:500"><?= e($s['title'] ?? 'Başlıksız') ?></div>
              <?php if ($s['subtitle']): ?>
                <div style="font-size:11px;color:#9ca3af"><?= e($s['subtitle']) ?></div>
              <?php endif; ?>
              <?php if ($s['link']): ?>
                <div style="font-size:11px;color:#2563eb"><?= e($s['link']) ?></div>
              <?php endif; ?>
            </div>
            <span class="badge <?= $s['is_active']?'b-success':'b-gray' ?>"><?= $s['is_active']?'Aktif':'Pasif' ?></span>
            <form method="POST" action="<?= adminUrl('sliderlar/'.$s['id'].'/sil') ?>">
              <?= csrfField() ?>
              <button type="submit" onclick="return confirm('Bu slider\'ı silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Yeni Slider Ekle -->
<div class="card">
  <div class="card-header"><span class="card-title">Yeni Slider Ekle</span></div>
  <div class="card-body">
    <form method="POST" action="<?= adminUrl('sliderlar/ekle') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div>
          <div class="form-group">
            <label>Görsel <span style="color:#dc2626">*</span></label>
            <input type="file" name="image" accept="image/*" required style="font-size:13px">
            <div style="font-size:11px;color:#9ca3af;margin-top:2px">Önerilen: 1920×600px</div>
          </div>
          <div class="form-group">
            <label>Bağlantı URL</label>
            <input type="text" name="link" placeholder="/urunler veya https://...">
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
            <div class="form-group">
              <label>Sıra No</label>
              <input type="number" name="sort_order" value="0" min="0">
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;padding-bottom:2px">
              <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin-bottom:0;font-weight:400;font-size:13px">
                <input type="checkbox" name="is_active" value="1" checked style="width:14px;height:14px;accent-color:#2563eb">
                Aktif
              </label>
            </div>
          </div>
        </div>
        <div>
          <?php if (count($languages) > 1): ?>
          <div style="display:flex;border-bottom:1px solid #f3f4f6;margin-bottom:12px">
            <?php foreach ($languages as $i => $lang): ?>
              <button type="button" class="slang-tab" data-lang="<?= $lang['code'] ?>"
                onclick="switchSliderLang('<?= $lang['code'] ?>')"
                style="padding:6px 12px;font-size:12px;font-weight:500;border:none;background:none;cursor:pointer;color:<?= $i===0?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $i===0?'#2563eb':'transparent' ?>;margin-bottom:-1px">
                <?= strtoupper($lang['code']) ?>
              </button>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>

          <?php foreach ($languages as $i => $lang): ?>
            <div class="slang-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $i===0?'block':'none' ?>">
              <div class="form-group">
                <label>Başlık <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
                <input type="text" name="title_<?= $lang['code'] ?>" placeholder="Slider başlığı...">
              </div>
              <div class="form-group">
                <label>Alt Başlık <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
                <input type="text" name="subtitle_<?= $lang['code'] ?>" placeholder="Kısa açıklama...">
              </div>
              <div class="form-group" style="margin-bottom:0">
                <label>Buton Metni <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
                <input type="text" name="btn_text_<?= $lang['code'] ?>" placeholder="Alışverişe Başla">
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div style="margin-top:14px">
        <button type="submit" class="btn btn-primary btn-sm">Slider Ekle</button>
      </div>
    </form>
  </div>
</div>

<script>
function switchSliderLang(code) {
  document.querySelectorAll('.slang-tab').forEach(t => {
    t.style.color = t.dataset.lang===code?'#2563eb':'#6b7280';
    t.style.borderBottom = t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';
  });
  document.querySelectorAll('.slang-panel').forEach(p => {
    p.style.display = p.dataset.lang===code?'block':'none';
  });
}
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
