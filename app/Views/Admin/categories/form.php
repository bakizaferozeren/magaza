<?php
use App\Core\Session;
ob_start();
$isEdit     = !empty($category);
$formUrl    = $isEdit ? adminUrl('kategoriler/'.$category['id'].'/duzenle') : adminUrl('kategoriler/ekle');
$activeLang = Session::get('admin_lang', 'tr');
?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('kategoriler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Kategoriler
    </a>
    <span style="font-size:15px;font-weight:700"><?= $isEdit ? 'Kategori Düzenle' : 'Yeni Kategori' ?></span>
  </div>
  <button type="submit" form="catForm" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
    Kaydet
  </button>
</div>

<form method="POST" action="<?= $formUrl ?>" enctype="multipart/form-data" id="catForm">
<?= csrfField() ?>
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start">

<!-- SOL -->
<div>
  <!-- Isim + Aciklama -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-body">
      <?php if (count($languages) > 1): ?>
      <div style="display:flex;border-bottom:1px solid #f3f4f6;margin:-16px -16px 16px">
        <?php foreach ($languages as $lang): ?>
          <button type="button" class="lang-tab" data-lang="<?= $lang['code'] ?>"
            onclick="switchLang('<?= $lang['code'] ?>')"
            style="padding:10px 16px;font-size:13px;font-weight:500;border:none;background:none;cursor:pointer;color:<?= $lang['code']===$activeLang?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $lang['code']===$activeLang?'#2563eb':'transparent' ?>;margin-bottom:-1px">
            <?= strtoupper($lang['code']) ?>
          </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php foreach ($languages as $lang): ?>
        <div class="lang-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $lang['code']===$activeLang?'block':'none' ?>">
          <div class="form-group">
            <label>Kategori Adı <span style="color:#dc2626">*</span> <span style="font-weight:400;color:#9ca3af;font-size:11px">(<?= strtoupper($lang['code']) ?>)</span></label>
            <input type="text" id="nameInput_<?= $lang['code'] ?>" name="name_<?= $lang['code'] ?>"
              value="<?= e($translations[$lang['code']]['name'] ?? '') ?>"
              placeholder="Kategori adı...">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Kısa Açıklama <span style="font-weight:400;color:#9ca3af;font-size:11px">(<?= strtoupper($lang['code']) ?>)</span></label>
            <textarea name="short_desc_<?= $lang['code'] ?>" rows="3" placeholder="Kategori açıklaması..."><?= e($translations[$lang['code']]['short_desc'] ?? '') ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Gorsel -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Kategori Görseli</span></div>
    <div class="card-body">
      <?php if (!empty($category['image'])): ?>
        <div style="margin-bottom:12px">
          <img src="<?= uploadUrl('categories/'.$category['image']) ?>" alt="" style="height:80px;border-radius:8px;border:1px solid #e5e7eb;object-fit:cover">
        </div>
      <?php endif; ?>
      <input type="file" name="image" accept="image/*" style="font-size:13px">
      <div style="font-size:11px;color:#9ca3af;margin-top:4px">JPG, PNG, WebP önerilir. Banner boyutu: 800×300px</div>
    </div>
  </div>

  <!-- SEO -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">SEO</span></div>
    <div class="card-body">
      <?php foreach ($languages as $lang): ?>
        <div class="lang-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $lang['code']===$activeLang?'block':'none' ?>">
          <div class="form-group">
            <label>Meta Başlık <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
            <input type="text" name="meta_title_<?= $lang['code'] ?>" value="<?= e($translations[$lang['code']]['meta_title'] ?? '') ?>" placeholder="SEO başlığı..." maxlength="60">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Meta Açıklama <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
            <textarea name="meta_desc_<?= $lang['code'] ?>" rows="2" placeholder="SEO açıklaması..." maxlength="160"><?= e($translations[$lang['code']]['meta_desc'] ?? '') ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<!-- SAG -->
<div style="display:flex;flex-direction:column;gap:12px;position:sticky;top:72px">

  <!-- Kaydet -->
  <div class="card">
    <div class="card-body">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        <?= $isEdit ? 'Değişiklikleri Kaydet' : 'Kategoriyi Kaydet' ?>
      </button>
    </div>
  </div>

  <!-- Ayarlar -->
  <div class="card">
    <div class="card-header"><span class="card-title">Ayarlar</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Üst Kategori</label>
        <select name="parent_id">
          <option value="">— Ana Kategori —</option>
          <?php foreach ($parents as $p): ?>
            <option value="<?= $p['id'] ?>" <?= ($category['parent_id']??'')==$p['id']?'selected':'' ?>><?= e($p['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>URL Slug</label>
        <input type="text" name="slug" id="slugInput" value="<?= e($category['slug'] ?? '') ?>" placeholder="kategori-adi" style="font-family:monospace;font-size:12px">
      </div>
      <div class="form-group">
        <label>Sıra No</label>
        <input type="number" name="sort_order" value="<?= e($category['sort_order'] ?? 0) ?>" min="0">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Küçük sayı öne çıkar</div>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px">
        <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;margin-bottom:0;font-weight:400">
          Aktif
          <input type="checkbox" name="is_active" value="1" <?= !isset($category)||$category['is_active']?'checked':'' ?> style="width:14px;height:14px;accent-color:#2563eb">
        </label>
      </div>
    </div>
  </div>

</div>
</div>
</form>

<script>
(function() {
  var slugEdited = <?= ($isEdit && !empty($category['slug'])) ? 'true' : 'false' ?>;

  function makeSlug(v) {
    return v.toLowerCase()
      .replace(/ş/g,'s').replace(/ı/g,'i').replace(/ğ/g,'g')
      .replace(/ü/g,'u').replace(/ö/g,'o').replace(/ç/g,'c')
      .replace(/Ş/g,'s').replace(/İ/g,'i').replace(/Ğ/g,'g')
      .replace(/Ü/g,'u').replace(/Ö/g,'o').replace(/Ç/g,'c')
      .replace(/[^a-z0-9\s]/g,'').trim()
      .replace(/\s+/g,'-').replace(/-+/g,'-').replace(/^-|-$/g,'');
  }

  // TR name input'u dinle
  var nameEl = document.getElementById('nameInput_tr');
  if (nameEl) {
    nameEl.addEventListener('input', function() {
      if (slugEdited) return;
      document.getElementById('slugInput').value = makeSlug(this.value);
    });
  }

  // Slug manuel değişince kilitle
  var si = document.getElementById('slugInput');
  if (si) {
    si.addEventListener('input', function() {
      slugEdited = this.value.length > 0;
    });
    si.addEventListener('blur', function() {
      this.value = makeSlug(this.value);
    });
  }

  window.switchLang = function(code) {
    document.querySelectorAll('.lang-tab').forEach(function(t) {
      t.style.color = t.dataset.lang===code?'#2563eb':'#6b7280';
      t.style.borderBottom = t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';
    });
    document.querySelectorAll('.lang-panel').forEach(function(p) {
      p.style.display = p.dataset.lang===code?'block':'none';
    });
  };
})();
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
.form-group{margin-bottom:12px}
.form-group:last-child{margin-bottom:0}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
