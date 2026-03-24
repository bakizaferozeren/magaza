<?php
use App\Core\Session;

ob_start();
?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
    <!-- Arama -->
    <form method="GET" action="<?= adminUrl('urunler') ?>" style="display:flex;gap:6px">
      <input
        type="text"
        name="search"
        value="<?= e($search) ?>"
        placeholder="Ürün adı, SKU, barkod..."
        style="width:220px;padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none"
      >
      <button type="submit" class="btn btn-outline btn-sm">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/></svg>
        Ara
      </button>
      <?php if ($search): ?>
        <a href="<?= adminUrl('urunler') ?>" class="btn btn-outline btn-sm">Temizle</a>
      <?php endif; ?>
    </form>

    <!-- Filtreler -->
    <form method="GET" action="<?= adminUrl('urunler') ?>" style="display:flex;gap:6px;align-items:center">
      <?php if ($search): ?><input type="hidden" name="search" value="<?= e($search) ?>"><?php endif; ?>

      <select name="category_id" onchange="this.form.submit()" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;color:#374151;outline:none">
        <option value="">Tüm Kategoriler</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= e($cat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <select name="stock_status" onchange="this.form.submit()" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;color:#374151;outline:none">
        <option value="">Tüm Stok</option>
        <option value="in_stock"    <?= ($filters['stock_status'] ?? '') === 'in_stock'    ? 'selected' : '' ?>>Stokta Var</option>
        <option value="out_of_stock"<?= ($filters['stock_status'] ?? '') === 'out_of_stock'? 'selected' : '' ?>>Stokta Yok</option>
        <option value="pre_order"   <?= ($filters['stock_status'] ?? '') === 'pre_order'   ? 'selected' : '' ?>>Ön Sipariş</option>
      </select>

      <select name="is_active" onchange="this.form.submit()" style="padding:6px 8px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;color:#374151;outline:none">
        <option value="">Tüm Durum</option>
        <option value="1" <?= ($filters['is_active'] ?? '') === '1' ? 'selected' : '' ?>>Aktif</option>
        <option value="0" <?= ($filters['is_active'] ?? '') === '0' ? 'selected' : '' ?>>Pasif</option>
      </select>
    </form>
  </div>

  <div style="display:flex;gap:8px">
    <a href="<?= adminUrl('urunler/csv-indir') ?>" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      CSV İndir
    </a>
    <label class="btn btn-outline btn-sm" style="cursor:pointer">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
      CSV Yükle
      <form method="POST" action="<?= adminUrl('urunler/csv-yukle') ?>" enctype="multipart/form-data" id="csvForm">
        <?= csrfField() ?>
        <input type="file" name="csv" accept=".csv" style="display:none" onchange="document.getElementById('csvForm').submit()">
      </form>
    </label>
    <a href="<?= adminUrl('urunler/ekle') ?>" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
      Yeni Ürün
    </a>
  </div>
</div>

<!-- Tablo -->
<div class="card" style="padding:0">
  <?php if (empty($products)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <svg viewBox="0 0 20 20" fill="currentColor" style="width:40px;height:40px;margin:0 auto 12px;opacity:.3;display:block">
        <path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/>
        <path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" clip-rule="evenodd"/>
      </svg>
      <p style="font-size:14px;font-weight:500;color:#6b7280">Ürün bulunamadı</p>
      <p style="font-size:13px;margin-top:4px">
        <a href="<?= adminUrl('urunler/ekle') ?>" style="color:#2563eb">İlk ürünü ekle →</a>
      </p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th style="width:40px"><input type="checkbox" id="selectAll" style="width:14px;height:14px;accent-color:#2563eb"></th>
            <th>Ürün</th>
            <th>SKU</th>
            <th>Fiyat</th>
            <th>Stok</th>
            <th>Durum</th>
            <th>Kargo</th>
            <th>Aktif</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
          <tr>
            <td><input type="checkbox" class="row-check" value="<?= $product['id'] ?>" style="width:14px;height:14px;accent-color:#2563eb"></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <?php if ($product['cover_image']): ?>
                  <img src="<?= uploadUrl('products/' . $product['cover_image']) ?>"
                       alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #f3f4f6;flex-shrink:0">
                <?php else: ?>
                  <div style="width:40px;height:40px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                    <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:20px;height:20px"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                  </div>
                <?php endif; ?>
                <div style="min-width:0">
                  <a href="<?= adminUrl('urunler/' . $product['id'] . '/duzenle') ?>"
                     style="font-size:13px;font-weight:500;color:#1a1a1a;text-decoration:none;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:200px">
                    <?= e($product['name'] ?? '—') ?>
                  </a>
                  <?php if ($product['category_name']): ?>
                    <span style="font-size:11px;color:#9ca3af"><?= e($product['category_name']) ?></span>
                  <?php endif; ?>
                </div>
              </div>
            </td>
            <td style="font-size:12px;color:#6b7280;font-family:monospace"><?= e($product['sku'] ?? '—') ?></td>
            <td>
              <div style="font-size:13px;font-weight:600"><?= formatPriceTRY($product['sale_price'] ?? $product['price']) ?></div>
              <?php if ($product['sale_price']): ?>
                <div style="font-size:11px;color:#9ca3af;text-decoration:line-through"><?= formatPriceTRY($product['price']) ?></div>
              <?php endif; ?>
            </td>
            <td>
              <span style="font-size:13px;font-weight:500;color:<?= $product['stock'] <= 5 ? '#dc2626' : '#1a1a1a' ?>">
                <?= $product['stock'] ?>
              </span>
              <div>
                <span class="badge b-<?= stockStatusColor($product['stock_status']) ?>" style="font-size:10px">
                  <?= stockStatusLabel($product['stock_status']) ?>
                </span>
              </div>
            </td>
            <td>
              <div style="display:flex;flex-wrap:wrap;gap:3px">
                <?php if ($product['is_best_seller']): ?>
                  <span class="badge b-warning" style="font-size:10px">Çok Satan</span>
                <?php endif; ?>
                <?php if ($product['is_recommended']): ?>
                  <span class="badge b-primary" style="font-size:10px">Önerilen</span>
                <?php endif; ?>
                <?php if ($product['is_featured']): ?>
                  <span class="badge b-purple" style="font-size:10px">Öne Çıkan</span>
                <?php endif; ?>
              </div>
            </td>
            <td>
              <span class="badge <?= $product['shipping_type'] === 'domestic' ? 'b-success' : 'b-info' ?>" style="font-size:10px">
                <?= $product['shipping_type'] === 'domestic' ? 'Yurtiçi' : 'Yurtdışı' ?>
              </span>
            </td>
            <td>
              <div class="toggle-switch" data-id="<?= $product['id'] ?>" data-active="<?= $product['is_active'] ?>">
                <div style="
                  width:34px;height:18px;border-radius:9px;
                  background:<?= $product['is_active'] ? '#2563eb' : '#e5e7eb' ?>;
                  position:relative;cursor:pointer;transition:background .2s
                ">
                  <div style="
                    position:absolute;top:2px;
                    left:<?= $product['is_active'] ? '18px' : '2px' ?>;
                    width:14px;height:14px;border-radius:50%;
                    background:#fff;transition:left .2s;
                  "></div>
                </div>
              </div>
            </td>
            <td>
              <div style="display:flex;gap:4px">
                <a href="<?= adminUrl('urunler/' . $product['id'] . '/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
                <button
                  onclick="confirmDelete(<?= $product['id'] ?>, '<?= e($product['name'] ?? '') ?>')"
                  class="btn btn-outline btn-sm"
                  style="color:#dc2626;border-color:#fecaca"
                >Sil</button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Sayfalama -->
    <?php if ($pagination['last_page'] > 1): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #f3f4f6">
      <span style="font-size:12px;color:#6b7280">
        <?= $pagination['from'] ?>–<?= $pagination['to'] ?> / <?= $pagination['total'] ?> ürün
      </span>
      <div style="display:flex;gap:4px">
        <?php if ($pagination['current_page'] > 1): ?>
          <a href="?page=<?= $pagination['current_page'] - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-outline btn-sm">←</a>
        <?php endif; ?>

        <?php for ($p = max(1, $pagination['current_page'] - 2); $p <= min($pagination['last_page'], $pagination['current_page'] + 2); $p++): ?>
          <a href="?page=<?= $p ?><?= $search ? '&search=' . urlencode($search) : '' ?>"
             class="btn btn-sm <?= $p === $pagination['current_page'] ? 'btn-primary' : 'btn-outline' ?>">
            <?= $p ?>
          </a>
        <?php endfor; ?>

        <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
          <a href="?page=<?= $pagination['current_page'] + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>" class="btn btn-outline btn-sm">→</a>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

  <?php endif; ?>
</div>

<!-- Sil Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:380px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Ürünü Sil</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px" id="deleteMsg"></p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="closeDelete()" class="btn btn-outline">İptal</button>
      <form id="deleteForm" method="POST">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-danger">Evet, Sil</button>
      </form>
    </div>
  </div>
</div>

<script>
// Toplu secim
document.getElementById('selectAll').addEventListener('change', function() {
  document.querySelectorAll('.row-check').forEach(c => c.checked = this.checked);
});

// Sil modal
function confirmDelete(id, name) {
  document.getElementById('deleteMsg').textContent = '"' + name + '" ürününü silmek istediğinizden emin misiniz?';
  document.getElementById('deleteForm').action = '<?= adminUrl('urunler/') ?>' + id + '/sil';
  const modal = document.getElementById('deleteModal');
  modal.style.display = 'flex';
}

function closeDelete() {
  document.getElementById('deleteModal').style.display = 'none';
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) closeDelete();
});

// Aktif/Pasif toggle
document.querySelectorAll('.toggle-switch').forEach(function(sw) {
  sw.addEventListener('click', function() {
    const id     = this.dataset.id;
    const active = this.dataset.active === '1' ? 0 : 1;
    const track  = this.querySelector('div');
    const thumb  = track.querySelector('div');

    track.style.background = active ? '#2563eb' : '#e5e7eb';
    thumb.style.left = active ? '18px' : '2px';
    this.dataset.active = active;

    fetch('<?= adminUrl('urunler/') ?>' + id + '/duzenle', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: '_csrf_token=<?= Session::csrfToken() ?>&is_active=' + active + '&_quick_toggle=1'
    });
  });
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
