<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('siparisler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Siparişler
    </a>
    <span style="font-size:15px;font-weight:700">Sipariş #<?= e($order['order_no']) ?></span>
    <span class="badge b-<?= orderStatusColor($order['status']) ?>"><?= orderStatusLabel($order['status']) ?></span>
    <span class="badge <?= $order['payment_status']==='paid'?'b-success':'b-warning' ?>"><?= $order['payment_status']==='paid'?'Ödendi':'Ödeme Bekleniyor' ?></span>
  </div>
  <div style="display:flex;gap:8px">
    <a href="<?= adminUrl('siparisler/'.$order['id'].'/fatura') ?>" target="_blank" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"/></svg>
      Fatura İndir
    </a>
  </div>
</div>

<!-- Progress Bar -->
<?php
$steps = ['pending'=>'Bekleyen','processing'=>'İşlemde','shipped'=>'Kargoda','delivered'=>'Teslim Edildi'];
$stepKeys = array_keys($steps);
$currentIdx = array_search($order['status'], $stepKeys);
$currentIdx = $currentIdx === false ? 0 : $currentIdx;
if ($order['status'] === 'cancelled') { $currentIdx = -1; }
?>
<?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'refunded'): ?>
<div class="card" style="padding:20px;margin-bottom:16px">
  <div style="display:flex;align-items:center;justify-content:space-between;position:relative">
    <div style="position:absolute;top:14px;left:5%;right:5%;height:2px;background:#e5e7eb;z-index:0">
      <div style="height:100%;background:#2563eb;transition:width .4s;width:<?= $currentIdx>=0?min(100,round($currentIdx/(count($steps)-1)*100)):0 ?>%"></div>
    </div>
    <?php foreach ($steps as $key => $label):
      $idx = array_search($key, $stepKeys);
      $done   = $idx <= $currentIdx;
      $active = $idx === $currentIdx;
    ?>
    <div style="display:flex;flex-direction:column;align-items:center;z-index:1;flex:1">
      <div style="width:28px;height:28px;border-radius:50%;background:<?= $done?'#2563eb':'#fff' ?>;border:2px solid <?= $done?'#2563eb':'#e5e7eb' ?>;display:flex;align-items:center;justify-content:center;margin-bottom:6px;transition:all .3s">
        <?php if ($done && !$active): ?>
          <svg width="12" height="12" viewBox="0 0 20 20" fill="#fff"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        <?php elseif ($active): ?>
          <div style="width:8px;height:8px;border-radius:50%;background:#fff"></div>
        <?php else: ?>
          <div style="width:8px;height:8px;border-radius:50%;background:#e5e7eb"></div>
        <?php endif; ?>
      </div>
      <span style="font-size:11px;font-weight:<?= $active?'600':'400' ?>;color:<?= $active?'#2563eb':($done?'#374151':'#9ca3af') ?>;text-align:center">
        <?= $label ?>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php elseif ($order['status'] === 'cancelled'): ?>
<div style="background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:12px 16px;margin-bottom:16px;font-size:13px;color:#b91c1c;display:flex;align-items:center;gap:8px">
  <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
  Bu sipariş iptal edilmiştir.
</div>
<?php endif; ?>

<div style="display:grid;grid-template-columns:1fr 310px;gap:16px;align-items:start">

