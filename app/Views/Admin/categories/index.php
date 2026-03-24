<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:13px;color:#6b7280"><?= count($categories) ?> kategori</span>
  <a href="<?= adminUrl('kategoriler/ekle') ?>" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
    Yeni Kategori
  </a>
</div>

<!-- Liste -->
<div class="card" style="padding:0">
  <?php if (empty($categories)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <svg viewBox="0 0 20 20" fill="currentColor" style="width:40px;height:40px;margin:0 auto 12px;opacity:.3;display:block"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz kategori yok</p>
      <a href="<?= adminUrl('kategoriler/ekle') ?>" style="font-size:13px;color:#2563eb">İlk kategoriyi ekle →</a>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Kategori</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Üst Kategori</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Ürün</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Sıra</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $cat): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <?php if ($cat['image']): ?>
                <img src="<?= uploadUrl('categories/'.$cat['image']) ?>" alt="" style="width:36px;height:36px;object-fit:cover;border-radius:6px;border:1px solid #f3f4f6;flex-shrink:0">
              <?php else: ?>
                <div style="width:36px;height:36px;background:#f3f4f6;border-radius:6px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                  <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:18px;height:18px"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/></svg>
                </div>
              <?php endif; ?>
              <div>
                <div style="font-weight:500;color:#1a1a1a"><?= $cat['parent_id'] ? '&nbsp;&nbsp;↳ ' : '' ?><?= e($cat['name'] ?? '—') ?></div>
                <div style="font-size:11px;color:#9ca3af;font-family:monospace">/<?= e($cat['slug']) ?></div>
              </div>
            </div>
          </td>
          <td style="padding:10px 16px;color:#6b7280"><?= e($cat['parent_name'] ?? '—') ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= $cat['product_count'] ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= $cat['sort_order'] ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $cat['is_active'] ? 'b-success' : 'b-gray' ?>"><?= $cat['is_active'] ? 'Aktif' : 'Pasif' ?></span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('kategoriler/'.$cat['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
              <button onclick="deleteCategory(<?= $cat['id'] ?>,'<?= e($cat['name']??'') ?>',<?= $cat['product_count'] ?>)" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<!-- Sil Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:380px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Kategoriyi Sil</h3>
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
function deleteCategory(id, name, count) {
  if (count > 0) {
    alert('"' + name + '" kategorisinde ' + count + ' ürün var.\nÖnce ürünleri başka kategoriye taşıyın.');
    return;
  }
  document.getElementById('deleteMsg').textContent = '"' + name + '" kategorisini silmek istediğinizden emin misiniz?';
  document.getElementById('deleteForm').action = '<?= adminUrl('kategoriler/') ?>' + id + '/sil';
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
