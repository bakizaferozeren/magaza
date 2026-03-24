<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:13px;color:#6b7280"><?= count($pages) ?> sayfa</span>
  <a href="<?= adminUrl('sayfalar/ekle') ?>" class="btn btn-primary btn-sm">+ Yeni Sayfa</a>
</div>

<div class="card" style="padding:0">
  <?php if (empty($pages)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Henüz sayfa yok</p>
    </div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Başlık</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Slug</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Şablon</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pages as $page): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px;font-weight:500"><?= e($page['title'] ?? '—') ?></td>
          <td style="padding:10px 16px;font-family:monospace;font-size:12px;color:#6b7280">/<?= e($page['slug']) ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= e($page['template']) ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $page['is_active']?'b-success':'b-gray' ?>"><?= $page['is_active']?'Aktif':'Pasif' ?></span>
          </td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('sayfalar/'.$page['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a>
              <form method="POST" action="<?= adminUrl('sayfalar/'.$page['id'].'/sil') ?>" style="display:inline">
                <?= csrfField() ?>
                <button type="submit" onclick="return confirm('Bu sayfayı silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
