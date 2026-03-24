<?php ob_start(); ?>
<div style="margin-bottom:16px"><span style="font-size:15px;font-weight:700">Mail Şablonları</span></div>
<div class="card" style="padding:0">
  <table style="width:100%;border-collapse:collapse;font-size:13px">
    <thead><tr>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Şablon</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Konu</th>
      <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Durum</th>
      <th style="border-bottom:1px solid #f3f4f6"></th>
    </tr></thead>
    <tbody>
      <?php foreach ($templates as $t): ?>
      <tr style="border-bottom:1px solid #f9fafb">
        <td style="padding:10px 16px;font-family:monospace;font-size:12px;font-weight:500"><?= e($t['code']) ?></td>
        <td style="padding:10px 16px;color:#6b7280"><?= e($t['subject'] ?? '—') ?></td>
        <td style="padding:10px 16px"><span class="badge <?= $t['is_active']?'b-success':'b-gray' ?>"><?= $t['is_active']?'Aktif':'Pasif' ?></span></td>
        <td style="padding:10px 16px"><a href="<?= adminUrl('ayarlar/mail-sablonlari/'.$t['id'].'/duzenle') ?>" class="btn btn-outline btn-sm">Düzenle</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
