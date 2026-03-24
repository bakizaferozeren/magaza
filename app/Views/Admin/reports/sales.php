<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Satış Raporları</span>
  <div style="display:flex;gap:8px;align-items:center">
    <form method="GET" style="display:flex;gap:6px;align-items:center">
      <input type="date" name="from" value="<?= $from ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
      <span style="font-size:12px;color:#9ca3af">—</span>
      <input type="date" name="to" value="<?= $to ?>" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
    </form>
    <a href="<?= adminUrl('raporlar/satis/csv?from='.$from.'&to='.$to) ?>" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      CSV İndir
    </a>
  </div>
</div>

<!-- Hızlı Filtreler -->
<div style="display:flex;gap:6px;margin-bottom:16px;flex-wrap:wrap">
  <?php
  $quickFilters = [
    ['label'=>'Bugün',     'from'=>date('Y-m-d'),          'to'=>date('Y-m-d')],
    ['label'=>'Bu Hafta',  'from'=>date('Y-m-d',strtotime('monday this week')), 'to'=>date('Y-m-d')],
    ['label'=>'Bu Ay',     'from'=>date('Y-m-01'),         'to'=>date('Y-m-d')],
    ['label'=>'Geçen Ay',  'from'=>date('Y-m-01',strtotime('first day of last month')), 'to'=>date('Y-m-t',strtotime('first day of last month'))],
    ['label'=>'Bu Yıl',    'from'=>date('Y-01-01'),        'to'=>date('Y-m-d')],
  ];
  ?>
  <?php foreach ($quickFilters as $qf): ?>
    <a href="?from=<?= $qf['from'] ?>&to=<?= $qf['to'] ?>"
       class="btn btn-sm <?= ($from===$qf['from']&&$to===$qf['to'])?'btn-primary':'btn-outline' ?>">
      <?= $qf['label'] ?>
    </a>
  <?php endforeach; ?>
</div>

<!-- Özet Kartlar -->
<?php
$prevRevenue = (float)($prevSummary['total_revenue'] ?? 0);
$currRevenue = (float)($summary['total_revenue'] ?? 0);
$revenueChange = $prevRevenue > 0 ? round((($currRevenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;

$prevOrders = (int)($prevSummary['total_orders'] ?? 0);
$currOrders = (int)($summary['total_orders'] ?? 0);
$ordersChange = $prevOrders > 0 ? round((($currOrders - $prevOrders) / $prevOrders) * 100, 1) : 0;
?>
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Toplam Ciro</div>
    <div style="font-size:22px;font-weight:700"><?= formatPriceTRY($currRevenue) ?></div>
    <div style="font-size:11px;margin-top:4px;color:<?= $revenueChange>=0?'#16a34a':'#dc2626' ?>">
      <?= $revenueChange>=0?'↑':'↓' ?> %<?= abs($revenueChange) ?> önceki döneme göre
    </div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Sipariş Sayısı</div>
    <div style="font-size:22px;font-weight:700"><?= number_format($currOrders) ?></div>
    <div style="font-size:11px;margin-top:4px;color:<?= $ordersChange>=0?'#16a34a':'#dc2626' ?>">
      <?= $ordersChange>=0?'↑':'↓' ?> %<?= abs($ordersChange) ?> önceki döneme göre
    </div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Ortalama Sepet</div>
    <div style="font-size:22px;font-weight:700"><?= formatPriceTRY($summary['avg_order'] ?? 0) ?></div>
    <div style="font-size:11px;margin-top:4px;color:#9ca3af">sipariş başına</div>
  </div>
  <div class="card" style="padding:14px 16px">
    <div style="font-size:12px;color:#6b7280;margin-bottom:8px">Toplam İndirim</div>
    <div style="font-size:22px;font-weight:700"><?= formatPriceTRY($summary['total_discount'] ?? 0) ?></div>
    <div style="font-size:11px;margin-top:4px;color:#9ca3af">kupon + indirimler</div>
  </div>
</div>

<!-- Grafik + Durum -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:16px">

  <!-- Günlük Satış Grafiği -->
  <div class="card">
    <div class="card-header"><span class="card-title">Günlük Satış</span></div>
    <div class="card-body">
      <?php if (empty($daily)): ?>
        <p style="font-size:13px;color:#9ca3af;text-align:center;padding:2rem 0">Bu dönemde satış verisi yok</p>
      <?php else: ?>
        <?php
        $maxRev = max(array_column($daily, 'revenue') ?: [1]);
        ?>
        <div style="display:flex;align-items:flex-end;gap:2px;height:140px;padding-top:8px">
          <?php foreach ($daily as $d): ?>
            <?php $h = max(4, round(($d['revenue'] / $maxRev) * 100)); ?>
            <div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px">
              <div title="<?= date('d M',strtotime($d['date'])) ?>: <?= formatPriceTRY($d['revenue']) ?> (<?= $d['order_count'] ?> sipariş)"
                   style="width:100%;height:<?= $h ?>%;background:#2563eb;border-radius:2px 2px 0 0;min-height:4px;cursor:pointer;transition:opacity .15s"
                   onmouseover="this.style.opacity='.7'" onmouseout="this.style.opacity='1'"></div>
            </div>
          <?php endforeach; ?>
        </div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;font-size:10px;color:#9ca3af">
          <span><?= date('d M', strtotime($from)) ?></span>
          <span><?= date('d M', strtotime($to)) ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Durum Dağılımı -->
  <div class="card">
    <div class="card-header"><span class="card-title">Sipariş Durumları</span></div>
    <div class="card-body">
      <?php if (empty($byStatus)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Veri yok</p>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px">
          <?php foreach ($byStatus as $s): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;font-size:12px">
              <div style="display:flex;align-items:center;gap:6px">
                <span class="badge b-<?= orderStatusColor($s['status']) ?>" style="font-size:10px"><?= orderStatusLabel($s['status']) ?></span>
              </div>
              <div style="text-align:right">
                <div style="font-weight:600"><?= $s['count'] ?> sipariş</div>
                <div style="font-size:11px;color:#9ca3af"><?= formatPriceTRY($s['revenue']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<!-- Detay Tablo -->
<div class="card">
  <div class="card-header"><span class="card-title">Günlük Detay</span></div>
  <?php if (empty($daily)): ?>
    <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Bu dönemde sipariş yok</div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Tarih</th>
            <th>Sipariş</th>
            <th>Ciro</th>
            <th>Ort. Sepet</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach (array_reverse($daily) as $d): ?>
          <tr>
            <td><?= date('d M Y', strtotime($d['date'])) ?></td>
            <td><?= $d['order_count'] ?></td>
            <td style="font-weight:600"><?= formatPriceTRY($d['revenue']) ?></td>
            <td><?= formatPriceTRY($d['avg_order']) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr style="background:#f9fafb;font-weight:600;border-top:2px solid #e5e7eb">
            <td>TOPLAM</td>
            <td><?= array_sum(array_column($daily,'order_count')) ?></td>
            <td><?= formatPriceTRY(array_sum(array_column($daily,'revenue'))) ?></td>
            <td><?= formatPriceTRY($summary['avg_order'] ?? 0) ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
@media(max-width:900px){
  div[style*="grid-template-columns: repeat(4"]{grid-template-columns:repeat(2,1fr)!important}
  div[style*="grid-template-columns: 2fr 1fr"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
