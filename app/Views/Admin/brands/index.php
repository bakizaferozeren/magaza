<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:13px;color:#6b7280"><?= count($brands) ?> marka</span>
  <a href="<?= adminUrl('markalar/ekle') ?>" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
    Yeni Marka
  </a>
</div>

<div class="card" style="padding:0">
  <?php if (empty($brands)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz marka yok</p>
      <a href="<?= adminUrl('markalar/ekle') ?>" style="font-size:13px;color:#2563eb">İlk markayı ekle →</a>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Marka</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Website</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Ürün</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Sıra</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($brands as $brand): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <?php if ($brand['logo']): ?>
                <img src="<?= uploadUrl('brands/'.$brand['logo']) ?>" alt="" style="width:40px;height:28px;object-fit:contain;border-radius:4px;border:1px solid #f3f4f6;flex-shrink:0;background:#f9fafb;padding:2px">
              <?php else: ?>
                <div style="width:40px;height:28px;background:#f3f4f6;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:16px;height:16px"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                </div>
              <?php endif; ?>
              <div>
                <div style="font-weight:500;color:#1a1a1a"><?= e($brand['name'] ?? '—') ?></div>
                <div style="font-size:11px;color:#9ca3af;font-family:monospace">/<?= e($brand['slug']) ?></div>
              </div>
            </div>
          </td>
          <td style="padding:10px 16px">
            <?php if ($brand['website']): ?>
              <a href="<?= e($brand['website']) ?>" target="_blank" style="font-size:12px;color:#2563eb;text-decoration:none"><?= e(parse_url($brand['website'], PHP_URL_HOST) ?? $brand['website']) ?></a>
            <?php else: ?>
              <span style="color:#9ca3af">—</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px;color:#6b7280"><?= $brand['product_count'] ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= $brand['sort_order'] ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $brand['is_active'] ? 'b-success' : 'b-gray' ?>"><?= $brand['is_active'] ? 'Aktif' : 'Pasif' ?></span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('markalar/'.$brand['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
              <button onclick="deleteBrand(<?= $brand['id'] ?>,'<?= e($brand['name']??'') ?>',<?= $brand['product_count'] ?>)" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:380px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Markayı Sil</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px" id="deleteMsg"></p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">İptal</button>
      <form id="deleteForm" method="POST">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-danger">Evet, Sil</button>
      </form>
    </div>
  </div>
</div>

<script>
function deleteBrand(id, name, count) {
  if (count > 0) {
    alert('"' + name + '" markasında ' + count + ' ürün var.\nÖnce ürünleri başka markaya taşıyın.');
    return;
  }
  document.getElementById('deleteMsg').textContent = '"' + name + '" markasını silmek istediğinizden emin misiniz?';
  document.getElementById('deleteForm').action = '<?= adminUrl('markalar/') ?>' + id + '/sil';
  document.getElementById('deleteModal').style.display = 'flex';
}
document.getElementById('deleteModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
