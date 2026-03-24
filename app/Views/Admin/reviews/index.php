<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;gap:6px;align-items:center">
    <a href="?status=pending" class="btn btn-sm <?= $status==='pending'?'btn-primary':'btn-outline' ?>">Bekleyenler <span style="font-size:10px">(<?= $counts['pending'] ?? 0 ?>)</span></a>
    <a href="?status=approved" class="btn btn-sm <?= $status==='approved'?'btn-primary':'btn-outline' ?>">Onaylananlar <span style="font-size:10px">(<?= $counts['approved'] ?? 0 ?>)</span></a>
    <a href="?status=all" class="btn btn-sm <?= $status==='all'?'btn-primary':'btn-outline' ?>">Tümü <span style="font-size:10px">(<?= $counts['total'] ?? 0 ?>)</span></a>
  </div>
  <button onclick="document.getElementById('addReviewModal').style.display='flex'" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
    Manuel Değerlendirme Ekle
  </button>
</div>

<!-- Değerlendirme Listesi -->
<div class="card" style="padding:0">
  <?php if (empty($reviews)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Değerlendirme bulunamadı</p>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Ürün</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Yazar</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Puan</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Yorum</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tip</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reviews as $r): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <div style="font-size:13px;font-weight:500;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($r['product_name'] ?? '—') ?></div>
            <?php if ($r['product_slug']): ?>
              <a href="<?= url('urun/'.$r['product_slug']) ?>" target="_blank" style="font-size:10px;color:#9ca3af;text-decoration:none">Ürünü gör →</a>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px">
            <div style="font-weight:500"><?= e($r['author_name']) ?></div>
            <?php if ($r['is_verified']): ?>
              <span style="font-size:10px;color:#16a34a">✓ Doğrulanmış</span>
            <?php endif; ?>
            <?php if ($r['is_manual']): ?>
              <span style="font-size:10px;color:#9ca3af">Manuel</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:1px">
              <?php for ($i=1;$i<=5;$i++): ?>
                <span style="color:<?= $i<=$r['rating']?'#f59e0b':'#e5e7eb' ?>;font-size:14px">★</span>
              <?php endfor; ?>
            </div>
          </td>
          <td style="padding:10px 16px;max-width:220px">
            <div style="font-size:12px;color:#374151;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($r['comment']) ?></div>
            <div style="font-size:11px;color:#9ca3af;margin-top:2px"><?= formatDate($r['created_at']) ?></div>
          </td>
          <td style="padding:10px 16px">
            <span class="badge b-info" style="font-size:10px"><?= $r['is_manual']?'Manuel':'Müşteri' ?></span>
          </td>
          <td style="padding:10px 16px">
            <span class="badge <?= $r['is_approved']?'b-success':'b-warning' ?>">
              <?= $r['is_approved']?'Onaylı':'Bekliyor' ?>
            </span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:4px">
              <?php if (!$r['is_approved']): ?>
                <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/onayla') ?>">
                  <?= csrfField() ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color:#16a34a;border-color:#bbf7d0">Onayla</button>
                </form>
              <?php else: ?>
                <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/reddet') ?>">
                  <?= csrfField() ?>
                  <button type="submit" class="btn btn-outline btn-sm" style="color:#d97706;border-color:#fde68a">Gizle</button>
                </form>
              <?php endif; ?>
              <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/sil') ?>">
                <?= csrfField() ?>
                <button onclick="return confirm('Bu değerlendirmeyi silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Manuel Değerlendirme Ekle Modal -->
