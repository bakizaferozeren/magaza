<?php ob_start(); ?>

<!-- Stat Kartlar -->
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px">
  <?php
  $statCards = [
    ['label'=>'Bekleyen',    'value'=>$stats['pending'],    'color'=>'#d97706','bg'=>'#fffbeb','icon'=>'<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>'],
    ['label'=>'İşlemde',     'value'=>$stats['processing'], 'color'=>'#2563eb','bg'=>'#eff6ff','icon'=>'<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>'],
    ['label'=>'Kargoda',     'value'=>$stats['shipped'],    'color'=>'#7c3aed','bg'=>'#f5f3ff','icon'=>'<path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/><path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3z"/><path d="M14 7h1.05A2.5 2.5 0 0117 8.95V14a1 1 0 01-1 1h-.05a2.5 2.5 0 00-4.9 0H11V7h3z"/>'],
    ['label'=>'Bugün',       'value'=>$stats['today'],      'color'=>'#16a34a','bg'=>'#f0fdf4','icon'=>'<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>'],
  ];
  ?>
  <?php foreach ($statCards as $sc): ?>
  <div class="card" style="padding:14px 16px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
      <span style="font-size:12px;color:#6b7280;font-weight:500"><?= $sc['label'] ?></span>
      <div style="width:30px;height:30px;border-radius:8px;background:<?= $sc['bg'] ?>;display:flex;align-items:center;justify-content:center">
        <svg width="15" height="15" viewBox="0 0 20 20" fill="<?= $sc['color'] ?>"><?= $sc['icon'] ?></svg>
      </div>
    </div>
    <div style="font-size:24px;font-weight:700;color:#1a1a1a"><?= $sc['value'] ?></div>
  </div>
  <?php endforeach; ?>
</div>

<!-- Toolbar -->
<div class="card" style="padding:12px 16px;margin-bottom:12px">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <form method="GET" action="<?= adminUrl('siparisler') ?>" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <input type="text" name="search" value="<?= e($search) ?>" placeholder="Sipariş no, müşteri, e-posta..."
        style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:220px">

      <select name="status" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none;color:#374151">
        <option value="">Tüm Durumlar</option>
        <?php foreach(['pending'=>'Bekleyen','processing'=>'İşlemde','shipped'=>'Kargoda','delivered'=>'Teslim Edildi','cancelled'=>'İptal','refunded'=>'İade'] as $v=>$l): ?>
          <option value="<?= $v ?>" <?= ($filters['status']??'')===$v?'selected':'' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select>

      <select name="payment" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none;color:#374151">
        <option value="">Tüm Ödeme</option>
        <option value="paid"    <?= ($filters['payment']??'')==='paid'   ?'selected':'' ?>>Ödendi</option>
        <option value="pending" <?= ($filters['payment']??'')==='pending'?'selected':'' ?>>Bekliyor</option>
        <option value="failed"  <?= ($filters['payment']??'')==='failed' ?'selected':'' ?>>Başarısız</option>
      </select>

      <input type="date" name="date_from" value="<?= e($filters['dateFrom']??'') ?>"
        style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">
      <input type="date" name="date_to" value="<?= e($filters['dateTo']??'') ?>"
        style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none" onchange="this.form.submit()">

      <button type="submit" class="btn btn-outline btn-sm">Ara</button>
      <?php if ($search || array_filter($filters)): ?>
        <a href="<?= adminUrl('siparisler') ?>" class="btn btn-outline btn-sm">Temizle</a>
      <?php endif; ?>
    </form>

    <a href="<?= adminUrl('siparisler/csv-indir') ?>" class="btn btn-outline btn-sm">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
      CSV İndir
    </a>
  </div>
</div>

