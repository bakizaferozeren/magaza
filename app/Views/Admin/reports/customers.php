<?php ob_start(); ?>
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Müşteri Raporları</span>
  <form method="GET" style="display:flex;gap:6px;align-items:center">
    <input type="date" name="from" value="<?= $from ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
    <span style="font-size:12px;color:#9ca3af">—</span>
    <input type="date" name="to" value="<?= $to ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
  </form>
</div>
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
  <!-- Yeni Müşteriler Grafik -->
  <div class="card">
    <div class="card-header"><span class="card-title">Yeni Üyeler</span></div>
    <div class="card-body">
      <?php if (empty($newCustomers)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Veri yok</p>
      <?php else: ?>
        <?php $max = max(array_column($newCustomers,'count')?:[1]); ?>
        <div style="display:flex;align-items:flex-end;gap:3px;height:80px">
          <?php foreach ($newCustomers as $d): ?>
            <?php $h = max(4, round(($d['count']/$max)*100)); ?>
            <div style="flex:1;height:<?= $h ?>%;background:#2563eb;border-radius:2px 2px 0 0;min-height:4px"
                 title="<?= date('d M',strtotime($d['date'])) ?>: <?= $d['count'] ?> yeni üye"></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
  <!-- En Çok Harcayan -->
  <div class="card">
    <div class="card-header"><span class="card-title">En Çok Harcayan (İlk 5)</span></div>
    <div class="card-body">
      <?php if (empty($topCustomers)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Veri yok</p>
      <?php else: ?>
        <?php $max = max(array_column($topCustomers,'total_spent')?:[1]); ?>
        <div style="display:flex;flex-direction:column;gap:8px">
          <?php foreach (array_slice($topCustomers,0,5) as $c): ?>
            <div>
              <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
                <span><?= e($c['name'].' '.$c['surname']) ?></span>
                <span style="font-weight:600"><?= formatPriceTRY($c['total_spent']) ?></span>
              </div>
              <div style="height:5px;background:#f3f4f6;border-radius:3px">
                <div style="height:100%;background:#7c3aed;border-radius:3px;width:<?= round(($c['total_spent']/$max)*100) ?>%"></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<!-- Tam Liste -->
<div class="card" style="padding:0">
  <div class="card-header"><span class="card-title">En Çok Harcayan Müşteriler</span></div>
  <?php if (empty($topCustomers)): ?>
    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Bu dönemde veri yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead><tr>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">#</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Müşteri</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sipariş</th>
        <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Toplam</th>
      </tr></thead>
      <tbody>
        <?php foreach ($topCustomers as $i => $c): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-weight:700;color:<?= $i<3?'#2563eb':'#9ca3af' ?>"><?= $i+1 ?></td>
          <td style="padding:10px 16px">
            <div style="font-weight:500"><?= e($c['name'].' '.$c['surname']) ?></div>
            <div style="font-size:11px;color:#9ca3af"><?= e($c['email']) ?></div>
          </td>
          <td style="padding:10px 16px"><?= $c['order_count'] ?> sipariş</td>
          <td style="padding:10px 16px;font-weight:600;color:#2563eb"><?= formatPriceTRY($c['total_spent']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
