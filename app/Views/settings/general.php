<?php ob_start(); ?>

<form method="POST" action="<?= adminUrl('ayarlar/genel') ?>" enctype="multipart/form-data" id="settingsForm">
<?= csrfField() ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <span style="font-size:15px;font-weight:700">Genel Ayarlar</span>
  <button type="submit" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
    Kaydet
  </button>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<!-- SOL -->
<div>

  <!-- Site Bilgileri -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Site Bilgileri</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Site Adı <span style="color:#dc2626">*</span></label>
        <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? '') ?>" placeholder="Magazam">
      </div>
      <div class="form-group">
        <label>Site Açıklaması</label>
        <textarea name="site_description" rows="2" placeholder="Kısa site açıklaması..."><?= e($settings['site_description'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label>Anahtar Kelimeler</label>
        <input type="text" name="site_keywords" value="<?= e($settings['site_keywords'] ?? '') ?>" placeholder="kelime1, kelime2...">
      </div>
      <div class="form-group">
        <label>Meta Başlık Soneki</label>
        <input type="text" name="meta_title_suffix" value="<?= e($settings['meta_title_suffix'] ?? '') ?>" placeholder="| Magazam">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Örn: "Ürün Adı | Magazam"</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group" style="margin-bottom:0">
          <label>E-posta</label>
          <input type="email" name="site_email" value="<?= e($settings['site_email'] ?? '') ?>" placeholder="info@magazam.com">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Telefon</label>
          <input type="text" name="site_phone" value="<?= e($settings['site_phone'] ?? '') ?>" placeholder="+90 212 000 00 00">
        </div>
      </div>
    </div>
  </div>

  <!-- Logo & Favicon -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Logo & Favicon</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Site Logosu</label>
        <?php if (!empty($settings['site_logo'])): ?>
          <div style="margin-bottom:8px;padding:10px;background:#f9fafb;border-radius:8px;display:inline-block">
            <img src="<?= uploadUrl($settings['site_logo']) ?>" alt="" style="height:40px;object-fit:contain">
          </div><br>
        <?php endif; ?>
        <input type="file" name="site_logo" accept="image/*" style="font-size:13px">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">SVG, PNG, WebP — Transparan arka plan önerilir</div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Favicon</label>
        <?php if (!empty($settings['site_favicon'])): ?>
          <div style="margin-bottom:8px">
            <img src="<?= uploadUrl($settings['site_favicon']) ?>" alt="" style="width:32px;height:32px;object-fit:contain">
          </div>
        <?php endif; ?>
        <input type="file" name="site_favicon" accept="image/*,.ico" style="font-size:13px">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">ICO, PNG — 32×32px veya 64×64px</div>
      </div>
    </div>
  </div>

  <!-- Adres -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">İletişim & Adres</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Adres</label>
        <textarea name="site_address" rows="3" placeholder="Şirket adresi..."><?= e($settings['site_address'] ?? '') ?></textarea>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>WhatsApp Numarası</label>
        <input type="text" name="whatsapp_number" value="<?= e($settings['whatsapp_number'] ?? '') ?>" placeholder="905001234567">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Ülke kodu dahil, boşluksuz (Örn: 905001234567)</div>
      </div>
    </div>
  </div>

  <!-- Sosyal Medya -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Sosyal Medya</span></div>
    <div class="card-body">
      <?php
      $socials = [
        ['key'=>'facebook_url',  'label'=>'Facebook',  'placeholder'=>'https://facebook.com/magazam'],
        ['key'=>'instagram_url', 'label'=>'Instagram', 'placeholder'=>'https://instagram.com/magazam'],
        ['key'=>'twitter_url',   'label'=>'Twitter/X', 'placeholder'=>'https://twitter.com/magazam'],
        ['key'=>'youtube_url',   'label'=>'YouTube',   'placeholder'=>'https://youtube.com/@magazam'],
        ['key'=>'tiktok_url',    'label'=>'TikTok',    'placeholder'=>'https://tiktok.com/@magazam'],
        ['key'=>'linkedin_url',  'label'=>'LinkedIn',  'placeholder'=>'https://linkedin.com/company/magazam'],
      ];
      ?>
      <?php foreach ($socials as $i => $s): ?>
        <div class="form-group" style="<?= $i === count($socials)-1 ? 'margin-bottom:0' : '' ?>">
          <label><?= $s['label'] ?></label>
          <input type="url" name="<?= $s['key'] ?>" value="<?= e($settings[$s['key']] ?? '') ?>" placeholder="<?= $s['placeholder'] ?>">
        </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- SAG -->
<div>

  <!-- Mağaza Ayarları -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Mağaza Ayarları</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Varsayılan Para Birimi</label>
        <select name="currency_default">
          <option value="TRY" <?= ($settings['currency_default']??'TRY')==='TRY'?'selected':'' ?>>₺ Türk Lirası (TRY)</option>
          <option value="USD" <?= ($settings['currency_default']??'')==='USD'?'selected':'' ?>>$ Dolar (USD)</option>
          <option value="EUR" <?= ($settings['currency_default']??'')==='EUR'?'selected':'' ?>>€ Euro (EUR)</option>
        </select>
      </div>
      <div class="form-group">
        <label>Varsayılan Dil</label>
        <select name="lang_default">
          <option value="tr" <?= ($settings['lang_default']??'tr')==='tr'?'selected':'' ?>>🇹🇷 Türkçe</option>
          <option value="en" <?= ($settings['lang_default']??'')==='en'?'selected':'' ?>>🇬🇧 English</option>
        </select>
      </div>
      <div class="form-group">
        <label style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0">
          Fiyatlara KDV Dahil
          <input type="hidden" name="tax_included" value="0">
          <input type="checkbox" name="tax_included" value="1"
            <?= ($settings['tax_included']??'1')==='1'?'checked':'' ?>
            style="width:14px;height:14px;accent-color:#2563eb">
        </label>
      </div>
    </div>
  </div>

  <!-- Kargo Ayarları -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Kargo Ayarları</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Ücretsiz Kargo Limiti (₺)</label>
        <input type="number" name="shipping_free_over" value="<?= e($settings['shipping_free_over'] ?? '') ?>" placeholder="0 = Her zaman ücretli" min="0" step="0.01">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Bu tutarın üzerindeki siparişlere ücretsiz kargo</div>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Standart Kargo Ücreti (₺)</label>
        <input type="number" name="shipping_default_cost" value="<?= e($settings['shipping_default_cost'] ?? '') ?>" placeholder="0.00" min="0" step="0.01">
      </div>
    </div>
  </div>

  <!-- Sipariş Ayarları -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Sipariş Ayarları</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Minimum Sipariş Tutarı (₺)</label>
        <input type="number" name="order_min_amount" value="<?= e($settings['order_min_amount'] ?? '') ?>" placeholder="0 = Limit yok" min="0" step="0.01">
      </div>
    </div>
  </div>

  <!-- Bakım Modu -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Bakım Modu</span></div>
    <div class="card-body">
      <div class="form-group">
        <label style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0">
          <div>
            <div style="font-size:13px;font-weight:500">Bakım Modunu Aktif Et</div>
            <div style="font-size:11px;color:#9ca3af;margin-top:1px">Ziyaretçiler bakım sayfasını görür</div>
          </div>
          <input type="hidden" name="maintenance_mode" value="0">
          <input type="checkbox" name="maintenance_mode" value="1"
            <?= ($settings['maintenance_mode']??'0')==='1'?'checked':'' ?>
            style="width:14px;height:14px;accent-color:#dc2626">
        </label>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Bakım Mesajı</label>
        <textarea name="maintenance_message" rows="2" placeholder="Site bakım çalışmaları nedeniyle geçici olarak kapalıdır."><?= e($settings['maintenance_message'] ?? '') ?></textarea>
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">SEO</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Google Site Doğrulama</label>
        <input type="text" name="google_site_verification" value="<?= e($settings['google_site_verification'] ?? '') ?>" placeholder="google-site-verification content değeri">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Search Console'dan alınan meta tag içeriği</div>
      </div>
    </div>
  </div>

  <!-- Kaydet -->
  <div class="card">
    <div class="card-body">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        Tüm Ayarları Kaydet
      </button>
    </div>
  </div>

</div>
</div>
</form>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
.form-group{margin-bottom:12px}
.form-group:last-child{margin-bottom:0}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
