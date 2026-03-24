<?php
use App\Core\Session;
ob_start();
$activeLang = Session::get('admin_lang','tr');
?>
<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
  <a href="<?= adminUrl('ayarlar/mail-sablonlari') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">← Mail Şablonları</a>
  <span style="font-size:15px;font-weight:700">Şablon: <?= e($template['code']) ?></span>
</div>
<form method="POST" action="<?= adminUrl('ayarlar/mail-sablonlari/'.$template['id'].'/duzenle') ?>">
  <?= csrfField() ?>
  <div style="display:grid;grid-template-columns:1fr 200px;gap:16px">
    <div class="card">
      <div class="card-body">
        <?php if (count($languages)>1): ?>
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
              <label>Konu (<?= strtoupper($lang['code']) ?>)</label>
              <input type="text" name="subject_<?= $lang['code'] ?>" value="<?= e($translations[$lang['code']]['subject'] ?? '') ?>" placeholder="Mail konusu...">
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label>İçerik (<?= strtoupper($lang['code']) ?>)</label>
              <div style="font-size:11px;color:#9ca3af;margin-bottom:4px">Değişkenler: {{order_no}}, {{customer_name}}, {{total}}, {{status}}, {{site_name}}</div>
              <textarea name="body_<?= $lang['code'] ?>" rows="20" style="width:100%;padding:8px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;font-family:monospace;outline:none"><?= htmlspecialchars($translations[$lang['code']]['body'] ?? '') ?></textarea>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <div style="display:flex;flex-direction:column;gap:12px">
      <div class="card">
        <div class="card-body">
          <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;font-weight:400;margin-bottom:12px">
            Aktif <input type="checkbox" name="is_active" value="1" <?= $template['is_active']?'checked':'' ?> style="width:14px;height:14px;accent-color:#2563eb">
          </label>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Kaydet</button>
        </div>
      </div>
    </div>
  </div>
</form>
<script>
function switchLang(code){
  document.querySelectorAll('.lang-tab').forEach(t=>{t.style.color=t.dataset.lang===code?'#2563eb':'#6b7280';t.style.borderBottom=t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';});
  document.querySelectorAll('.lang-panel').forEach(p=>{p.style.display=p.dataset.lang===code?'block':'none';});
}
</script>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