<!-- SOL -->
<div>

  <!-- Siparis Urunleri -->
  <div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Sipariş Ürünleri (<?= count($items) ?>)</span></div>
    <div style="padding:0">
      <?php foreach ($items as $i => $item): ?>
      <div style="display:flex;align-items:center;gap:12px;padding:12px 16px;<?= $i<count($items)-1?'border-bottom:1px solid #f9fafb':'' ?>">
        <!-- Gorsel -->
        <?php if ($item['product_image']): ?>
          <img src="<?= uploadUrl('products/'.$item['product_image']) ?>" alt="" style="width:50px;height:50px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;flex-shrink:0">
        <?php else: ?>
          <div style="width:50px;height:50px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="#d1d5db"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
          </div>
        <?php endif; ?>
        <!-- Bilgi -->
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:500;color:#1a1a1a"><?= e($item['product_name'] ?? 'Ürün Silinmiş') ?></div>
          <?php if (!empty($item['variation_info'])): ?>
            <div style="font-size:11px;color:#9ca3af;margin-top:1px"><?= e($item['variation_info']) ?></div>
          <?php endif; ?>
          <div style="font-size:11px;color:#9ca3af">SKU: <?= e($item['sku'] ?? '—') ?></div>
        </div>
        <!-- Adet x Fiyat -->
        <div style="text-align:right;flex-shrink:0">
          <div style="font-size:13px;color:#6b7280"><?= formatPriceTRY($item['price']) ?> × <?= $item['quantity'] ?></div>
          <div style="font-size:14px;font-weight:600;color:#1a1a1a"><?= formatPriceTRY($item['price'] * $item['quantity']) ?></div>
        </div>
      </div>
      <?php endforeach; ?>
      <!-- Toplam -->
      <div style="padding:12px 16px;border-top:1px solid #f3f4f6;background:#f9fafb">
        <div style="display:flex;flex-direction:column;gap:5px;max-width:260px;margin-left:auto">
          <div style="display:flex;justify-content:space-between;font-size:12px;color:#6b7280">
            <span>Ara Toplam</span><span><?= formatPriceTRY($order['subtotal'] ?? $order['total']) ?></span>
          </div>
          <?php if (!empty($order['discount']) && $order['discount'] > 0): ?>
          <div style="display:flex;justify-content:space-between;font-size:12px;color:#16a34a">
            <span>İndirim</span><span>-<?= formatPriceTRY($order['discount']) ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($order['shipping_cost'])): ?>
          <div style="display:flex;justify-content:space-between;font-size:12px;color:#6b7280">
            <span>Kargo</span><span><?= $order['shipping_cost']>0?formatPriceTRY($order['shipping_cost']):'Ücretsiz' ?></span>
          </div>
          <?php endif; ?>
          <div style="display:flex;justify-content:space-between;font-size:14px;font-weight:700;color:#1a1a1a;padding-top:6px;border-top:1px solid #e5e7eb;margin-top:2px">
            <span>Toplam</span><span><?= formatPriceTRY($order['total']) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Kargo Bilgileri -->
  <div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Kargo & Takip</span></div>
    <div class="card-body">
      <?php if ($order['tracking_code']): ?>
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:12px 14px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between">
          <div>
            <div style="font-size:11px;color:#6b7280;margin-bottom:2px">Kargo Takip</div>
            <div style="font-size:14px;font-weight:600;color:#15803d;font-family:monospace"><?= e($order['tracking_code']) ?></div>
            <?php if ($order['cargo_company']): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:1px"><?= e($order['cargo_company']) ?></div>
            <?php endif; ?>
          </div>
          <?php if ($order['tracking_url']): ?>
            <a href="<?= e($order['tracking_url']) ?>" target="_blank" class="btn btn-outline btn-sm">Takip Et →</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?= adminUrl('siparisler/'.$order['id'].'/kargo') ?>">
        <?= csrfField() ?>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px">
          <div class="form-group" style="margin-bottom:0">
            <label>Kargo Şirketi</label>
            <select name="cargo_company" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:100%">
              <option value="">Seçin...</option>
              <?php foreach (['Yurtiçi Kargo','Aras Kargo','MNG Kargo','PTT Kargo','Sürat Kargo','HepsiJet','Sendeo','DHL','UPS','FedEx'] as $c): ?>
                <option value="<?= $c ?>" <?= ($order['cargo_company']??'')===$c?'selected':'' ?>><?= $c ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Takip Kodu</label>
            <input type="text" name="tracking_code" value="<?= e($order['tracking_code'] ?? '') ?>" placeholder="Takip numarası" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:100%">
          </div>
        </div>
        <div class="form-group" style="margin-bottom:10px">
          <label>Takip URL</label>
          <input type="url" name="tracking_url" value="<?= e($order['tracking_url'] ?? '') ?>" placeholder="https://..." style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:100%">
        </div>
        <button type="submit" class="btn btn-primary btn-sm">Kargo Bilgilerini Kaydet</button>
      </form>
    </div>
  </div>

  <!-- Siparis Gecmisi -->
  <div class="card" style="margin-bottom:16px">
    <div class="card-header"><span class="card-title">Sipariş Geçmişi</span></div>
    <div class="card-body">
      <?php if (empty($statusHistory)): ?>
        <p style="font-size:12px;color:#9ca3af">Henüz geçmiş kaydı yok.</p>
      <?php else: ?>
        <div style="position:relative;padding-left:20px">
          <div style="position:absolute;left:7px;top:4px;bottom:4px;width:2px;background:#f3f4f6"></div>
          <?php foreach ($statusHistory as $h): ?>
          <div style="position:relative;margin-bottom:14px">
            <div style="position:absolute;left:-16px;top:3px;width:10px;height:10px;border-radius:50%;background:#2563eb;border:2px solid #fff"></div>
            <div style="font-size:12px;font-weight:600;color:#374151"><?= orderStatusLabel($h['status']) ?></div>
            <?php if ($h['note']): ?>
              <div style="font-size:12px;color:#6b7280;margin-top:1px"><?= e($h['note']) ?></div>
            <?php endif; ?>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px"><?= formatDate($h['created_at'], 'd M Y H:i') ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>
<!-- /SOL -->

