<?php
use App\Core\Session;
ob_start();
$isEdit     = !empty($page);
$formUrl    = $isEdit ? adminUrl('sayfalar/'.$page['id'].'/duzenle') : adminUrl('sayfalar/ekle');
$activeLang = Session::get('admin_lang', 'tr');
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('sayfalar') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Sayfalar
    </a>
    <span style="font-size:15px;font-weight:700"><?= $isEdit ? 'Sayfa Düzenle' : 'Yeni Sayfa' ?></span>
  </div>
  <button type="submit" form="pageForm" class="btn btn-primary btn-sm">Kaydet</button>
</div>

<form method="POST" action="<?= $formUrl ?>" id="pageForm">
<?= csrfField() ?>
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px;align-items:start">

<div>
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
            <label>Sayfa Başlığı <span style="color:#dc2626">*</span></label>
            <input type="text" name="title_<?= $lang['code'] ?>"
              value="<?= e($translations[$lang['code']]['title'] ?? '') ?>"
              placeholder="Sayfa başlığı...">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>İçerik</label>
            <textarea name="content_<?= $lang['code'] ?>" rows="16" placeholder="Sayfa içeriği (HTML desteklenir)..."><?= htmlspecialchars_decode(e($translations[$lang['code']]['content'] ?? '')) ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">SEO</span></div>
    <div class="card-body">
      <?php foreach ($languages as $lang): ?>
        <div class="lang-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $lang['code']===$activeLang?'block':'none' ?>">
          <div class="form-group">
            <label>Meta Başlık <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
            <input type="text" name="meta_title_<?= $lang['code'] ?>" value="<?= e($translations[$lang['code']]['meta_title'] ?? '') ?>" maxlength="60">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Meta Açıklama <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
            <textarea name="meta_desc_<?= $lang['code'] ?>" rows="2" maxlength="160"><?= e($translations[$lang['code']]['meta_desc'] ?? '') ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<div style="position:sticky;top:72px;display:flex;flex-direction:column;gap:12px">
  <div class="card">
    <div class="card-body">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Kaydet</button>
    </div>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Ayarlar</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>URL Slug</label>
        <input type="text" name="slug" value="<?= e($page['slug'] ?? '') ?>" placeholder="sayfa-adi" style="font-family:monospace;font-size:12px">
      </div>
      <div class="form-group">
        <label>Şablon</label>
        <select name="template">
          <option value="default" <?= ($page['template']??'default')==='default'?'selected':'' ?>>Varsayılan</option>
          <option value="contact" <?= ($page['template']??'')==='contact'?'selected':'' ?>>İletişim</option>
          <option value="full"    <?= ($page['template']??'')==='full'   ?'selected':'' ?>>Tam Genişlik</option>
        </select>
      </div>
      <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;font-weight:400;margin-bottom:0">
        Aktif
        <input type="checkbox" name="is_active" value="1" <?= !isset($page)||$page['is_active']?'checked':'' ?> style="width:14px;height:14px;accent-color:#2563eb">
      </label>
    </div>
  </div>
</div>
</div>
</form>

<script>
function switchLang(code) {
  document.querySelectorAll('.lang-tab').forEach(t => {
    t.style.color = t.dataset.lang===code?'#2563eb':'#6b7280';
    t.style.borderBottom = t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';
  });
  document.querySelectorAll('.lang-panel').forEach(p => {
    p.style.display = p.dataset.lang===code?'block':'none';
  });
}
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}.form-group:last-child{margin-bottom:0}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select,textarea{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
