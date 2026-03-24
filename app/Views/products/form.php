<?php
use App\Core\Session;
ob_start();
$isEdit     = !empty($product);
$formUrl    = $isEdit ? adminUrl('urunler/'.$product['id'].'/duzenle') : adminUrl('urunler/ekle');
$activeLang = Session::get('admin_lang', 'tr');
$csrfToken  = Session::csrfToken();
$productId  = $product['id'] ?? 0;
$slugValue  = e($product['slug'] ?? '');
$isNewProd  = $isEdit ? 'true' : 'false';
?>
<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('urunler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Ürünler
    </a>
    <span style="font-size:15px;font-weight:700"><?= $isEdit ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' ?></span>
    <span style="display:inline-flex;padding:2px 8px;border-radius:10px;font-size:11px;font-weight:500;background:<?= ($isEdit && !empty($product['is_active'])) ? '#f0fdf4' : '#fffbeb' ?>;color:<?= ($isEdit && !empty($product['is_active'])) ? '#15803d' : '#b45309' ?>">
      <?= ($isEdit && !empty($product['is_active'])) ? 'Aktif' : 'Taslak' ?>
    </span>
  </div>
  <div style="display:flex;gap:8px">
    <?php if ($isEdit): ?>
      <a href="<?= url('urun/'.$product['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">Önizle →</a>
    <?php endif; ?>
    <button type="submit" form="productForm" class="btn btn-primary btn-sm">
      <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
      <?= $isEdit ? 'Kaydet' : 'Ürünü Kaydet' ?>
    </button>
  </div>
</div>

<form method="POST" action="<?= $formUrl ?>" enctype="multipart/form-data" id="productForm">
<?= csrfField() ?>
<div style="display:grid;grid-template-columns:1fr 290px;gap:16px;align-items:start">

<!-- SOL -->
<div>

  <!-- Baslik -->
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
            <label>Ürün Başlığı <span style="color:#dc2626">*</span> <span style="font-weight:400;color:#9ca3af;font-size:11px">(<?= strtoupper($lang['code']) ?>)</span></label>
            <input type="text" id="nameInput_<?= $lang['code'] ?>" name="name_<?= $lang['code'] ?>"
              value="<?= e($translations[$lang['code']]['name'] ?? '') ?>"
              placeholder="Ürün adını girin..." style="font-size:14px"
              oninput="onTitleInput(this.value,'<?= $lang['code'] ?>')">
          </div>
          <div class="form-group">
            <label>Kısa Açıklama <span style="font-weight:400;color:#9ca3af;font-size:11px">(<?= strtoupper($lang['code']) ?>)</span></label>
            <textarea name="short_desc_<?= $lang['code'] ?>" rows="2" placeholder="Kısa ürün açıklaması..."><?= e($translations[$lang['code']]['short_desc'] ?? '') ?></textarea>
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>Uzun Açıklama <span style="font-weight:400;color:#9ca3af;font-size:11px">(<?= strtoupper($lang['code']) ?>)</span></label>
            <textarea name="long_desc_<?= $lang['code'] ?>" rows="8" placeholder="Detaylı ürün açıklaması..."><?= htmlspecialchars_decode(e($translations[$lang['code']]['long_desc'] ?? '')) ?></textarea>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- URL -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header">
      <span class="card-title">URL (Slug)</span>
      <button type="button" onclick="checkSlug()" class="btn btn-outline btn-sm">Kontrol Et</button>
    </div>
    <div class="card-body">
      <div style="display:flex;align-items:center;border:1px solid #e5e7eb;border-radius:7px;overflow:hidden">
        <span style="padding:7px 10px;background:#f9fafb;border-right:1px solid #e5e7eb;font-size:12px;color:#9ca3af;white-space:nowrap;flex-shrink:0"><?= url('urun/') ?></span>
        <input type="text" name="slug" id="slugInput" value="<?= $slugValue ?>" placeholder="urun-adi"
          style="border:none;box-shadow:none;border-radius:0;flex:1;font-family:monospace;font-size:12px;padding:7px 10px;outline:none"
          oninput="onSlugManualEdit(this.value)">
      </div>
      <div id="slugStatus" style="display:none;margin-top:5px;padding:5px 8px;border-radius:6px;font-size:11px"></div>
      <div style="margin-top:8px;padding:8px 10px;background:#f9fafb;border-radius:7px">
        <div style="font-size:10px;color:#9ca3af;text-transform:uppercase;letter-spacing:.04em;margin-bottom:2px">Tam URL</div>
        <div id="slugFullUrl" style="font-size:12px;color:#1a7f37;word-break:break-all"><?= url('urun/'.($product['slug'] ?? '')) ?></div>
      </div>
      <div style="font-size:11px;color:#9ca3af;margin-top:5px">Başlıktan otomatik oluşturulur — düzenleyebilirsiniz</div>
    </div>
  </div>

  <!-- Gorseller -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Görseller & Galeri</span></div>
    <div class="card-body">
      <div id="imgGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-bottom:10px">
        <?php foreach ($images ?? [] as $img): ?>
          <div class="img-item" data-id="<?= $img['id'] ?>" style="aspect-ratio:1;border-radius:8px;border:1px solid #e5e7eb;overflow:hidden;position:relative">
            <img src="<?= uploadUrl('products/'.$img['path']) ?>" style="width:100%;height:100%;object-fit:cover">
            <?php if ($img['is_cover']): ?>
              <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(37,99,235,.85);color:#fff;font-size:9px;text-align:center;padding:2px;font-weight:600">KAPAK</div>
            <?php endif; ?>
            <button type="button" onclick="deleteImage(<?= $img['id'] ?>,this)" style="position:absolute;top:3px;right:3px;width:18px;height:18px;background:rgba(0,0,0,.5);border:none;border-radius:50%;color:#fff;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center">×</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div id="previewGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(80px,1fr));gap:8px;margin-bottom:8px"></div>
      <div id="dropzone"
        style="border:2px dashed #e5e7eb;border-radius:8px;padding:24px;text-align:center;cursor:pointer;transition:border-color .15s,background .15s"
        onclick="document.getElementById('imageInput').click()"
        ondragover="event.preventDefault();this.style.borderColor='#2563eb';this.style.background='#eff6ff'"
        ondragleave="this.style.borderColor='#e5e7eb';this.style.background=''"
        ondrop="event.preventDefault();this.style.borderColor='#e5e7eb';this.style.background='';dropImages(event)"
        onmouseover="this.style.borderColor='#2563eb'"
        onmouseout="this.style.borderColor='#e5e7eb'">
        <svg width="32" height="32" viewBox="0 0 20 20" fill="#9ca3af" style="display:block;margin:0 auto 8px"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
        <div style="font-size:13px;font-weight:500;color:#374151">Sürükle & Bırak veya Tıkla</div>
        <div style="font-size:12px;color:#9ca3af;margin-top:3px">JPG, PNG, WebP — max 5MB</div>
      </div>
      <input type="file" name="images[]" multiple accept="image/*" id="imageInput" style="display:none" onchange="previewImages(this)">
    </div>
  </div>

  <!-- Fiyat Stok -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Fiyat & Stok</span></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-bottom:12px">
        <div class="form-group">
          <label>Normal Fiyat (₺) <span style="color:#dc2626">*</span></label>
          <input type="number" name="price" id="priceInput" value="<?= e($product['price'] ?? '') ?>" placeholder="0.00" step="0.01" min="0" oninput="checkPrices()">
        </div>
        <div class="form-group">
          <label>İndirimli Fiyat (₺)</label>
          <input type="number" name="sale_price" id="salePriceInput" value="<?= e($product['sale_price'] ?? '') ?>" placeholder="Boş = indirim yok" step="0.01" min="0" oninput="checkPrices()">
          <div id="priceMsg" style="font-size:11px;margin-top:3px"></div>
        </div>
        <div class="form-group">
          <label>Vergi Oranı</label>
          <select name="tax_rate">
            <option value="20" <?= ($product['tax_rate']??20)==20?'selected':'' ?>>%20 KDV</option>
            <option value="10" <?= ($product['tax_rate']??20)==10?'selected':'' ?>>%10 KDV</option>
            <option value="1"  <?= ($product['tax_rate']??20)==1 ?'selected':'' ?>>%1 KDV</option>
          </select>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr;gap:12px">
        <div class="form-group" style="margin-bottom:0"><label>SKU</label><input type="text" name="sku" value="<?= e($product['sku']??'') ?>" placeholder="PROD-001"></div>
        <div class="form-group" style="margin-bottom:0"><label>Barkod</label><input type="text" name="barcode" value="<?= e($product['barcode']??'') ?>" placeholder="8680000000000"></div>
        <div class="form-group" style="margin-bottom:0">
          <label>Stok Durumu</label>
          <select name="stock_status" id="stockStatus" onchange="toggleStockQty()">
            <option value="in_stock"    <?= ($product['stock_status']??'in_stock')==='in_stock'    ?'selected':'' ?>>Stokta Var</option>
            <option value="out_of_stock"<?= ($product['stock_status']??'')==='out_of_stock'?'selected':'' ?>>Stokta Yok</option>
            <option value="pre_order"   <?= ($product['stock_status']??'')==='pre_order'   ?'selected':'' ?>>Ön Sipariş</option>
            <option value="coming_soon" <?= ($product['stock_status']??'')==='coming_soon' ?'selected':'' ?>>Yakında</option>
          </select>
        </div>
        <div class="form-group" style="margin-bottom:0" id="stockQtyWrap">
          <label>Stok Miktarı</label>
          <input type="number" name="stock" value="<?= e($product['stock']??0) ?>" min="0">
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;margin-top:12px">
        <div class="form-group" style="margin-bottom:0">
          <label>Stok Uyarı Eşiği</label>
          <input type="number" name="stock_alert_qty" value="<?= e($product['stock_alert_qty']??'') ?>" placeholder="Örn: 5" min="0">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Ürün Başına Limit</label>
          <input type="number" name="order_limit_per_product" value="<?= e($product['order_limit_per_product']??'') ?>" placeholder="Sınırsız" min="1">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Müşteri Başına Limit</label>
          <input type="number" name="order_limit_per_customer" value="<?= e($product['order_limit_per_customer']??'') ?>" placeholder="Sınırsız" min="1">
        </div>
      </div>
    </div>
  </div>

  <!-- Teknik Ozellikler -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header">
      <span class="card-title">Teknik Özellikler</span>
      <button type="button" onclick="addAttr()" class="btn btn-outline btn-sm">+ Özellik Ekle</button>
    </div>
    <div class="card-body">
      <div id="attrList">
        <?php foreach ($attributes??[] as $attr): ?>
          <div class="attr-row" style="display:grid;grid-template-columns:1fr 1fr 26px;gap:8px;margin-bottom:8px">
            <input type="text" name="attr_name[]" value="<?= e($attr['attr_name']) ?>" placeholder="Özellik (Örn: RAM)">
            <input type="text" name="attr_value[]" value="<?= e($attr['attr_value']) ?>" placeholder="Değer (Örn: 16 GB)">
            <button type="button" onclick="this.closest('.attr-row').remove()" style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;cursor:pointer;width:26px;height:36px;font-size:16px;display:flex;align-items:center;justify-content:center">×</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="font-size:11px;color:#9ca3af;margin-top:4px">Filtreler bu özelliklerden otomatik oluşturulur</div>
    </div>
  </div>

  <!-- Varyasyonlar -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header">
      <span class="card-title">Varyasyonlar</span>
      <label style="display:flex;align-items:center;gap:6px;cursor:pointer;margin-bottom:0;font-size:12px;font-weight:400">
        <div id="varToggle" onclick="toggleVar()" style="width:36px;height:20px;border-radius:10px;background:<?= !empty($product['has_variations'])?'#2563eb':'#e5e7eb' ?>;position:relative;cursor:pointer;transition:background .2s;flex-shrink:0">
          <div id="varThumb" style="position:absolute;top:2px;left:<?= !empty($product['has_variations'])?'18px':'2px' ?>;width:16px;height:16px;border-radius:50%;background:#fff;transition:left .2s"></div>
        </div>
        <input type="hidden" name="has_variations" id="hasVariations" value="<?= !empty($product['has_variations'])?'1':'0' ?>">
        Varyasyonlu ürün
      </label>
    </div>
    <div class="card-body" id="varBody" style="display:<?= !empty($product['has_variations'])?'block':'none' ?>">
      <div style="padding:10px 12px;background:#f0f9ff;border-radius:8px;font-size:12px;color:#0369a1;margin-bottom:12px;display:flex;align-items:center;gap:6px">
        <svg width="14" height="14" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
        Her varyasyonun kendi görseli, fiyatı ve stoğu olabilir.
      </div>
      <div style="display:grid;grid-template-columns:56px 1fr 100px 80px 80px 28px;gap:8px;padding:0 0 6px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em">
        <div>Görsel</div><div>Renk / Ad</div><div>Fiyat (₺)</div><div>Stok</div><div>SKU</div><div></div>
      </div>
      <div id="varRows">
        <?php foreach ($variations??[] as $var):
          $varLabel = implode(' / ', array_column($var['options']??[], 'option_name'));
        ?>
          <div class="var-row" style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;margin-bottom:8px">
            <div style="display:grid;grid-template-columns:56px 1fr 100px 80px 80px 28px;gap:8px;align-items:center">
              <label style="display:flex;align-items:center;justify-content:center;width:52px;height:52px;border:1.5px dashed #d1d5db;border-radius:7px;cursor:pointer;overflow:hidden;background:#fff">
                <div id="varImg_<?= $var['id'] ?>" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%">
                  <?php if (!empty($var['image_path'])): ?>
                    <img src="<?= uploadUrl('products/'.$var['image_path']) ?>" style="width:100%;height:100%;object-fit:cover">
                  <?php else: ?>
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="#d1d5db"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                  <?php endif; ?>
                </div>
                <input type="file" name="variation_image[<?= $var['id'] ?>]" accept="image/*" style="display:none" onchange="previewVarImage(this,'varImg_<?= $var['id'] ?>')">
              </label>
              <div style="display:flex;align-items:center;gap:6px">
                <input type="color" value="#888780" style="width:24px;height:24px;border:none;cursor:pointer;border-radius:50%;padding:0;flex-shrink:0" onchange="this.nextElementSibling.style.background=this.value">
                <div style="width:16px;height:16px;border-radius:50%;background:#888780;border:2px solid rgba(0,0,0,.1);flex-shrink:0"></div>
                <input type="text" name="variation[<?= $var['id'] ?>][label]" value="<?= e($varLabel) ?>" placeholder="Renk adı">
              </div>
              <input type="number" name="variation[<?= $var['id'] ?>][price]" value="<?= e($var['price']??'') ?>" placeholder="Ana fiyat" step="0.01">
              <input type="number" name="variation[<?= $var['id'] ?>][stock]" value="<?= e($var['stock']??0) ?>" min="0">
              <input type="text" name="variation[<?= $var['id'] ?>][sku]" value="<?= e($var['sku']??'') ?>" placeholder="SKU">
              <button type="button" onclick="this.closest('.var-row').remove()" style="width:26px;height:26px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center">×</button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;gap:8px;margin-top:4px">
        <button type="button" onclick="addVar()" class="btn btn-outline btn-sm">+ Varyasyon Ekle</button>
        <button type="button" onclick="quickColors()" class="btn btn-outline btn-sm" style="color:#7c3aed;border-color:#e9d5ff">⚡ Hızlı Renk Ekle</button>
      </div>
    </div>
  </div>

  <!-- Video -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Video</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>YouTube / Vimeo URL</label>
        <input type="url" name="video_url" value="<?= e($product['video_url']??'') ?>" placeholder="https://youtube.com/watch?v=...">
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>veya Video Yükle</label>
        <input type="file" name="video_file" accept="video/*" style="padding:6px;font-size:13px">
      </div>
    </div>
  </div>

  <!-- Baglantili Urunler -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Bağlantılı Ürünler</span></div>
    <div class="card-body">
      <div class="form-group" style="margin-bottom:0">
        <label>Çapraz Satış Ürünleri</label>
        <input type="text" name="cross_sell_ids" placeholder="Ürün ID'leri virgülle ayırın: 1, 2, 3">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Sepete ekleyince "Bununla birlikte al" olarak gösterilir</div>
      </div>
    </div>
  </div>

  <!-- SEO -->
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">SEO Analizi</span></div>
    <div class="card-body">
      <?php if (count($languages) > 1): ?>
      <div style="display:flex;border-bottom:1px solid #f3f4f6;margin:-16px -16px 16px">
        <?php foreach ($languages as $lang): ?>
          <button type="button" class="seo-lang-tab" data-lang="<?= $lang['code'] ?>"
            onclick="switchSeoLang('<?= $lang['code'] ?>')"
            style="padding:8px 14px;font-size:12px;font-weight:500;border:none;background:none;cursor:pointer;color:<?= $lang['code']===$activeLang?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $lang['code']===$activeLang?'#2563eb':'transparent' ?>;margin-bottom:-1px">
            <?= strtoupper($lang['code']) ?>
          </button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php foreach ($languages as $lang): ?>
      <div class="seo-lang-panel" data-lang="<?= $lang['code'] ?>" style="display:<?= $lang['code']===$activeLang?'block':'none' ?>">
        <div class="form-group">
          <label>Odak Anahtar Kelime <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
          <input type="text" id="kw_<?= $lang['code'] ?>" name="meta_keywords_<?= $lang['code'] ?>"
            value="<?= e($translations[$lang['code']]['meta_keywords']??'') ?>"
            placeholder="Örn: bluetooth kulaklık" oninput="runSeo('<?= $lang['code'] ?>')">
        </div>
        <div id="seoScore_<?= $lang['code'] ?>" style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#f9fafb;border-radius:8px;margin-bottom:12px">
          <div id="seoCircle_<?= $lang['code'] ?>" style="width:40px;height:40px;border-radius:50%;background:#9ca3af;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0">—</div>
          <div>
            <div id="seoLabel_<?= $lang['code'] ?>" style="font-size:12px;font-weight:600;color:#6b7280">Analiz bekleniyor</div>
            <div id="seoDesc_<?= $lang['code'] ?>" style="font-size:11px;color:#9ca3af;margin-top:1px">Anahtar kelime girerek başlayın</div>
          </div>
        </div>
        <div style="background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:12px;margin-bottom:12px">
          <div style="font-size:10px;color:#9ca3af;font-weight:500;text-transform:uppercase;letter-spacing:.04em;margin-bottom:6px">Google Önizleme</div>
          <div style="font-size:11px;color:#1a7f37;margin-bottom:2px"><?= url('urun/') ?><span id="prevSlug_<?= $lang['code'] ?>"><?= e($product['slug']??'') ?></span></div>
          <div id="prevTitle_<?= $lang['code'] ?>" style="font-size:16px;color:#1a0dab;margin-bottom:3px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($translations[$lang['code']]['meta_title']??($translations[$lang['code']]['name']??'Sayfa başlığı')) ?></div>
          <div id="prevDesc_<?= $lang['code'] ?>" style="font-size:12px;color:#4d5156;line-height:1.5"><?= e($translations[$lang['code']]['meta_desc']??'Meta açıklaması burada görünecek...') ?></div>
        </div>
        <div class="form-group">
          <label>SEO Başlığı <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
          <input type="text" id="mt_<?= $lang['code'] ?>" name="meta_title_<?= $lang['code'] ?>"
            value="<?= e($translations[$lang['code']]['meta_title']??'') ?>"
            placeholder="SEO başlığı..." maxlength="70"
            oninput="onMetaTitleInput(this,'<?= $lang['code'] ?>')">
          <div style="height:2px;border-radius:2px;margin-top:4px;background:#f3f4f6"><div id="tBar_<?= $lang['code'] ?>" style="height:100%;border-radius:2px;width:0%;background:#9ca3af;transition:all .3s"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:11px;margin-top:2px">
            <span id="tHint_<?= $lang['code'] ?>" style="color:#9ca3af">30–60 karakter önerilir</span>
            <span id="tCount_<?= $lang['code'] ?>" style="color:#9ca3af">0/60</span>
          </div>
        </div>
        <div class="form-group">
          <label>Meta Açıklama <span style="font-size:11px;color:#9ca3af">(<?= strtoupper($lang['code']) ?>)</span></label>
          <textarea id="md_<?= $lang['code'] ?>" name="meta_desc_<?= $lang['code'] ?>"
            rows="3" placeholder="SEO açıklaması..." maxlength="170"
            oninput="onMetaDescInput(this,'<?= $lang['code'] ?>')"><?= e($translations[$lang['code']]['meta_desc']??'') ?></textarea>
          <div style="height:2px;border-radius:2px;margin-top:4px;background:#f3f4f6"><div id="dBar_<?= $lang['code'] ?>" style="height:100%;border-radius:2px;width:0%;background:#9ca3af;transition:all .3s"></div></div>
          <div style="display:flex;justify-content:space-between;font-size:11px;margin-top:2px">
            <span id="dHint_<?= $lang['code'] ?>" style="color:#9ca3af">120–160 karakter önerilir</span>
            <span id="dCount_<?= $lang['code'] ?>" style="color:#9ca3af">0/160</span>
          </div>
        </div>
        <div id="seoChecks_<?= $lang['code'] ?>" style="display:flex;flex-direction:column;gap:4px"></div>
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid #f3f4f6">
          <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;margin-bottom:8px">OG / Sosyal Medya</div>
          <div class="form-group">
            <label>OG Başlık</label>
            <input type="text" name="og_title_<?= $lang['code'] ?>" value="<?= e($translations[$lang['code']]['og_title']??'') ?>" placeholder="Boş = SEO başlığı kullanılır">
          </div>
          <div class="form-group" style="margin-bottom:0">
            <label>OG Açıklama</label>
            <textarea name="og_desc_<?= $lang['code'] ?>" rows="2" placeholder="Boş = meta açıklama kullanılır"><?= e($translations[$lang['code']]['og_desc']??'') ?></textarea>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>
<!-- /SOL -->

<!-- SAG KOLON -->
<div style="position:sticky;top:72px;display:flex;flex-direction:column;gap:12px">

  <div class="card">
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">
        <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
        <?= $isEdit ? 'Değişiklikleri Kaydet' : 'Ürünü Kaydet' ?>
      </button>
      <?php if ($isEdit): ?>
        <a href="<?= url('urun/'.$product['slug']) ?>" target="_blank" class="btn btn-outline" style="width:100%;justify-content:center;font-size:12px">Ürün Sayfasını Gör →</a>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Durum</span></div>
    <div class="card-body">
      <div style="display:flex;align-items:center;justify-content:space-between">
        <div>
          <div style="font-size:13px;font-weight:500">Aktif</div>
          <div style="font-size:11px;color:#9ca3af;margin-top:1px">Mağazada görünür</div>
        </div>
        <div id="activeTrack" onclick="toggleActive()" style="width:38px;height:20px;border-radius:10px;background:<?= (!isset($product)||$product['is_active'])?'#2563eb':'#e5e7eb' ?>;position:relative;cursor:pointer;transition:background .2s;flex-shrink:0">
          <div id="activeThumb" style="position:absolute;top:2px;left:<?= (!isset($product)||$product['is_active'])?'20px':'2px' ?>;width:16px;height:16px;border-radius:50%;background:#fff;transition:left .2s"></div>
          <input type="checkbox" name="is_active" value="1" id="isActiveInput" <?= (!isset($product)||$product['is_active'])?'checked':'' ?> style="opacity:0;position:absolute;width:0;height:0">
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Organizasyon</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Kategori</label>
        <select name="category_id">
          <option value="">— Seçin —</option>
          <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($product['category_id']??'')==$cat['id']?'selected':'' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <a href="<?= adminUrl('kategoriler/ekle') ?>" target="_blank" style="font-size:11px;color:#2563eb;display:block;margin-top:4px">+ Yeni Kategori Ekle</a>
      </div>
      <div class="form-group" style="margin-bottom:0">
        <label>Marka</label>
        <select name="brand_id">
          <option value="">— Seçin —</option>
          <?php foreach ($brands as $brand): ?>
            <option value="<?= $brand['id'] ?>" <?= ($product['brand_id']??'')==$brand['id']?'selected':'' ?>><?= e($brand['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <a href="<?= adminUrl('markalar/ekle') ?>" target="_blank" style="font-size:11px;color:#2563eb;display:block;margin-top:4px">+ Yeni Marka Ekle</a>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Kargo</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>Kargo Türü</label>
        <select name="shipping_type" id="shippingType" onchange="toggleShipping()">
          <option value="domestic"      <?= ($product['shipping_type']??'domestic')==='domestic'      ?'selected':'' ?>>Yurtiçi</option>
          <option value="international" <?= ($product['shipping_type']??'')==='international'?'selected':'' ?>>Yurtdışı</option>
        </select>
      </div>
      <div id="domesticShipping" style="display:<?= ($product['shipping_type']??'domestic')==='domestic'?'block':'none' ?>">
        <div class="form-group" style="margin-bottom:0">
          <label>Teslimat Süresi</label>
          <select name="shipping_days_domestic">
            <option value="1" <?= ($product['shipping_days_min']??1)==1?'selected':'' ?>>1-2 İş Günü</option>
            <option value="0" <?= ($product['shipping_days_min']??1)==0?'selected':'' ?>>Ertesi Gün Kargo</option>
          </select>
        </div>
      </div>
      <div id="intlShipping" style="display:<?= ($product['shipping_type']??'')==='international'?'block':'none' ?>">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
          <div class="form-group" style="margin-bottom:0"><label>Min Gün</label><input type="number" name="shipping_days_min" value="<?= e($product['shipping_days_min']??7) ?>" min="1"></div>
          <div class="form-group" style="margin-bottom:0"><label>Max Gün</label><input type="number" name="shipping_days_max" value="<?= e($product['shipping_days_max']??14) ?>" min="1"></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><span class="card-title">Etiketler</span></div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
      <?php foreach ([['key'=>'is_featured','label'=>'Öne Çıkan'],['key'=>'is_best_seller','label'=>'En Çok Satan'],['key'=>'is_most_clicked','label'=>'En Çok Tıklanan'],['key'=>'is_recommended','label'=>'Önerilen']] as $lbl): ?>
        <label style="display:flex;align-items:center;justify-content:space-between;font-size:13px;cursor:pointer;margin-bottom:0;font-weight:400">
          <?= $lbl['label'] ?>
          <input type="checkbox" name="<?= $lbl['key'] ?>" value="1" <?= !empty($product[$lbl['key']])?'checked':'' ?> style="width:14px;height:14px;accent-color:#2563eb">
        </label>
      <?php endforeach; ?>
    </div>
  </div>

</div>
<!-- /SAG -->

</div>
</form>

<script>
(function() {
'use strict';

// ─── CONFIG ───────────────────────────────────────────────
var SLUG_EDITED   = <?= $isEdit ? 'true' : 'false' ?>;
var ADMIN_URL     = '<?= adminUrl('') ?>';
var STORE_URL     = '<?= url('urun/') ?>';
var CSRF          = '<?= $csrfToken ?>';
var PRODUCT_ID    = <?= (int)$productId ?>;

// ─── SLUG ─────────────────────────────────────────────────
function makeSlug(v) {
  return v.toLowerCase()
    .replace(/ş/g,'s').replace(/ı/g,'i').replace(/ğ/g,'g')
    .replace(/ü/g,'u').replace(/ö/g,'o').replace(/ç/g,'c')
    .replace(/Ş/g,'s').replace(/İ/g,'i').replace(/Ğ/g,'g')
    .replace(/Ü/g,'u').replace(/Ö/g,'o').replace(/Ç/g,'c')
    .replace(/[^a-z0-9\s]/g,'').trim()
    .replace(/\s+/g,'-').replace(/-+/g,'-').replace(/^-|-$/g,'');
}

function setSlug(slug) {
  var si = document.getElementById('slugInput');
  if (si) si.value = slug;
  updateSlugPreview(slug);
}

function updateSlugPreview(slug) {
  var fu = document.getElementById('slugFullUrl');
  if (fu) {
    fu.textContent = STORE_URL + (slug || '');
    fu.style.color = slug ? '#1a7f37' : '#9ca3af';
  }
  // SEO panellerindeki slug preview
  document.querySelectorAll('[id^="prevSlug_"]').forEach(function(el) {
    el.textContent = slug || '';
  });
}

// Global — HTML oninput="onTitleInput(...)" çağırır
window.onTitleInput = function(val, lang) {
  if (lang === 'tr' && !SLUG_EDITED) {
    setSlug(makeSlug(val));
  }
  var mt = document.getElementById('mt_' + lang);
  if (mt && !mt.dataset.edited) {
    mt.value = val;
    var pt = document.getElementById('prevTitle_' + lang);
    if (pt) pt.textContent = val || 'Sayfa başlığı';
    updateBar('tBar_' + lang, val.length, 30, 60, 'tCount_' + lang, 'tHint_' + lang);
    runSeo(lang);
  }
};

window.onSlugManualEdit = function(val) {
  SLUG_EDITED = true;
  updateSlugPreview(val);
  var st = document.getElementById('slugStatus');
  if (st) st.style.display = 'none';
};

// Slug blur
var si = document.getElementById('slugInput');
if (si) {
  si.addEventListener('blur', function() {
    var c = makeSlug(this.value);
    this.value = c;
    updateSlugPreview(c);
  });
}

window.checkSlug = function() {
  var si = document.getElementById('slugInput');
  var st = document.getElementById('slugStatus');
  if (!si || !st) return;

  var slug = makeSlug(si.value);
  si.value = slug;
  updateSlugPreview(slug);

  if (!slug) {
    st.style.cssText = 'display:block;padding:5px 8px;border-radius:6px;font-size:11px;background:#fef2f2;color:#b91c1c';
    st.textContent = 'Slug boş olamaz';
    return;
  }

  st.style.cssText = 'display:block;padding:5px 8px;border-radius:6px;font-size:11px;background:#f9fafb;color:#6b7280';
  st.textContent = 'Kontrol ediliyor...';

  fetch(ADMIN_URL + 'urunler/slug-kontrol', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: '_csrf_token=' + CSRF + '&slug=' + encodeURIComponent(slug) + '&product_id=' + PRODUCT_ID
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.exists) {
      si.value = d.slug;
      updateSlugPreview(d.slug);
      st.style.cssText = 'display:block;padding:5px 8px;border-radius:6px;font-size:11px;background:#fffbeb;color:#b45309';
      st.textContent = '⚠ Değiştirildi: ' + d.slug;
    } else {
      st.style.cssText = 'display:block;padding:5px 8px;border-radius:6px;font-size:11px;background:#f0fdf4;color:#15803d';
      st.textContent = '✓ Kullanılabilir';
      setTimeout(function() { st.style.display = 'none'; }, 2500);
    }
  }).catch(function() {
    st.style.cssText = 'display:block;padding:5px 8px;border-radius:6px;font-size:11px;background:#fef2f2;color:#b91c1c';
    st.textContent = 'Kontrol edilemedi';
  });
};

// ─── DIL SEKME ────────────────────────────────────────────
window.switchLang = function(code) {
  document.querySelectorAll('.lang-tab').forEach(function(t) {
    t.style.color = t.dataset.lang === code ? '#2563eb' : '#6b7280';
    t.style.borderBottom = t.dataset.lang === code ? '2px solid #2563eb' : '2px solid transparent';
  });
  document.querySelectorAll('.lang-panel').forEach(function(p) {
    p.style.display = p.dataset.lang === code ? 'block' : 'none';
  });
};

window.switchSeoLang = function(code) {
  document.querySelectorAll('.seo-lang-tab').forEach(function(t) {
    t.style.color = t.dataset.lang === code ? '#2563eb' : '#6b7280';
    t.style.borderBottom = t.dataset.lang === code ? '2px solid #2563eb' : '2px solid transparent';
  });
  document.querySelectorAll('.seo-lang-panel').forEach(function(p) {
    p.style.display = p.dataset.lang === code ? 'block' : 'none';
  });
};

// ─── SEO ──────────────────────────────────────────────────
function updateBar(barId, len, min, max, countId, hintId) {
  var fill = document.getElementById(barId);
  var cnt  = document.getElementById(countId);
  var hint = document.getElementById(hintId);
  if (!fill) return;
  var ok = len >= min && len <= max;
  fill.style.width      = Math.min(100, (len / max) * 100) + '%';
  fill.style.background = ok ? '#16a34a' : len > 0 ? '#d97706' : '#9ca3af';
  if (cnt) { cnt.textContent = len + '/' + max; cnt.style.color = len > max ? '#dc2626' : ok ? '#16a34a' : '#9ca3af'; }
  if (hint) { hint.textContent = ok ? '✓ İdeal uzunluk' : len < min ? 'Min ' + min + ' karakter önerilir' : 'Çok uzun'; hint.style.color = ok ? '#16a34a' : '#d97706'; }
}

window.onMetaTitleInput = function(el, lang) {
  el.dataset.edited = '1';
  var pt = document.getElementById('prevTitle_' + lang);
  if (pt) pt.textContent = el.value || 'Sayfa başlığı';
  updateBar('tBar_' + lang, el.value.length, 30, 60, 'tCount_' + lang, 'tHint_' + lang);
  runSeo(lang);
};

window.onMetaDescInput = function(el, lang) {
  var pd = document.getElementById('prevDesc_' + lang);
  if (pd) pd.textContent = el.value || 'Meta açıklaması...';
  updateBar('dBar_' + lang, el.value.length, 120, 160, 'dCount_' + lang, 'dHint_' + lang);
  runSeo(lang);
};

function runSeo(lang) {
  var kw    = (document.getElementById('kw_'  + lang) || {value:''}).value.trim().toLowerCase();
  var title = (document.getElementById('mt_'  + lang) || {value:''}).value.trim();
  var desc  = (document.getElementById('md_'  + lang) || {value:''}).value.trim();
  var slug  = (document.getElementById('slugInput')   || {value:''}).value.trim();
  var checks = document.getElementById('seoChecks_' + lang);
  if (!checks) return;

  if (!kw) {
    checks.innerHTML = '';
    var c = document.getElementById('seoCircle_'+lang);
    var l = document.getElementById('seoLabel_'+lang);
    var d = document.getElementById('seoDesc_'+lang);
    var w = document.getElementById('seoScore_'+lang);
    if (c) { c.textContent='—'; c.style.background='#9ca3af'; }
    if (l) { l.textContent='Analiz bekleniyor'; l.style.color='#6b7280'; }
    if (d) d.textContent = 'Anahtar kelime girerek başlayın';
    if (w) w.style.background = '#f9fafb';
    return;
  }

  var items = [
    { ok: title.toLowerCase().includes(kw),              msg: 'Anahtar kelime başlıkta '     + (title.toLowerCase().includes(kw) ? 'geçiyor' : 'geçmiyor') },
    { ok: desc.toLowerCase().includes(kw),               msg: 'Anahtar kelime açıklamada '   + (desc.toLowerCase().includes(kw)  ? 'geçiyor' : 'geçmiyor') },
    { ok: slug.includes(kw.replace(/\s+/g,'-')),         msg: 'Anahtar kelime URL\'de '      + (slug.includes(kw.replace(/\s+/g,'-')) ? 'geçiyor' : 'geçmiyor') },
    { ok: title.length>=30 && title.length<=60,          msg: title.length>=30&&title.length<=60 ? 'Başlık ideal uzunlukta' : title.length<30 ? 'Başlık çok kısa' : 'Başlık çok uzun' },
    { ok: desc.length>=120 && desc.length<=160,          msg: desc.length>=120&&desc.length<=160 ? 'Meta açıklama ideal' : desc.length<120 ? 'Meta açıklama çok kısa' : 'Meta açıklama çok uzun' },
    { ok: slug.length > 0,                               msg: slug.length > 0 ? 'URL slug mevcut' : 'URL slug eksik' },
  ];

  var passed = items.filter(function(i){return i.ok;}).length;
  var score  = Math.round((passed / items.length) * 100);
  var color  = score>=80 ? '#16a34a' : score>=50 ? '#d97706' : '#dc2626';
  var label  = score>=80 ? 'İyi SEO' : score>=50 ? 'Orta SEO' : 'Zayıf SEO';
  var bg     = score>=80 ? '#f0fdf4' : score>=50 ? '#fffbeb' : '#fef2f2';

  var cc=document.getElementById('seoCircle_'+lang), ll=document.getElementById('seoLabel_'+lang),
      dd=document.getElementById('seoDesc_'+lang),   ww=document.getElementById('seoScore_'+lang);
  if (cc) { cc.textContent=score; cc.style.background=color; }
  if (ll) { ll.textContent=label; ll.style.color=color; }
  if (dd) dd.textContent = passed+'/'+items.length+' kontrol geçti';
  if (ww) ww.style.background = bg;

  checks.innerHTML = items.map(function(i) {
    var icon = i.ok
      ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'
      : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm-7-4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>';
    return '<div style="display:flex;align-items:center;gap:6px;padding:5px 8px;border-radius:6px;font-size:11px;margin-bottom:3px;background:' + (i.ok?'#f0fdf4':'#fef2f2') + ';color:' + (i.ok?'#15803d':'#b91c1c') + '">'
      + '<svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor">' + icon + '</svg>' + i.msg + '</div>';
  }).join('');
}

window.runSeo = runSeo;

// ─── FIYAT ────────────────────────────────────────────────
window.checkPrices = function() {
  var p   = parseFloat((document.getElementById('priceInput')     ||{value:0}).value)||0;
  var sp  = parseFloat((document.getElementById('salePriceInput') ||{value:0}).value)||0;
  var msg = document.getElementById('priceMsg');
  if (!msg) return;
  if (sp>0 && sp>=p)     { msg.textContent='⚠ İndirimli fiyat normal fiyattan yüksek olamaz'; msg.style.color='#dc2626'; }
  else if (sp>0 && p>0)  { msg.textContent='✓ %'+Math.round(((p-sp)/p)*100)+' indirim uygulanıyor'; msg.style.color='#16a34a'; }
  else                   { msg.textContent=''; }
};

// ─── STOK ─────────────────────────────────────────────────
window.toggleStockQty = function() {
  var v = (document.getElementById('stockStatus')||{value:'in_stock'}).value;
  var w = document.getElementById('stockQtyWrap');
  if (w) w.style.display = (v==='in_stock'||v==='backorder') ? 'block' : 'none';
};

// ─── AKTIF TOGGLE ─────────────────────────────────────────
window.toggleActive = function() {
  var cb    = document.getElementById('isActiveInput');
  var track = document.getElementById('activeTrack');
  var thumb = document.getElementById('activeThumb');
  if (!cb) return;
  cb.checked        = !cb.checked;
  track.style.background = cb.checked ? '#2563eb' : '#e5e7eb';
  thumb.style.left       = cb.checked ? '20px' : '2px';
};

// ─── KARGO ────────────────────────────────────────────────
window.toggleShipping = function() {
  var v = (document.getElementById('shippingType')||{value:'domestic'}).value;
  var d = document.getElementById('domesticShipping');
  var i = document.getElementById('intlShipping');
  if (d) d.style.display = v==='domestic'      ? 'block' : 'none';
  if (i) i.style.display = v==='international' ? 'block' : 'none';
};

// ─── GORSEL ───────────────────────────────────────────────
window.previewImages = function(input) {
  var grid = document.getElementById('previewGrid');
  if (!grid) return;
  Array.prototype.forEach.call(input.files, function(file) {
    var reader = new FileReader();
    reader.onload = function(e) {
      var div = document.createElement('div');
      div.style.cssText = 'aspect-ratio:1;border-radius:8px;border:1px solid #e5e7eb;overflow:hidden;position:relative';
      div.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover">'
        + '<button type="button" onclick="this.closest(\'div\').remove()" style="position:absolute;top:3px;right:3px;width:18px;height:18px;background:rgba(0,0,0,.5);border:none;border-radius:50%;color:#fff;cursor:pointer;font-size:11px;display:flex;align-items:center;justify-content:center">×</button>';
      grid.appendChild(div);
    };
    reader.readAsDataURL(file);
  });
};

window.dropImages = function(e) {
  var files = e.dataTransfer.files;
  if (!files || !files.length) return;
  try {
    var dt = new DataTransfer();
    Array.prototype.forEach.call(files, function(f) { dt.items.add(f); });
    document.getElementById('imageInput').files = dt.files;
  } catch(ex) {}
  window.previewImages({ files: files });
};

window.deleteImage = function(id, btn) {
  if (!confirm('Görseli silmek istediğinizden emin misiniz?')) return;
  fetch(ADMIN_URL + 'urunler/' + PRODUCT_ID + '/gorsel-sil', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: '_csrf_token=' + CSRF + '&image_id=' + id
  }).then(function(r) { return r.json(); }).then(function(d) {
    if (d.success) btn.closest('.img-item').remove();
  });
};

// ─── TEKNIK OZELLIK ───────────────────────────────────────
window.addAttr = function() {
  var list = document.getElementById('attrList');
  if (!list) return;
  var row = document.createElement('div');
  row.className = 'attr-row';
  row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 26px;gap:8px;margin-bottom:8px';
  row.innerHTML = '<input type="text" name="attr_name[]" placeholder="Özellik (Örn: RAM)" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none">'
    + '<input type="text" name="attr_value[]" placeholder="Değer (Örn: 16 GB)" style="padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none">'
    + '<button type="button" onclick="this.closest(\'.attr-row\').remove()" style="background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;cursor:pointer;width:26px;height:36px;font-size:16px;display:flex;align-items:center;justify-content:center">×</button>';
  list.appendChild(row);
  row.querySelector('input').focus();
};

// ─── VARYASYON ────────────────────────────────────────────
var varOn  = <?= !empty($product['has_variations']) ? 'true' : 'false' ?>;
var varIdx = <?= count($variations ?? []) ?>;
var colorMap = {kirmizi:'#e53e3e',mavi:'#3182ce',siyah:'#1a202c',beyaz:'#e2e8f0',yesil:'#38a169',sari:'#d69e2e',mor:'#805ad5',turuncu:'#dd6b20'};

window.toggleVar = function() {
  varOn = !varOn;
  var t  = document.getElementById('varToggle');
  var th = document.getElementById('varThumb');
  var i  = document.getElementById('hasVariations');
  var b  = document.getElementById('varBody');
  if (t)  t.style.background = varOn ? '#2563eb' : '#e5e7eb';
  if (th) th.style.left      = varOn ? '18px' : '2px';
  if (i)  i.value            = varOn ? '1' : '0';
  if (b)  b.style.display    = varOn ? 'block' : 'none';
};

window.addVar = function(colorName) {
  var col = colorMap[colorName] || '#888780';
  var lbl = colorName ? (colorName.charAt(0).toUpperCase() + colorName.slice(1)) : '';
  var idx = varIdx++;
  var row = document.createElement('div');
  row.className = 'var-row';
  row.style.cssText = 'background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:10px 12px;margin-bottom:8px';
  row.innerHTML = '<div style="display:grid;grid-template-columns:56px 1fr 100px 80px 80px 28px;gap:8px;align-items:center">'
    + '<label style="display:flex;align-items:center;justify-content:center;width:52px;height:52px;border:1.5px dashed #d1d5db;border-radius:7px;cursor:pointer;overflow:hidden;background:#fff">'
    + '<div id="varImgNew_' + idx + '" style="display:flex;align-items:center;justify-content:center;width:100%;height:100%"><svg width="18" height="18" viewBox="0 0 20 20" fill="#d1d5db"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg></div>'
    + '<input type="file" name="variation_image[new_' + idx + ']" accept="image/*" style="display:none" onchange="previewVarImage(this,\'varImgNew_' + idx + '\')">'
    + '</label>'
    + '<div style="display:flex;align-items:center;gap:6px">'
    + '<input type="color" value="' + col + '" style="width:24px;height:24px;border:none;cursor:pointer;border-radius:50%;padding:0;flex-shrink:0" onchange="this.nextElementSibling.style.background=this.value">'
    + '<div style="width:16px;height:16px;border-radius:50%;background:' + col + ';border:2px solid rgba(0,0,0,.1);flex-shrink:0"></div>'
    + '<input type="text" name="variation[new_' + idx + '][label]" value="' + lbl + '" placeholder="Renk adı" style="flex:1;padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;outline:none">'
    + '</div>'
    + '<input type="number" name="variation[new_' + idx + '][price]" placeholder="Fiyat" step="0.01" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;outline:none;width:100%">'
    + '<input type="number" name="variation[new_' + idx + '][stock]" value="0" min="0" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;outline:none;width:100%">'
    + '<input type="text" name="variation[new_' + idx + '][sku]" placeholder="SKU" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:6px;font-size:13px;outline:none;width:100%">'
    + '<button type="button" onclick="this.closest(\'.var-row\').remove()" style="width:26px;height:26px;background:#fef2f2;border:1px solid #fecaca;border-radius:6px;color:#dc2626;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center">×</button>'
    + '</div>';
  document.getElementById('varRows').appendChild(row);
};

window.quickColors = function() {
  ['kirmizi','mavi','siyah'].forEach(function(c) { window.addVar(c); });
};

window.previewVarImage = function(input, targetId) {
  if (!input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    var el = document.getElementById(targetId);
    if (el) el.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;border-radius:6px">';
  };
  reader.readAsDataURL(input.files[0]);
};

// ─── INIT ─────────────────────────────────────────────────
checkPrices();
toggleStockQty();

// Mevcut meta title/desc uzunluklarini hesapla
<?php foreach ($languages as $lang): ?>
updateBar('tBar_<?= $lang['code'] ?>', <?= strlen($translations[$lang['code']]['meta_title'] ?? '') ?>, 30, 60,  'tCount_<?= $lang['code'] ?>', 'tHint_<?= $lang['code'] ?>');
updateBar('dBar_<?= $lang['code'] ?>', <?= strlen($translations[$lang['code']]['meta_desc']  ?? '') ?>, 120, 160, 'dCount_<?= $lang['code'] ?>', 'dHint_<?= $lang['code'] ?>');
<?php endforeach; ?>

})(); // IIFE sonu
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
.form-group{margin-bottom:12px}
.form-group:last-child{margin-bottom:0}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 290px"]{grid-template-columns:1fr!important}
  div[style*="position: sticky"]{position:static!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
