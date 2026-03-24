<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <span style="font-size:13px;color:#6b7280"><?= count($coupons) ?> kupon</span>
  <div style="display:flex;gap:8px">
    <button onclick="document.getElementById('generateModal').style.display='flex'" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
      Toplu Oluştur
    </button>
    <a href="<?= adminUrl('kuponlar/ekle') ?>" class="btn btn-primary btn-sm">+ Yeni Kupon</a>
  </div>
</div>

<div class="card" style="padding:0">
  <?php if (empty($coupons)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz kupon yok</p>
      <a href="<?= adminUrl('kuponlar/ekle') ?>" style="font-size:13px;color:#2563eb">İlk kuponu oluştur →</a>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Kupon Kodu</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">İndirim</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Min. Sipariş</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Kullanım</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Son Tarih</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($coupons as $c): ?>
        <?php $expired = $c['expires_at'] && strtotime($c['expires_at']) < time(); ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <div style="font-family:monospace;font-size:13px;font-weight:700;letter-spacing:.05em;background:#f3f4f6;display:inline-block;padding:3px 8px;border-radius:5px">
              <?= e($c['code']) ?>
            </div>
          </td>
          <td style="padding:10px 16px;font-weight:600;color:#2563eb">
            <?= $c['type']==='percent' ? '%'.$c['value'] : formatPriceTRY($c['value']) ?>
            <?php if ($c['max_discount']): ?>
              <div style="font-size:10px;color:#9ca3af">max <?= formatPriceTRY($c['max_discount']) ?></div>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px;color:#6b7280"><?= $c['min_order']>0 ? formatPriceTRY($c['min_order']) : '—' ?></td>
          <td style="padding:10px 16px">
            <span style="font-weight:500"><?= $c['used_count'] ?></span>
            <?php if ($c['usage_limit']): ?>
              <span style="color:#9ca3af"> / <?= $c['usage_limit'] ?></span>
            <?php else: ?>
              <span style="color:#9ca3af"> / Sınırsız</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px;font-size:12px;color:<?= $expired?'#dc2626':'#6b7280' ?>">
            <?= $c['expires_at'] ? date('d M Y', strtotime($c['expires_at'])) : '—' ?>
            <?php if ($expired): ?> <span style="font-size:10px">(Süresi doldu)</span><?php endif; ?>
          </td>
          <td style="padding:10px 16px">
            <span class="badge <?= ($c['is_active'] && !$expired) ? 'b-success' : 'b-gray' ?>">
              <?= ($c['is_active'] && !$expired) ? 'Aktif' : 'Pasif' ?>
            </span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('kuponlar/'.$c['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
              <button onclick="deleteCoupon(<?= $c['id'] ?>, '<?= e($c['code']) ?>')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Toplu Oluştur Modal -->
<div id="generateModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:400px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:16px">Toplu Kupon Oluştur</h3>
    <form method="POST" action="<?= adminUrl('kuponlar/toplu-olustur') ?>">
      <?= csrfField() ?>
      <div class="form-group">
        <label>Ön Ek</label>
        <input type="text" name="prefix" value="KPN" style="text-transform:uppercase" placeholder="KPN">
        <div style="font-size:11px;color:#9ca3af;margin-top:2px">Örn: KPN-A3F2B1</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>Adet</label>
          <input type="number" name="count" value="5" min="1" max="50">
        </div>
        <div class="form-group">
          <label>İndirim Tipi</label>
          <select name="type">
            <option value="percent">Yüzde (%)</option>
            <option value="fixed">Sabit (₺)</option>
          </select>
        </div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <div class="form-group">
          <label>İndirim Değeri</label>
          <input type="number" name="value" value="10" min="0" step="0.01">
        </div>
        <div class="form-group">
          <label>Min. Sipariş (₺)</label>
          <input type="number" name="min_order" value="0" min="0" step="0.01">
        </div>
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px">
        <button type="button" onclick="document.getElementById('generateModal').style.display='none'" class="btn btn-outline">İptal</button>
        <button type="submit" class="btn btn-primary">Oluştur</button>
      </div>
    </form>
  </div>
</div>

<!-- Sil Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:380px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Kuponu Sil</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px" id="deleteMsg"></p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">İptal</button>
      <form id="deleteForm" method="POST"><?= csrfField() ?><button type="submit" class="btn btn-danger">Evet, Sil</button></form>
    </div>
  </div>
</div>

<script>
function deleteCoupon(id, code) {
  document.getElementById('deleteMsg').textContent = '"' + code + '" kuponunu silmek istediğinizden emin misiniz?';
  document.getElementById('deleteForm').action = '<?= adminUrl('kuponlar/') ?>' + id + '/sil';
  document.getElementById('deleteModal').style.display = 'flex';
}
[document.getElementById('deleteModal'), document.getElementById('generateModal')].forEach(m => {
  m.addEventListener('click', e => { if(e.target===m) m.style.display='none'; });
});
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