<!-- Toplu Işlem -->
<form method="POST" action="<?= adminUrl('siparisler/toplu-guncelle') ?>" id="bulkForm">
  <?= csrfField() ?>

  <div id="bulkBar" style="display:none;background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:10px 16px;margin-bottom:10px;align-items:center;gap:12px">
    <span id="bulkCount" style="font-size:13px;font-weight:500;color:#1d4ed8"></span>
    <select name="action" style="padding:5px 10px;border:1px solid #bfdbfe;border-radius:6px;font-size:12px;outline:none">
      <option value="">İşlem Seç</option>
      <option value="processing">İşleme Al</option>
      <option value="shipped">Kargoya Ver</option>
      <option value="delivered">Teslim Edildi</option>
      <option value="cancelled">İptal Et</option>
    </select>
    <button type="submit" class="btn btn-primary btn-sm" onclick="return confirmBulk()">Uygula</button>
    <button type="button" onclick="clearSelection()" class="btn btn-outline btn-sm">İptal</button>
  </div>

  <!-- Tablo -->
  <div class="card" style="padding:0">
    <?php if (empty($orders)): ?>
      <div style="text-align:center;padding:3rem;color:#9ca3af">
        <svg viewBox="0 0 20 20" fill="currentColor" style="width:40px;height:40px;margin:0 auto 12px;opacity:.3;display:block"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
        <p style="font-size:14px;font-weight:500;color:#6b7280">Sipariş bulunamadı</p>
      </div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th style="width:40px"><input type="checkbox" id="selectAll" style="width:14px;height:14px;accent-color:#2563eb" onchange="toggleAll(this)"></th>
              <th>Sipariş No</th>
              <th>Müşteri</th>
              <th>Ürünler</th>
              <th>Tutar</th>
              <th>Ödeme</th>
              <th>Durum</th>
              <th>Tarih</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
              <td><input type="checkbox" name="ids[]" value="<?= $order['id'] ?>" class="row-check" style="width:14px;height:14px;accent-color:#2563eb" onchange="updateBulkBar()"></td>
              <td>
                <a href="<?= adminUrl('siparisler/'.$order['id']) ?>" style="font-weight:600;color:#2563eb;text-decoration:none;font-size:13px">
                  <?= e($order['order_no']) ?>
                </a>
              </td>
              <td>
                <div style="font-size:13px;font-weight:500;color:#1a1a1a;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($order['customer_name']) ?></div>
                <div style="font-size:11px;color:#9ca3af;max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e($order['customer_email']) ?></div>
              </td>
              <td style="font-size:12px;color:#6b7280"><?= $order['item_count'] ?> ürün</td>
              <td style="font-weight:600;font-size:13px"><?= formatPriceTRY($order['total']) ?></td>
              <td>
                <span class="badge <?= $order['payment_status']==='paid'?'b-success':($order['payment_status']==='failed'?'b-danger':'b-warning') ?>">
                  <?= $order['payment_status']==='paid'?'Ödendi':($order['payment_status']==='failed'?'Başarısız':'Bekliyor') ?>
                </span>
              </td>
              <td>
                <span class="badge b-<?= orderStatusColor($order['status']) ?>">
                  <?= orderStatusLabel($order['status']) ?>
                </span>
              </td>
              <td style="font-size:12px;color:#9ca3af;white-space:nowrap"><?= formatDate($order['created_at']) ?></td>
              <td>
                <a href="<?= adminUrl('siparisler/'.$order['id']) ?>" class="btn btn-outline btn-sm">Detay</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Sayfalama -->
      <?php if ($pagination['last_page'] > 1): ?>
      <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #f3f4f6">
        <span style="font-size:12px;color:#6b7280"><?= $pagination['from'] ?>–<?= $pagination['to'] ?> / <?= $pagination['total'] ?> sipariş</span>
        <div style="display:flex;gap:4px">
          <?php if ($pagination['current_page'] > 1): ?>
            <a href="?page=<?= $pagination['current_page']-1 ?>" class="btn btn-outline btn-sm">←</a>
          <?php endif; ?>
          <?php for ($p=max(1,$pagination['current_page']-2);$p<=min($pagination['last_page'],$pagination['current_page']+2);$p++): ?>
            <a href="?page=<?= $p ?>" class="btn btn-sm <?= $p===$pagination['current_page']?'btn-primary':'btn-outline' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <?php if ($pagination['current_page'] < $pagination['last_page']): ?>
            <a href="?page=<?= $pagination['current_page']+1 ?>" class="btn btn-outline btn-sm">→</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</form>

<script>
function toggleAll(cb) {
  document.querySelectorAll('.row-check').forEach(c => c.checked = cb.checked);
  updateBulkBar();
}

function updateBulkBar() {
  const checked = document.querySelectorAll('.row-check:checked');
  const bar = document.getElementById('bulkBar');
  const cnt = document.getElementById('bulkCount');
  bar.style.display = checked.length > 0 ? 'flex' : 'none';
  cnt.textContent   = checked.length + ' sipariş seçildi';
}

function clearSelection() {
  document.querySelectorAll('.row-check,#selectAll').forEach(c => c.checked = false);
  document.getElementById('bulkBar').style.display = 'none';
}

function confirmBulk() {
  const action = document.querySelector('[name="action"]').value;
  if (!action) { alert('Lütfen bir işlem seçin.'); return false; }
  const cnt = document.querySelectorAll('.row-check:checked').length;
  return confirm(cnt + ' sipariş için "' + action + '" işlemi uygulanacak. Onaylıyor musunuz?');
}
</script>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
