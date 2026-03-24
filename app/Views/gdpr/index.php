<?php ob_start(); ?>

<div style="margin-bottom:16px">
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:12px 16px;font-size:13px;color:#92400e;display:flex;align-items:center;gap:8px">
    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
    KVKK silme taleplerinde 30 günlük bekleme süresi uygulanmaktadır. Onaylanan talepler otomatik olarak planlanır.
  </div>
</div>

<div class="card" style="padding:0">
  <?php if (empty($requests)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">KVKK talebi bulunamadı</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Kişi</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Talep Tipi</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Talep Tarihi</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">İşlem Tarihi</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Durum</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($requests as $req): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:500"><?= e($req['requester']) ?></td>
          <td style="padding:10px 16px">
            <span class="badge <?= $req['type']==='delete'?'b-danger':'b-info' ?>">
              <?= $req['type']==='delete' ? 'Silme' : 'İndirme' ?>
            </span>
          </td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af"><?= formatDate($req['created_at']) ?></td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af">
            <?= $req['scheduled_at'] ? formatDate($req['scheduled_at']) : '—' ?>
          </td>
          <td style="padding:10px 16px">
            <?php
            $sm = ['pending'=>'b-warning','approved'=>'b-success','rejected'=>'b-danger','completed'=>'b-info'];
            $sl = ['pending'=>'Bekliyor','approved'=>'Onaylandı','rejected'=>'Reddedildi','completed'=>'Tamamlandı'];
            ?>
            <span class="badge <?= $sm[$req['status']]??'b-gray' ?>"><?= $sl[$req['status']]??$req['status'] ?></span>
          </td>
          <td style="padding:10px 16px">
            <?php if ($req['status'] === 'pending'): ?>
              <div style="display:flex;gap:6px">
                <form method="POST" action="<?= adminUrl('kvkk/'.$req['id'].'/onayla') ?>">
                  <?= csrfField() ?>
                  <button class="btn btn-outline btn-sm" style="color:#16a34a;border-color:#bbf7d0">Onayla</button>
                </form>
                <form method="POST" action="<?= adminUrl('kvkk/'.$req['id'].'/reddet') ?>">
                  <?= csrfField() ?>
                  <input type="hidden" name="note" value="Talebiniz reddedilmiştir.">
                  <button class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Reddet</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); require APP_PATH.'/Views/Admin/layouts/main.php'; ?>
