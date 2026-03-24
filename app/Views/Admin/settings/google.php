<?php ob_start(); ?>

<form method="POST" action="<?= adminUrl('ayarlar/google') ?>">
<?= csrfField() ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <span style="font-size:15px;font-weight:700">Google Entegrasyonları</span>
  <button type="submit" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
    Kaydet
  </button>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<!-- SOL -->
<div>

  <!-- GA4 -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header">
      <span class="card-title">Google Analytics 4</span>
      <span class="badge b-info">Ücretsiz</span>
    </div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Measurement ID</label>
        <input type="text" name="ga4_measurement_id" value="<?= e($google['ga4_measurement_id'] ?? '') ?>" placeholder="G-XXXXXXXXXX">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Google Analytics → Veri Akışları → Measurement ID</div>
      </div>
    </div>
  </div>

  <!-- GTM -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Google Tag Manager</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>GTM Container ID</label>
        <input type="text" name="gtm_id" value="<?= e($settings['gtm_id'] ?? '') ?>" placeholder="GTM-XXXXXXX">
      </div>
    </div>
  </div>

  <!-- Search Console -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Google Search Console</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Doğrulama Kodu</label>
        <input type="text" name="search_console_code" value="<?= e($google['search_console_code'] ?? '') ?>" placeholder="google-site-verification=xxxxx...">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">HTML meta tag yöntemiyle doğrulama kodu</div>
      </div>
    </div>
  </div>

  <!-- Merchant Center -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Google Merchant Center</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Ürün Feed URL</label>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="text" name="merchant_feed_url" value="<?= e($google['merchant_feed_url'] ?? url('sitemap/urunler.xml')) ?>" style="flex:1" readonly>
          <button type="button" onclick="navigator.clipboard.writeText(this.previousElementSibling.value)" class="btn btn-outline btn-sm">Kopyala</button>
        </div>
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Bu URL'yi Merchant Center'a ekleyin</div>
      </div>
    </div>
  </div>

</div>

<!-- SAG -->
<div>

  <!-- Facebook Pixel -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Meta (Facebook) Pixel</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Pixel ID</label>
        <input type="text" name="pixel_id" value="<?= e($settings['pixel_id'] ?? '') ?>" placeholder="000000000000000">
      </div>
    </div>
  </div>

  <!-- TikTok Pixel -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">TikTok Pixel</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>TikTok Pixel ID</label>
        <input type="text" name="tiktok_pixel" value="<?= e($settings['tiktok_pixel'] ?? '') ?>" placeholder="XXXXXXXXXXXXXXXX">
      </div>
    </div>
  </div>

  <!-- Microsoft Clarity -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Microsoft Clarity</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Clarity Project ID</label>
        <input type="text" name="clarity_id" value="<?= e($settings['clarity_id'] ?? '') ?>" placeholder="xxxxxxxxxx">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Ücretsiz ısı haritası ve oturum kaydı</div>
      </div>
    </div>
  </div>

  <!-- Durum -->
  <div class="card">
    <div class="card-header"><span class="card-title">Entegrasyon Durumu</span></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:8px">
        <?php
        $integrations = [
          ['label'=>'GA4',              'active'=>!empty($google['ga4_measurement_id'])],
          ['label'=>'GTM',              'active'=>!empty($settings['gtm_id'])],
          ['label'=>'Search Console',   'active'=>!empty($google['search_console_code'])],
          ['label'=>'Merchant Center',  'active'=>!empty($google['merchant_feed_url'])],
          ['label'=>'Facebook Pixel',   'active'=>!empty($settings['pixel_id'])],
          ['label'=>'TikTok Pixel',     'active'=>!empty($settings['tiktok_pixel'])],
          ['label'=>'Microsoft Clarity','active'=>!empty($settings['clarity_id'])],
        ];
        ?>
        <?php foreach ($integrations as $int): ?>
          <div style="display:flex;align-items:center;justify-content:space-between;font-size:13px">
            <span><?= $int['label'] ?></span>
            <span class="badge <?= $int['active']?'b-success':'b-gray' ?>">
              <?= $int['active']?'Aktif':'Kurulmadı' ?>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
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
