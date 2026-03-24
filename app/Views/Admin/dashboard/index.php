<?php
ob_start();
?>

<!-- Stat Kartlar -->
<div class="stat-grid">

  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Toplam Satış</span>
      <div class="stat-icon si-blue">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/></svg>
      </div>
    </div>
    <div class="stat-value"><?= formatPriceTRY($stats['monthly_total'] ?? 0) ?></div>
    <div class="stat-sub">Bu ay toplam satış</div>
  </div>

  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Bugünkü Sipariş</span>
      <div class="stat-icon si-green">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
      </div>
    </div>
    <div class="stat-value"><?= $stats['today_count'] ?? 0 ?></div>
    <div class="stat-sub">Bugün alınan sipariş</div>
  </div>

  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Bekleyen Sipariş</span>
      <div class="stat-icon si-amber">
        <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
      </div>
    </div>
    <div class="stat-value"><?= $stats['pending_count'] ?? 0 ?></div>
    <div class="stat-sub">İşlem bekliyor</div>
  </div>

  <div class="stat-card">
    <div class="stat-top">
      <span class="stat-label">Bugünkü Satış</span>
      <div class="stat-icon si-purple">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
      </div>
    </div>
    <div class="stat-value"><?= formatPriceTRY($stats['today_total'] ?? 0) ?></div>
    <div class="stat-sub">Bugün ciro</div>
  </div>

</div>

