<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Ürün Raporları</span>
  <form method="GET" style="display:flex;gap:6px;align-items:center">
    <input type="date" name="from" value="<?= $from ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
    <span style="font-size:12px;color:#9ca3af">—</span>
    <input type="date" name="to" value="<?= $to ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
  </form>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">

  <!-- Kategori Bazlı -->
  <div class="card">
    <div class="card-header"><span class="card-title">Kategori Bazlı Satış</span></div>
    <div class="card-body">
      <?php if (empty($byCategory)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Veri yok</p>
      <?php else: ?>
        <?php $maxCat = max(array_column($byCategory,'total_revenue') ?: [1]); ?>
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php foreach ($byCategory as $cat): ?>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                <span style="font-weight:500"><?= e($cat['category_name'] ?? 'Kategorisiz') ?></span>
                <span style="color:#6b7280"><?= formatPriceTRY($cat['total_revenue']) ?></span>
              </div>
              <div style="height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden">
                <div style="height:100%;background:#2563eb;border-radius:3px;width:<?= round(($cat['total_revenue']/$maxCat)*100) ?>%;transition:width .4s"></div>
              </div>
              <div style="font-size:11px;color:#9ca3af;margin-top:2px"><?= $cat['total_qty'] ?> adet · <?= $cat['order_count'] ?> sipariş</div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Marka Bazlı -->
  <div class="card">
    <div class="card-header"><span class="card-title">Marka Bazlı Satış</span></div>
    <div class="card-body">
      <?php if (empty($byBrand)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Veri yok</p>
      <?php else: ?>
        <?php $maxBrand = max(array_column($byBrand,'total_revenue') ?: [1]); ?>
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php foreach ($byBrand as $brand): ?>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                <span style="font-weight:500"><?= e($brand['brand_name'] ?? 'Markasız') ?></span>
                <span style="color:#6b7280"><?= formatPriceTRY($brand['total_revenue']) ?></span>
              </div>
              <div style="height:6px;background:#f3f4f6;border-radius:3px;overflow:hidden">
                <div style="height:100%;background:#7c3aed;border-radius:3px;width:<?= round(($brand['total_revenue']/$maxBrand)*100) ?>%;transition:width .4s"></div>
              </div>
              <div style="font-size:11px;color:#9ca3af;margin-top:2px"><?= $brand['total_qty'] ?> adet</div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- En Çok Satan Ürünler -->
<div class="card" style="padding:0">
  <div class="card-header"><span class="card-title">En Çok Satan Ürünler</span></div>
  <?php if (empty($topProducts)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Bu dönemde satış verisi yok</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:30px">#</th>
            <th>Ürün</th>
            <th>Satılan Adet</th>
            <th>Sipariş Sayısı</th>
            <th>Ciro</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($topProducts as $i => $p): ?>
          <tr>
            <td style="font-weight:700;color:<?= $i<3?'#2563eb':'#9ca3af' ?>;font-size:14px"><?= $i+1 ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <?php if ($p['image']): ?>
                  <img src="<?= uploadUrl('products/'.$p['image']) ?>" style="width:36px;height:36px;object-fit:cover;border-radius:6px;flex-shrink:0">
                <?php else: ?>
                  <div style="width:36px;height:36px;background:#f3f4f6;border-radius:6px;flex-shrink:0"></div>
                <?php endif; ?>
                <span style="font-size:13px;font-weight:500"><?= e($p['name'] ?? '—') ?></span>
              </div>
            </td>
            <td style="font-weight:600"><?= number_format($p['total_qty']) ?> adet</td>
            <td style="color:#6b7280"><?= $p['order_count'] ?> sipariş</td>
            <td style="font-weight:600;color:#2563eb"><?= formatPriceTRY($p['total_revenue']) ?></td>
            <td>
              <a href="<?= url('urun/'.$p['slug']) ?>" target="_blank" class="btn btn-outline btn-sm">Görüntüle</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
