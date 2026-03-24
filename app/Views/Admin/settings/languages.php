<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Dil Yönetimi</span>
</div>
<div style="display:grid;grid-template-columns:1fr 280px;gap:16px">
  <div class="card" style="padding:0">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Dil</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Kod</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sıra</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
        <th style="border-bottom:1px solid #f3f4f6"></th>
      </tr></thead>
      <tbody>
        <?php foreach ($languages as $l): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:500"><?= e($l['name']) ?> <?= $l['flag'] ?></td>
          <td style="padding:10px 16px;font-family:monospace;color:#6b7280"><?= e($l['code']) ?></td>
          <td style="padding:10px 16px;color:#6b7280"><?= $l['sort_order'] ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $l['is_active']?'b-success':'b-gray' ?>"><?= $l['is_default']?'Varsayılan':($l['is_active']?'Aktif':'Pasif') ?></span>
          </td>
          <td style="padding:10px 16px">
            <?php if (!$l['is_default']): ?>
            <form method="POST" action="<?= adminUrl('ayarlar/diller/'.$l['id'].'/sil') ?>">
              <?= csrfField() ?>
              <button type="submit" onclick="return confirm('Sil?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="card-header"><span class="card-title">Yeni Dil Ekle</span></div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('ayarlar/diller/ekle') ?>">
        <?= csrfField() ?>
        <div class="form-group"><label>Dil Adı</label><input type="text" name="name" placeholder="English" required></div>
        <div class="form-group"><label>Kod</label><input type="text" name="code" placeholder="en" maxlength="5" required></div>
        <div class="form-group" style="margin-bottom:12px"><label>Sıra No</label><input type="number" name="sort_order" value="99"></div>
        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center">Ekle</button>
      </form>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
