<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Stok Raporları</span>
  <a href="<?= adminUrl('raporlar/stok/csv') ?>" class="btn btn-outline btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
    CSV İndir
  </a>
</div>

<!-- Özet Kartlar -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Toplam Ürün</div>
    <div style="font-size:22px;font-weight:700"><?= number_format($stockSummary['total_products'] ?? 0) ?></div>
    <div style="font-size:11px;color:#9ca3af;margin-top:4px">aktif ürün</div>
  </div>
  <div class="card" style="padding:14px 16px;border-left:3px solid #16a34a">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Stokta Var</div>
    <div style="font-size:22px;font-weight:700;color:#16a34a"><?= number_format($stockSummary['in_stock'] ?? 0) ?></div>
    <div style="font-size:11px;color:#9ca3af;margin-top:4px">yeterli stok</div>
  </div>
  <div class="card" style="padding:14px 16px;border-left:3px solid #d97706">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Az Stok</div>
    <div style="font-size:22px;font-weight:700;color:#d97706"><?= number_format($stockSummary['low_stock'] ?? 0) ?></div>
    <div style="font-size:11px;color:#9ca3af;margin-top:4px">eşiğin altında</div>
  </div>
  <div class="card" style="padding:14px 16px;border-left:3px solid #dc2626">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Stok Yok</div>
    <div style="font-size:22px;font-weight:700;color:#dc2626"><?= number_format($stockSummary['out_of_stock'] ?? 0) ?></div>
    <div style="font-size:11px;color:#9ca3af;margin-top:4px">tükendi</div>
  </div>
</div>

<!-- Stok Değeri -->
<div class="card" style="margin-bottom:16px;padding:14px 16px">
  <div style="display:flex;align-items:center;justify-content:space-between">
    <div>
      <div style="font-size:12px;color:#6b7280;margin-bottom:4px">Toplam Stok Değeri</div>
      <div style="font-size:24px;font-weight:700"><?= formatPriceTRY($stockSummary['stock_value'] ?? 0) ?></div>
    </div>
    <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:40px;height:40px"><path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" clip-rule="evenodd"/></svg>
  </div>
</div>

<!-- Filtre -->
<div style="display:flex;gap:6px;margin-bottom:12px">
  <a href="?filter=low" class="btn btn-sm <?= $filter==='low'?'btn-primary':'btn-outline' ?>">Az Stok</a>
  <a href="?filter=out" class="btn btn-sm <?= $filter==='out'?'btn-primary':'btn-outline' ?>">Tükenenler</a>
  <a href="?filter=all" class="btn btn-sm <?= $filter==='all'?'btn-primary':'btn-outline' ?>">Tümü</a>
</div>

<!-- Tablo -->
<div class="card" style="padding:0">
  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">
      Bu filtre için ürün bulunamadı 👍
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Ürün</th>
            <th>SKU</th>
            <th>Kategori</th>
            <th>Stok</th>
            <th>Eşik</th>
            <th>Durum</th>
            <th>Fiyat</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $p): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:8px">
                <?php if ($p['image']): ?>
                  <img src="<?= uploadUrl('products/'.$p['image']) ?>" style="width:32px;height:32px;object-fit:cover;border-radius:5px;flex-shrink:0">
                <?php else: ?>
                  <div style="width:32px;height:32px;background:#f3f4f6;border-radius:5px;flex-shrink:0"></div>
                <?php endif; ?>
                <span style="font-size:13px;font-weight:500"><?= e($p['name'] ?? '—') ?></span>
              </div>
            </td>
            <td style="font-family:monospace;font-size:12px;color:#6b7280"><?= e($p['sku'] ?? '—') ?></td>
            <td style="font-size:12px;color:#6b7280"><?= e($p['category_name'] ?? '—') ?></td>
            <td>
              <span style="font-size:14px;font-weight:700;color:<?= $p['stock']==0?'#dc2626':($p['stock']<=(int)($p['stock_alert_qty']??5)?'#d97706':'#16a34a') ?>">
                <?= $p['stock'] ?>
              </span>
            </td>
            <td style="font-size:12px;color:#9ca3af"><?= $p['stock_alert_qty'] ?? 5 ?></td>
            <td>
              <span class="badge b-<?= $p['stock']==0?'danger':($p['stock']<=(int)($p['stock_alert_qty']??5)?'warning':'success') ?>">
                <?= $p['stock']==0?'Tükendi':($p['stock']<=(int)($p['stock_alert_qty']??5)?'Az Stok':'Yeterli') ?>
              </span>
            </td>
            <td style="font-weight:500"><?= formatPriceTRY($p['price']) ?></td>
            <td>
              <a href="<?= adminUrl('urunler/'.$p['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
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
  div[style*="grid-template-columns: repeat(4"]{grid-template-columns:repeat(2,1fr)!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