<!-- SAG KOLON -->
<div style="display:flex;flex-direction:column;gap:12px">

  <!-- Durum Guncelle -->
  <div class="card">
    <div class="card-header"><span class="card-title">Durum Güncelle</span></div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('siparisler/'.$order['id'].'/durum') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label>Yeni Durum</label>
          <select name="status" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:100%">
            <?php foreach(['pending'=>'Bekleyen','processing'=>'İşlemde','shipped'=>'Kargoda','delivered'=>'Teslim Edildi','cancelled'=>'İptal','refunded'=>'İade'] as $v=>$l): ?>
              <option value="<?= $v ?>" <?= $order['status']===$v?'selected':'' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:10px">
          <label>Not (opsiyonel)</label>
          <textarea name="note" rows="2" placeholder="Müşteriye görünmeyecek iç not..." style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:100%;resize:vertical"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Güncelle</button>
      </form>
    </div>
  </div>

  <!-- Musteri -->
  <div class="card">
    <div class="card-header"><span class="card-title">Müşteri</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:center;gap:10px;margin-bottom:12px">
        <div style="width:36px;height:36px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#2563eb;flex-shrink:0">
          <?= strtoupper(substr($order['customer_name'],0,1)) ?>
        </div>
        <div>
          <div style="font-size:13px;font-weight:500"><?= e($order['customer_name']) ?></div>
          <div style="font-size:12px;color:#6b7280"><?= e($order['customer_email']) ?></div>
        </div>
      </div>
      <?php if ($order['user_id']): ?>
        <a href="<?= adminUrl('musteriler/'.$order['user_id']) ?>" class="btn btn-outline btn-sm" style="width:100%;justify-content:center">Profili Görüntüle</a>
      <?php else: ?>
        <div style="font-size:11px;color:#9ca3af;text-align:center">Misafir Alışveriş</div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Teslimat Adresi -->
  <div class="card">
    <div class="card-header"><span class="card-title">Teslimat Adresi</span></div>
    <div class="card-body">
      <div style="font-size:13px;line-height:1.7;color:#374151">
        <div style="font-weight:500"><?= e(($shippingAddr['first_name']??'').' '.($shippingAddr['last_name']??'')) ?></div>
        <?php if (!empty($shippingAddr['phone'])): ?>
          <div><?= e($shippingAddr['phone']) ?></div>
        <?php endif; ?>
        <div><?= e($shippingAddr['address'] ?? '') ?></div>
        <?php if (!empty($shippingAddr['address2'])): ?>
          <div><?= e($shippingAddr['address2']) ?></div>
        <?php endif; ?>
        <div><?= e(($shippingAddr['district']??'').' '.($shippingAddr['city']??'')) ?></div>
        <?php if (!empty($shippingAddr['country'])): ?>
          <div><?= e($shippingAddr['country']) ?></div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Fatura Adresi -->
  <?php if (!empty($billingAddr) && $billingAddr !== $shippingAddr): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">Fatura Adresi</span></div>
    <div class="card-body">
      <div style="font-size:13px;line-height:1.7;color:#374151">
        <?php if (!empty($billingAddr['company'])): ?>
          <div style="font-weight:500"><?= e($billingAddr['company']) ?></div>
          <?php if (!empty($billingAddr['tax_no'])): ?>
            <div style="font-size:11px;color:#9ca3af">VKN: <?= e($billingAddr['tax_no']) ?> — <?= e($billingAddr['tax_office']??'') ?></div>
          <?php endif; ?>
        <?php else: ?>
          <div style="font-weight:500"><?= e(($billingAddr['first_name']??'').' '.($billingAddr['last_name']??'')) ?></div>
        <?php endif; ?>
        <div><?= e($billingAddr['address'] ?? '') ?></div>
        <div><?= e(($billingAddr['district']??'').' '.($billingAddr['city']??'')) ?></div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Odeme Bilgisi -->
  <div class="card">
    <div class="card-header"><span class="card-title">Ödeme</span></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:7px;font-size:13px">
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Yöntem</span>
          <span><?= e($order['payment_method'] ?? '—') ?></span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Durum</span>
          <span class="badge <?= $order['payment_status']==='paid'?'b-success':'b-warning' ?>">
            <?= $order['payment_status']==='paid'?'Ödendi':'Bekliyor' ?>
          </span>
        </div>
        <?php if ($order['payment_ref']): ?>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Referans</span>
          <span style="font-family:monospace;font-size:11px"><?= e($order['payment_ref']) ?></span>
        </div>
        <?php endif; ?>
        <div style="display:flex;justify-content:space-between;font-weight:600">
          <span>Toplam</span>
          <span><?= formatPriceTRY($order['total']) ?></span>
        </div>
      </div>
    </div>
  </div>

  <!-- Faturalar -->
  <?php if (!empty($invoices)): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">Faturalar</span></div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
      <?php foreach ($invoices as $inv): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px;background:#f9fafb;border-radius:7px">
          <div>
            <div style="font-size:12px;font-weight:500"><?= e($inv['invoice_no']) ?></div>
            <div style="font-size:11px;color:#9ca3af"><?= formatDate($inv['created_at']) ?></div>
          </div>
          <a href="<?= adminUrl('faturalar/'.$inv['id'].'/indir') ?>" class="btn btn-outline btn-sm">İndir</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
<!-- /SAG -->

</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
.form-group{margin-bottom:12px}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 310px"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
