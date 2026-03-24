<?php
use App\Core\Session;
ob_start();
$activeLang = Session::get('admin_lang', 'tr');
$languages  = \App\Core\Database::rows("SELECT * FROM languages WHERE is_active=1 ORDER BY sort_order");
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Sık Sorulan Sorular</span>
  <button onclick="document.getElementById('addFaqForm').style.display='block';this.style.display='none'" class="btn btn-primary btn-sm">+ Soru Ekle</button>
</div>

<!-- Yeni SSS Ekle -->
<div id="addFaqForm" style="display:none;margin-bottom:16px">
  <div class="card">
    <div class="card-header">
      <span class="card-title">Yeni Soru Ekle</span>
      <button onclick="this.closest('.card').parentElement.style.display='none';document.querySelector('.btn-primary').style.display=''" class="btn btn-outline btn-sm">İptal</button>
    </div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('sss/ekle') ?>">
        <?= csrfField() ?>
        <?php if (count($languages) > 1): ?>
        <div style="display:flex;border-bottom:1px solid #f3f4f6;margin:-16px -16px 16px">
          <?php foreach ($languages as $i => $lang): ?>
            <button type="button" class="nfaq-tab" data-lang="<?= $lang['code'] ?>"
              onclick="switchNewFaq('<?= $lang['code'] ?>')"
              style="padding:8px 14px;font-size:12px;font-weight:500;border:none;background:none;cursor:pointer;color:<?= $i===0?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $i===0?'#2563eb':'transparent' ?>;margin-bottom:-1px">
              <?= strtoupper($lang['code']) ?>
            </button>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php foreach ($languages as $i => $lang): ?>
          <div class="nfaq-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $i===0?'block':'none' ?>">
            <div class="form-group">
              <label>Soru <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
              <input type="text" name="question_<?= $lang['code'] ?>" placeholder="Soru metni...">
            </div>
            <div class="form-group" style="margin-bottom:0">
              <label>Cevap <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
              <textarea name="answer_<?= $lang['code'] ?>" rows="4" placeholder="Cevap metni..."></textarea>
            </div>
          </div>
        <?php endforeach; ?>

        <div style="display:flex;align-items:center;gap:12px;margin-top:14px">
          <input type="number" name="sort_order" value="0" min="0" style="width:80px;padding:6px 10px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;outline:none" placeholder="Sıra">
          <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer">
            <input type="checkbox" name="is_active" value="1" checked style="accent-color:#2563eb"> Aktif
          </label>
          <button type="submit" class="btn btn-primary btn-sm">Ekle</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- SSS Listesi -->
<?php if (empty($faqs)): ?>
  <div class="card" style="text-align:center;padding:3rem;color:#9ca3af">
    <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz soru yok</p>
  </div>
<?php else: ?>
  <div style="display:flex;flex-direction:column;gap:8px">
    <?php foreach ($faqs as $faq): ?>
      <div class="card" style="padding:0">
        <details>
          <summary style="padding:14px 16px;cursor:pointer;list-style:none;display:flex;align-items:center;justify-content:space-between;font-weight:500;font-size:13px">
            <div style="display:flex;align-items:center;gap:8px">
              <span style="font-size:11px;color:#9ca3af;font-family:monospace">#{<?= $faq['id'] ?>}</span>
              <?= e($faq['question'] ?? 'Soru yok') ?>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <span class="badge <?= $faq['is_active']?'b-success':'b-gray' ?>"><?= $faq['is_active']?'Aktif':'Pasif' ?></span>
              <svg width="14" height="14" viewBox="0 0 20 20" fill="#9ca3af" class="faq-arrow"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </div>
          </summary>
          <div style="padding:0 16px 16px;border-top:1px solid #f3f4f6">
            <form method="POST" action="<?= adminUrl('sss/'.$faq['id'].'/duzenle') ?>">
              <?= csrfField() ?>
              <?php if (count($languages) > 1): ?>
              <div style="display:flex;border-bottom:1px solid #f3f4f6;margin:0 -16px 14px;padding:0 16px">
                <?php foreach ($languages as $i => $lang): ?>
                  <button type="button" class="efaq-tab-<?= $faq['id'] ?>" data-lang="<?= $lang['code'] ?>"
                    onclick="switchEditFaq(<?= $faq['id'] ?>, '<?= $lang['code'] ?>')"
                    style="padding:7px 12px;font-size:11px;font-weight:500;border:none;background:none;cursor:pointer;color:<?= $i===0?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $i===0?'#2563eb':'transparent' ?>;margin-bottom:-1px">
                    <?= strtoupper($lang['code']) ?>
                  </button>
                <?php endforeach; ?>
              </div>
              <?php endif; ?>

              <?php
              $faqTranslations = [];
              foreach ($languages as $lang) {
                  $faqTranslations[$lang['code']] = \App\Core\Database::row(
                      "SELECT * FROM faq_translations WHERE faq_id=? AND lang=?",
                      [$faq['id'], $lang['code']]
                  ) ?? [];
              }
              ?>

              <?php foreach ($languages as $i => $lang): ?>
                <div class="efaq-panel-<?= $faq['id'] ?>" data-lang="<?= $lang['code'] ?>" style="display:<?= $i===0?'block':'none' ?>">
                  <div class="form-group">
                    <label>Soru <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
                    <input type="text" name="question_<?= $lang['code'] ?>" value="<?= e($faqTranslations[$lang['code']]['question'] ?? '') ?>">
                  </div>
                  <div class="form-group" style="margin-bottom:12px">
                    <label>Cevap <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
                    <textarea name="answer_<?= $lang['code'] ?>" rows="4"><?= e($faqTranslations[$lang['code']]['answer'] ?? '') ?></textarea>
                  </div>
                </div>
              <?php endforeach; ?>

              <div style="display:flex;align-items:center;gap:12px">
                <input type="number" name="sort_order" value="<?= $faq['sort_order'] ?>" min="0" style="width:70px;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:12px;outline:none">
                <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;font-weight:400">
                  <input type="checkbox" name="is_active" value="1" <?= $faq['is_active']?'checked':'' ?> style="accent-color:#2563eb"> Aktif
                </label>
                <button type="submit" class="btn btn-primary btn-sm">Kaydet</button>
                <form method="POST" action="<?= adminUrl('sss/'.$faq['id'].'/sil') ?>" style="margin-left:auto">
                  <?= csrfField() ?>
                  <button type="submit" onclick="return confirm('Bu soruyu silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
                </form>
              </div>
            </form>
          </div>
        </details>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<script>
function switchNewFaq(code) {
  document.querySelectorAll('.nfaq-tab').forEach(t => {
    t.style.color = t.dataset.lang===code?'#2563eb':'#6b7280';
    t.style.borderBottom = t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';
  });
  document.querySelectorAll('.nfaq-panel').forEach(p => {
    p.style.display = p.dataset.lang===code?'block':'none';
  });
}
function switchEditFaq(faqId, code) {
  document.querySelectorAll('.efaq-tab-'+faqId).forEach(t => {
    t.style.color = t.dataset.lang===code?'#2563eb':'#6b7280';
    t.style.borderBottom = t.dataset.lang===code?'2px solid #2563eb':'2px solid transparent';
  });
  document.querySelectorAll('.efaq-panel-'+faqId).forEach(p => {
    p.style.display = p.dataset.lang===code?'block':'none';
  });
}
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:10px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,textarea{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}details[open] .faq-arrow{transform:rotate(180deg)}summary::-webkit-details-marker{display:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