<!-- Grafik + Hızlı Erişim -->
<div style="display:grid;grid-template-columns:1.6fr 1fr;gap:12px;margin-bottom:16px">

  <!-- Satis Grafigi -->
  <div class="card">
    <div class="card-header">
      <span class="card-title">Son 30 Gün Satış</span>
      <a href="<?= adminUrl('raporlar/satis') ?>" class="card-link">Detaylı Rapor →</a>
    </div>
    <?php
    $salesData = \App\Core\Database::rows(
        "SELECT DATE(created_at) as date, COALESCE(SUM(total), 0) as total
         FROM orders
         WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
           AND payment_status = 'paid'
         GROUP BY DATE(created_at)
         ORDER BY date ASC"
    );

    // 30 günlük dizi oluştur
    $salesMap = [];
    foreach ($salesData as $row) {
        $salesMap[$row['date']] = (float) $row['total'];
    }

    $chartData = [];
    $chartLabels = [];
    for ($i = 29; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $chartData[] = $salesMap[$date] ?? 0;
        $chartLabels[] = date('d', strtotime($date));
    }

    $maxVal = max($chartData) ?: 1;
    ?>
    <div style="height:120px;display:flex;align-items:flex-end;gap:3px;padding-top:8px" id="chartBars">
      <?php foreach ($chartData as $i => $val): ?>
        <?php $height = max(4, round(($val / $maxVal) * 100)); ?>
        <?php $isToday = $i === 29; ?>
        <div style="flex:1;display:flex;flex-direction:column;align-items:center;">
          <div
            title="<?= date('d M', strtotime("-" . (29 - $i) . " days")) ?>: <?= formatPriceTRY($val) ?>"
            style="width:100%;height:<?= $height ?>%;border-radius:3px 3px 0 0;background:<?= $isToday ? '#2563eb' : ($val > 0 ? '#bfdbfe' : '#f3f4f6') ?>;cursor:pointer;transition:opacity .15s;"
            onmouseover="this.style.opacity='.7'"
            onmouseout="this.style.opacity='1'"
          ></div>
        </div>
      <?php endforeach; ?>
    </div>
    <div style="display:flex;gap:3px;margin-top:5px">
      <?php foreach ($chartLabels as $i => $label): ?>
        <div style="flex:1;text-align:center;font-size:9px;color:#9ca3af">
          <?= in_array($i, [0, 7, 14, 21, 29]) ? $label : '' ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Sag kolon -->
  <div style="display:flex;flex-direction:column;gap:12px">

    <!-- Hizli Erisim -->
    <div class="card">
      <div class="card-header">
        <span class="card-title">Hızlı Erişim</span>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
        <?php
        $shortcuts = [
          ['url' => adminUrl('urunler/ekle'),    'label' => 'Ürün Ekle',    'bg' => '#eff6ff', 'color' => '#2563eb'],
          ['url' => adminUrl('siparisler'),       'label' => 'Siparişler',   'bg' => '#f0fdf4', 'color' => '#16a34a'],
          ['url' => adminUrl('kategoriler/ekle'), 'label' => 'Kategori',     'bg' => '#fffbeb', 'color' => '#d97706'],
          ['url' => adminUrl('raporlar/satis'),   'label' => 'Raporlar',     'bg' => '#f5f3ff', 'color' => '#7c3aed'],
        ];
        ?>
        <?php foreach ($shortcuts as $s): ?>
          <a href="<?= $s['url'] ?>" style="
            display:flex;align-items:center;justify-content:center;
            padding:10px 8px;border-radius:8px;
            background:<?= $s['bg'] ?>;color:<?= $s['color'] ?>;
            text-decoration:none;font-size:11px;font-weight:600;
            border:1px solid transparent;transition:all .15s;text-align:center;
          "
          onmouseover="this.style.opacity='.8'"
          onmouseout="this.style.opacity='1'">
            <?= e($s['label']) ?>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Son Aktiviteler -->
    <div class="card" style="flex:1">
      <div class="card-header">
        <span class="card-title">Son Aktiviteler</span>
      </div>
      <?php
      $activities = \App\Core\Database::rows(
          "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 5"
      );
      ?>
      <?php if (empty($activities)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Henüz aktivite yok</p>
      <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:10px">
          <?php foreach ($activities as $act): ?>
            <div style="display:flex;align-items:flex-start;gap:8px">
              <div style="width:7px;height:7px;border-radius:50%;background:#2563eb;margin-top:5px;flex-shrink:0"></div>
              <div>
                <div style="font-size:12px;color:#374151;line-height:1.4"><?= e($act['action']) ?></div>
                <div style="font-size:11px;color:#9ca3af;margin-top:1px"><?= timeAgo($act['created_at']) ?></div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<!-- Son Siparisler -->
<div class="card">
  <div class="card-header">
    <span class="card-title">Son Siparişler</span>
    <a href="<?= adminUrl('siparisler') ?>" class="card-link">Tümünü Gör →</a>
  </div>
  <?php
  $recentOrders = \App\Core\Database::rows(
      "SELECT o.*,
              COALESCE(CONCAT(u.name, ' ', u.surname), o.guest_email, 'Misafir') as customer_name
       FROM orders o
       LEFT JOIN users u ON u.id = o.user_id
       ORDER BY o.id DESC LIMIT 8"
  );
  ?>
  <?php if (empty($recentOrders)): ?>
    <p style="font-size:13px;color:#9ca3af;text-align:center;padding:2rem 0">Henüz sipariş bulunmuyor</p>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Sipariş No</th>
            <th>Müşteri</th>
            <th>Tutar</th>
            <th>Ödeme</th>
            <th>Durum</th>
            <th>Tarih</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentOrders as $order): ?>
          <tr>
            <td style="font-weight:600;color:#2563eb">
              <a href="<?= adminUrl('siparisler/' . $order['id']) ?>" style="color:inherit;text-decoration:none">
                <?= e($order['order_no']) ?>
              </a>
            </td>
            <td style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
              <?= e(truncate($order['customer_name'], 22)) ?>
            </td>
            <td style="font-weight:600"><?= formatPriceTRY($order['total']) ?></td>
            <td>
              <span class="badge <?= $order['payment_status'] === 'paid' ? 'b-success' : 'b-warning' ?>">
                <?= $order['payment_status'] === 'paid' ? 'Ödendi' : 'Bekliyor' ?>
              </span>
            </td>
            <td>
              <span class="badge b-<?= orderStatusColor($order['status']) ?>">
                <?= orderStatusLabel($order['status']) ?>
              </span>
            </td>
            <td style="color:#9ca3af;white-space:nowrap"><?= formatDate($order['created_at']) ?></td>
            <td>
              <a href="<?= adminUrl('siparisler/' . $order['id']) ?>" class="btn btn-outline btn-sm">Detay</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();

$extraStyles = '<style>
@media (max-width: 900px) {
  div[style*="grid-template-columns: 1.6fr 1fr"] {
    grid-template-columns: 1fr !important;
  }
}
</style>';

require APP_PATH . '/Views/Admin/layouts/main.php';
?>
