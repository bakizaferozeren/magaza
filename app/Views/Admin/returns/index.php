<?php ob_start(); ?>

<div style="display:flex;gap:6px;margin-bottom:16px">
  <a href="?status="          class="btn btn-sm <?= $status===''        ?'btn-primary':'btn-outline' ?>">Tümü</a>
  <a href="?status=pending"   class="btn btn-sm <?= $status==='pending' ?'btn-primary':'btn-outline' ?>">Bekleyen</a>
  <a href="?status=approved"  class="btn btn-sm <?= $status==='approved'?'btn-primary':'btn-outline' ?>">Onaylanan</a>
  <a href="?status=rejected"  class="btn btn-sm <?= $status==='rejected'?'btn-primary':'btn-outline' ?>">Reddedilen</a>
</div>

<div class="card" style="padding:0">
  <?php if (empty($returns)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">İade talebi bulunamadı</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Sipariş</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Müşteri</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Sebep</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Tarih</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($returns as $ret): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px">
            <a href="<?= adminUrl('siparisler/'.$ret['order_id']) ?>" style="color:#2563eb;font-weight:600;text-decoration:none"><?= e($ret['order_no'] ?? '—') ?></a>
          </td>
          <td style="padding:10px 16px;color:#374151"><?= e($ret['customer_name']) ?></td>
          <td style="padding:10px 16px;color:#6b7280;max-width:250px">
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e(substr($ret['reason'],0,80)) ?></div>
          </td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af"><?= formatDate($ret['created_at']) ?></td>
          <td style="padding:10px 16px">
            <?php
            $badgeMap = ['pending'=>'b-warning','approved'=>'b-success','rejected'=>'b-danger','completed'=>'b-info'];
            $labelMap = ['pending'=>'Bekliyor','approved'=>'Onaylandı','rejected'=>'Reddedildi','completed'=>'Tamamlandı'];
            ?>
            <span class="badge <?= $badgeMap[$ret['status']]??'b-gray' ?>"><?= $labelMap[$ret['status']]??$ret['status'] ?></span>
          </td>
          <td style="padding:10px 16px">
            <a href="<?= adminUrl('iadeler/'.$ret['id']) ?>" class="btn btn-outline btn-sm">Detay</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); require APP_PATH.'/Views/Admin/layouts/main.php'; ?>