<div id="addReviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:520px;max-width:90vw;max-height:90vh;overflow-y:auto">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:16px">Manuel Değerlendirme Ekle</h3>
    <form method="POST" action="<?= adminUrl('degerlendirmeler/ekle') ?>">
      <?= csrfField() ?>

      <div class="form-group">
        <label>Ürün Seç <span style="color:#dc2626">*</span></label>
        <input type="text" id="productSearch" placeholder="Ürün adı veya SKU ara..." oninput="searchProducts(this.value)"
          style="margin-bottom:6px">
        <select name="product_id" id="productSelect" required size="5"
          style="height:120px;border-radius:7px;padding:6px">
          <option value="">— Ürün arayın —</option>
        </select>
        <div id="selectedProduct" style="font-size:11px;color:#2563eb;margin-top:4px"></div>
      </div>

      <div class="form-group">
        <label>Yazar Adı <span style="color:#dc2626">*</span></label>
        <input type="text" name="author_name" placeholder="Ad Soyad" required>
      </div>

      <div class="form-group">
        <label>Puan <span style="color:#dc2626">*</span></label>
        <div style="display:flex;gap:8px;align-items:center">
          <?php for ($i=1;$i<=5;$i++): ?>
            <label style="display:flex;align-items:center;gap:4px;cursor:pointer;font-weight:400;font-size:13px;margin-bottom:0">
              <input type="radio" name="rating" value="<?= $i ?>" <?= $i===5?'checked':'' ?> style="width:14px;height:14px;accent-color:#f59e0b">
              <span style="color:#f59e0b;font-size:18px">★</span> <?= $i ?>
            </label>
          <?php endfor; ?>
        </div>
      </div>

      <div class="form-group">
        <label>Yorum <span style="color:#dc2626">*</span></label>
        <textarea name="comment" rows="4" placeholder="Değerlendirme metni..." required
          style="width:100%;padding:8px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;resize:vertical"></textarea>
      </div>

      <div style="display:flex;gap:12px;margin-bottom:16px">
        <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;font-weight:400">
          <input type="checkbox" name="is_verified" value="1" style="accent-color:#2563eb"> Doğrulanmış alıcı
        </label>
        <label style="display:flex;align-items:center;gap:5px;font-size:13px;cursor:pointer;font-weight:400">
          <input type="checkbox" name="is_approved" value="1" checked style="accent-color:#2563eb"> Hemen yayınla
        </label>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button type="button" onclick="document.getElementById('addReviewModal').style.display='none'" class="btn btn-outline">İptal</button>
        <button type="submit" class="btn btn-primary">Değerlendirme Ekle</button>
      </div>
    </form>
  </div>
</div>

<script>
let searchTimeout;

function searchProducts(q) {
  clearTimeout(searchTimeout);
  if (q.length < 2) return;
  searchTimeout = setTimeout(() => {
    fetch('<?= adminUrl('urunler') ?>?search=' + encodeURIComponent(q) + '&ajax=1&format=select')
      .then(r => r.json())
      .then(data => {
        const sel = document.getElementById('productSelect');
        sel.innerHTML = '';
        if (!data.length) {
          sel.innerHTML = '<option value="">Sonuç bulunamadı</option>';
          return;
        }
        data.forEach(p => {
          const opt = document.createElement('option');
          opt.value = p.id;
          opt.textContent = p.name + (p.sku ? ' [' + p.sku + ']' : '');
          sel.appendChild(opt);
        });
      })
      .catch(() => {
        // AJAX çalışmazsa manuel product_id girişi göster
        document.getElementById('productSelect').style.display = 'none';
        if (!document.getElementById('productIdManual')) {
          const inp = document.createElement('input');
          inp.type = 'number'; inp.name = 'product_id'; inp.id = 'productIdManual';
          inp.placeholder = 'Ürün ID numarasını girin';
          inp.required = true;
          inp.style.cssText = 'width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none';
          document.getElementById('productSelect').parentNode.appendChild(inp);
        }
      });
  }, 300);
}

document.getElementById('productSelect').addEventListener('change', function() {
  const txt = this.options[this.selectedIndex]?.text ?? '';
  document.getElementById('selectedProduct').textContent = txt ? '✓ Seçili: ' + txt : '';
});

document.getElementById('addReviewModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.form-group{margin-bottom:14px}
label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}
input[type=text],input[type=number],select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}
select option{padding:4px 8px}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
