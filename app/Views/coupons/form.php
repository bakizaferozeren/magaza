<?php ob_start();
$isEdit  = !empty($coupon);
$formUrl = $isEdit ? adminUrl('kuponlar/'.$coupon['id'].'/duzenle') : adminUrl('kuponlar/ekle');
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('kuponlar') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Kuponlar
    </a>
    <span style="font-size:15px;font-weight:700"><?= $isEdit ? 'Kupon Düzenle' : 'Yeni Kupon' ?></span>
  </div>
  <button type="submit" form="couponForm" class="btn btn-primary btn-sm">Kaydet</button>
</div>

<form method="POST" action="<?= $formUrl ?>" id="couponForm">
<?= csrfField() ?>
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px">

<div>
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Kupon Bilgileri</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Kupon Kodu <span style="color:#dc2626">*</span></label>
        <div style="display:flex;gap:8px">
          <input type="text" name="code" id="couponCode" value="<?= e($coupon['code'] ?? '') ?>"
            placeholder="YENI10" style="text-transform:uppercase;font-family:monospace;font-weight:600;letter-spacing:.05em;flex:1"
            <?= $isEdit ? 'readonly style="background:#f9fafb;text-transform:uppercase;font-family:monospace;font-weight:600;letter-spacing:.05em;flex:1"' : '' ?>>
          <?php if (!$isEdit): ?>
            <button type="button" onclick="generateCode()" class="btn btn-outline btn-sm">Otomatik</button>
          <?php endif; ?>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label>İndirim Tipi</label>
          <select name="type" id="couponType" onchange="updateValueLabel()">
            <option value="percent" <?= ($coupon['type']??'percent')==='percent'?'selected':'' ?>>Yüzde (%)</option>
            <option value="fixed"   <?= ($coupon['type']??'')==='fixed'  ?'selected':'' ?>>Sabit Tutar (₺)</option>
          </select>
        </div>
        <div class="form-group">
          <label id="valueLabel">İndirim Değeri (%)</label>
          <input type="number" name="value" value="<?= e($coupon['value'] ?? 10) ?>" min="0" step="0.01" required>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group">
          <label>Minimum Sipariş Tutarı (₺)</label>
          <input type="number" name="min_order" value="<?= e($coupon['min_order'] ?? 0) ?>" min="0" step="0.01">
          <div style="font-size:11px;color:#9ca3af;margin-top:2px">0 = limit yok</div>
        </div>
        <div class="form-group">
          <label>Maksimum İndirim (₺)</label>
          <input type="number" name="max_discount" value="<?= e($coupon['max_discount'] ?? '') ?>" min="0" step="0.01" placeholder="Sınırsız">
          <div style="font-size:11px;color:#9ca3af;margin-top:2px">Yüzde tipinde tavan</div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Kullanım Limitleri</span></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
        <div class="form-group" style="margin-bottom:0">
          <label>Toplam Kullanım Limiti</label>
          <input type="number" name="usage_limit" value="<?= e($coupon['usage_limit'] ?? '') ?>" min="1" placeholder="Sınırsız">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Kullanıcı Başına Limit</label>
          <input type="number" name="usage_per_user" value="<?= e($coupon['usage_per_user'] ?? '') ?>" min="1" placeholder="Sınırsız">
        </div>
      </div>
    </div>
  </div>
</div>

<div style="display:flex;flex-direction:column;gap:12px">
  <div class="card">
    <div class="card-body">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        <?= $isEdit ? 'Değişiklikleri Kaydet' : 'Kuponu Oluştur' ?>
      </button>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Geçerlilik</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Son Kullanma Tarihi</label>
        <input type="date" name="expires_at" value="<?= e($coupon['expires_at'] ? date('Y-m-d', strtotime($coupon['expires_at'])) : '') ?>">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Boş = süresiz</div>
      </div>
      <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;margin-bottom:0;font-weight:400">
        Aktif
        <input type="checkbox" name="is_active" value="1" <?= !isset($coupon)||$coupon['is_active']?'checked':'' ?> style="width:14px;height:14px;accent-color:#2563eb">
      </label>
    </div>
  </div>

  <?php if ($isEdit): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">İstatistik</span></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:8px;font-size:13px">
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Kullanım Sayısı</span>
          <span style="font-weight:600"><?= $coupon['usage_count'] ?></span>
        </div>
        <?php if ($coupon['usage_limit']): ?>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Kalan Kullanım</span>
          <span style="font-weight:600"><?= max(0, $coupon['usage_limit'] - $coupon['usage_count']) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

</div>
</form>

<script>
function generateCode() {
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  let code = '';
  for (let i = 0; i < 8; i++) code += chars[Math.floor(Math.random()*chars.length)];
  document.getElementById('couponCode').value = code;
}

function updateValueLabel() {
  const type = document.getElementById('couponType').value;
  document.getElementById('valueLabel').textContent = type === 'percent' ? 'İndirim Değeri (%)' : 'İndirim Tutarı (₺)';
}
updateValueLabel();
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}.form-group:last-child{margin-bottom:0}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
