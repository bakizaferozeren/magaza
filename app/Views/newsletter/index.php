<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:13px;color:#6b7280"><?= count($subscribers) ?> abone</span>
  <a href="<?= adminUrl('bulten/csv-indir') ?>" class="btn btn-outline btn-sm">
    <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
    CSV İndir
  </a>
</div>

<div class="card" style="padding:0">
  <?php if (empty($subscribers)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Henüz abone yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">E-posta</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Ad</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Tarih</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($subscribers as $s): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:500"><?= e($s['email']) ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= e($s['name'] ?? '—') ?></td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af"><?= formatDate($s['subscribed_at']) ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $s['is_active']?'b-success':'b-gray' ?>"><?= $s['is_active']?'Aktif':'Pasif' ?></span>
          </td>
          <td style="padding:10px 16px">
            <form method="POST" action="<?= adminUrl('bulten/'.$s['id'].'/sil') ?>">
              <?= csrfField() ?>
              <button onclick="return confirm('Bu aboneyi silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); require APP_PATH.'/Views/Admin/layouts/main.php'; ?>
