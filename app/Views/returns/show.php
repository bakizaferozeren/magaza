<?php ob_start(); ?>

<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
  <a href="<?= adminUrl('iadeler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
    İadeler
  </a>
  <span style="font-size:15px;font-weight:700">İade Talebi — <?= e($return['order_no']) ?></span>
  <?php
  $badgeMap = ['pending'=>'b-warning','approved'=>'b-success','rejected'=>'b-danger','completed'=>'b-info'];
  $labelMap = ['pending'=>'Bekliyor','approved'=>'Onaylandı','rejected'=>'Reddedildi','completed'=>'Tamamlandı'];
  ?>
  <span class="badge <?= $badgeMap[$return['status']]??'b-gray' ?>"><?= $labelMap[$return['status']]??$return['status'] ?></span>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:16px">
<div>
  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">İade Sebebi</span></div>
    <div class="card-body">
      <p style="font-size:13px;color:#374151;line-height:1.6"><?= nl2br(e($return['reason'])) ?></p>
      <?php if ($return['admin_note']): ?>
        <div style="margin-top:12px;padding:10px 12px;background:#f9fafb;border-radius:8px;font-size:12px;color:#6b7280">
          <strong>Admin Notu:</strong> <?= e($return['admin_note']) ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card" style="margin-bottom:12px">
    <div class="card-header"><span class="card-title">Sipariş Ürünleri</span></div>
    <div class="card-body">
      <?php foreach ($items as $item): ?>
        <div style="display:flex;justify-content:space-between;padding:6px 0;border-bottom:1px solid #f9fafb;font-size:13px">
          <span><?= e($item['product_name']??$item['name']) ?></span>
          <span style="font-weight:500"><?= $item['quantity'] ?> × <?= formatPriceTRY($item['price']) ?></span>
        </div>
      <?php endforeach; ?>
      <div style="display:flex;justify-content:flex-end;margin-top:8px;font-size:14px;font-weight:700">
        Toplam: <?= formatPriceTRY($return['total']) ?>
      </div>
    </div>
  </div>
</div>

<div style="display:flex;flex-direction:column;gap:12px">
  <div class="card">
    <div class="card-header"><span class="card-title">Müşteri</span></div>
    <div class="card-body">
      <div style="font-size:13px;font-weight:500"><?= e($return['customer_name']) ?></div>
      <div style="font-size:12px;color:#9ca3af;margin-top:2px"><?= e($return['customer_email']??'') ?></div>
      <div style="font-size:12px;color:#9ca3af;margin-top:2px"><?= formatDate($return['created_at']) ?></div>
    </div>
  </div>

  <?php if ($return['status'] === 'pending'): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">İşlem</span></div>
    <div class="card-body">
      <form method="POST" action="<?= adminUrl('iadeler/'.$return['id'].'/onayla') ?>" style="margin-bottom:8px">
        <?= csrfField() ?>
        <div class="form-group">
          <label>Not (opsiyonel)</label>
          <textarea name="note" rows="2" placeholder="Müşteriye not..."></textarea>
        </div>
        <button type="submit" class="btn btn-primary btn-sm" style="width:100%;justify-content:center">İadeyi Onayla</button>
      </form>
      <form method="POST" action="<?= adminUrl('iadeler/'.$return['id'].'/reddet') ?>">
        <?= csrfField() ?>
        <div class="form-group">
          <label>Red Sebebi</label>
          <textarea name="note" rows="2" placeholder="Red sebebini açıklayın..." required></textarea>
        </div>
        <button type="submit" class="btn btn-outline btn-sm" style="width:100%;justify-content:center;color:#dc2626;border-color:#fecaca">Reddet</button>
      </form>
    </div>
  </div>
  <?php endif; ?>
</div>
</div>

<?php
$content=ob_get_clean();
$extraStyles='<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:10px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}textarea{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;resize:vertical}</style>';
require APP_PATH.'/Views/Admin/layouts/main.php';
?>
