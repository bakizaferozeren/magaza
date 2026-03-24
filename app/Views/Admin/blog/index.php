<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:13px;color:#6b7280"><?= count($posts) ?> yazı</span>
  <a href="<?= adminUrl('blog/ekle') ?>" class="btn btn-primary btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
    Yeni Yazı
  </a>
</div>

<div class="card" style="padding:0">
  <?php if (empty($posts)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz yazı yok</p>
      <a href="<?= adminUrl('blog/ekle') ?>" style="font-size:13px;color:#2563eb">İlk yazıyı ekle →</a>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Yazı</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Yazar</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Yayın Tarihi</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($posts as $post): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <div style="display:flex;align-items:center;gap:10px">
              <?php if ($post['image']): ?>
                <img src="<?= uploadUrl('blog/'.$post['image']) ?>" style="width:48px;height:36px;object-fit:cover;border-radius:5px;flex-shrink:0">
              <?php else: ?>
                <div style="width:48px;height:36px;background:#f3f4f6;border-radius:5px;flex-shrink:0"></div>
              <?php endif; ?>
              <div>
                <div style="font-weight:500"><?= e($post['title'] ?? '—') ?></div>
                <div style="font-size:11px;color:#9ca3af;font-family:monospace">/blog/<?= e($post['slug']) ?></div>
              </div>
            </div>
          </td>
          <td style="padding:10px 16px;color:#6b7280"><?= e($post['author_name'] ?? '—') ?></td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af">
            <?= $post['published_at'] ? formatDate($post['published_at']) : '—' ?>
          </td>
          <td style="padding:10px 16px">
            <span class="badge <?= $post['is_active']?'b-success':'b-gray' ?>"><?= $post['is_active']?'Yayında':'Taslak' ?></span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('blog/'.$post['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
              <button onclick="deleteBlog(<?= $post['id'] ?>, '<?= e($post['title']??'') ?>')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
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
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Yazıyı Sil</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px" id="deleteMsg"></p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">İptal</button>
      <form id="deleteForm" method="POST"><?= csrfField() ?><button type="submit" class="btn btn-danger">Evet, Sil</button></form>
    </div>
  </div>
</div>
<script>
function deleteBlog(id, title) {
  document.getElementById('deleteMsg').textContent = '"' + title + '" yazısını silmek istediğinizden emin misiniz?';
  document.getElementById('deleteForm').action = '<?= adminUrl('blog/') ?>' + id + '/sil';
  document.getElementById('deleteModal').style.display = 'flex';
}
document.getElementById('deleteModal').addEventListener('click', function(e){ if(e.target===this) this.style.display='none'; });
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
