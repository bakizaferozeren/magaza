<?php
ob_start();
$tab = \App\Core\Request::get('tab', 'missing');
?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <span style="font-size:15px;font-weight:700">Faturalar</span>
</div>

<!-- Sekmeler -->
<div style="display:flex;border-bottom:2px solid #f3f4f6;margin-bottom:16px">
  <?php
  $missingCount = (int)\App\Core\Database::value(
    "SELECT COUNT(*) FROM orders o
     WHERE o.payment_status='paid' AND o.status NOT IN ('cancelled','refunded')
     AND NOT EXISTS (SELECT 1 FROM invoices i WHERE i.order_id=o.id AND i.type IN ('e_invoice','e_archive'))"
  );
  ?>
  <a href="?tab=missing" style="padding:10px 18px;font-size:13px;font-weight:500;text-decoration:none;border:none;background:none;cursor:pointer;color:<?= $tab==='missing'?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $tab==='missing'?'#2563eb':'transparent' ?>;margin-bottom:-2px;display:flex;align-items:center;gap:6px">
    Fatura Bekleyenler
    <?php if ($missingCount > 0): ?>
      <span style="background:#dc2626;color:#fff;font-size:10px;border-radius:10px;padding:1px 7px;font-weight:600"><?= $missingCount ?></span>
    <?php endif; ?>
  </a>
  <a href="?tab=all" style="padding:10px 18px;font-size:13px;font-weight:500;text-decoration:none;color:<?= $tab==='all'?'#2563eb':'#6b7280' ?>;border-bottom:2px solid <?= $tab==='all'?'#2563eb':'transparent' ?>;margin-bottom:-2px">
    Tüm Faturalar
  </a>
</div>

<?php if ($tab === 'missing'): ?>

<?php
$missingOrders = \App\Core\Database::rows(
  "SELECT o.id, o.order_no, o.total, o.created_at,
          COALESCE(CONCAT(u.name,' ',u.surname), o.shipping_name, 'Misafir') as customer_name,
          COALESCE(u.email, o.guest_email) as customer_email,
          (SELECT GROUP_CONCAT(type SEPARATOR ',') FROM invoices WHERE order_id=o.id) as existing_types
   FROM orders o
   LEFT JOIN users u ON u.id = o.user_id
   WHERE o.payment_status='paid' AND o.status NOT IN ('cancelled','refunded')
   AND NOT EXISTS (SELECT 1 FROM invoices i WHERE i.order_id=o.id AND i.type IN ('e_invoice','e_archive'))
   ORDER BY o.id DESC LIMIT 100"
);
?>

<?php if (empty($missingOrders)): ?>
  <div class="card" style="text-align:center;padding:3rem;color:#16a34a">
    <svg viewBox="0 0 20 20" fill="currentColor" style="width:40px;height:40px;margin:0 auto 12px;display:block">
      <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
    </svg>
    <p style="font-size:14px;font-weight:600;color:#16a34a">Tüm siparişlerin faturası yüklenmiş!</p>
    <p style="font-size:12px;color:#9ca3af;margin-top:4px">Bekleyen fatura yok.</p>
  </div>
<?php else: ?>
  <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:10px 14px;margin-bottom:12px;display:flex;align-items:center;gap:8px">
    <svg viewBox="0 0 20 20" fill="#d97706" style="width:16px;height:16px;flex-shrink:0">
      <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
    </svg>
    <span style="font-size:12px;color:#92400e"><strong><?= count($missingOrders) ?> sipariş</strong> için e-fatura veya e-arşiv fatura henüz yüklenmemiş.</span>
  </div>
  <div class="card" style="padding:0">
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sipariş No</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Müşteri</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tutar</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Mevcut</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($missingOrders as $o): ?>
        <tr style="border-bottom:1px solid #f9fafb" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
          <td style="padding:10px 16px">
            <a href="<?= adminUrl('siparisler/'.$o['id']) ?>" style="font-weight:600;color:#2563eb;text-decoration:none"><?= e($o['order_no']) ?></a>
          </td>
          <td style="padding:10px 16px">
            <div style="font-weight:500"><?= e($o['customer_name']) ?></div>
            <div style="font-size:11px;color:#9ca3af"><?= e($o['customer_email']) ?></div>
          </td>
          <td style="padding:10px 16px;font-weight:600"><?= formatPriceTRY($o['total']) ?></td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af"><?= formatDate($o['created_at']) ?></td>
          <td style="padding:10px 16px">
            <?php if ($o['existing_types']): ?>
              <?php $tl=['proforma'=>'Proforma','e_invoice'=>'E-Fatura','e_archive'=>'E-Arşiv','return'=>'İade','cancel'=>'İptal']; ?>
              <?php foreach (explode(',', $o['existing_types']) as $t): ?>
                <span class="badge b-info" style="font-size:10px;margin-right:2px"><?= $tl[$t]??$t ?></span>
              <?php endforeach; ?>
            <?php else: ?>
              <span style="font-size:11px;color:#9ca3af">Yok</span>
            <?php endif; ?>
          </td>
          <td style="padding:10px 16px">
            <button onclick="openUpload(<?= $o['id'] ?>, '<?= e($o['order_no']) ?>')" class="btn btn-primary btn-sm">Fatura Yükle</button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php else: ?>

<!-- TÜM FATURALAR -->
<div class="card" style="padding:0">
  <?php if (empty($invoices)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Henüz fatura yok</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Fatura No</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Sipariş</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Müşteri</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tip</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;border-bottom:1px solid #f3f4f6">Tarih</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-family:monospace;font-weight:600"><?= e($inv['invoice_no'] ?? '—') ?></td>
          <td style="padding:10px 16px">
            <a href="<?= adminUrl('siparisler/'.$inv['order_id']) ?>" style="color:#2563eb;text-decoration:none;font-weight:500"><?= e($inv['order_no']) ?></a>
          </td>
          <td style="padding:10px 16px"><?= e($inv['customer_name']) ?></td>
          <td style="padding:10px 16px">
            <?php $tl=['proforma'=>'Proforma','e_invoice'=>'E-Fatura','e_archive'=>'E-Arşiv','return'=>'İade','cancel'=>'İptal']; ?>
            <span class="badge b-info"><?= $tl[$inv['type']]??$inv['type'] ?></span>
          </td>
          <td style="padding:10px 16px;font-size:12px;color:#9ca3af"><?= formatDate($inv['created_at']) ?></td>
          <td style="padding:10px 16px">
            <div style="display:flex;gap:6px">
              <a href="<?= adminUrl('faturalar/'.$inv['id'].'/indir') ?>" class="btn btn-outline btn-sm">İndir</a>
              <form method="POST" action="<?= adminUrl('faturalar/'.$inv['id'].'/sil') ?>">
                <?= csrfField() ?>
                <button onclick="return confirm('Bu faturayı silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php endif; ?>

<!-- Fatura Yükleme Modal -->
<div id="uploadModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:420px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:4px">Fatura Yükle</h3>
    <p id="uploadOrderLabel" style="font-size:12px;color:#9ca3af;margin-bottom:16px"></p>
    <form method="POST" action="<?= adminUrl('faturalar/yukle') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>
      <input type="hidden" name="order_id" id="uploadOrderId">
      <div class="form-group">
        <label>Fatura Tipi</label>
        <select name="type">
          <option value="e_archive">E-Arşiv Fatura</option>
          <option value="e_invoice">E-Fatura</option>
          <option value="proforma">Proforma Fatura</option>
          <option value="return">İade Faturası</option>
          <option value="cancel">İptal Faturası</option>
        </select>
      </div>
      <div class="form-group">
        <label>Fatura No</label>
        <input type="text" name="invoice_no" placeholder="FTR-2024-0001">
      </div>
      <div class="form-group" style="margin-bottom:16px">
        <label>Fatura Dosyası (PDF veya Görsel) <span style="color:#dc2626">*</span></label>
        <input type="file" name="invoice" accept=".pdf,image/*" required style="font-size:13px">
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" class="btn btn-outline">İptal</button>
        <button type="submit" class="btn btn-primary">Yükle</button>
      </div>
    </form>
  </div>
</div>

<script>
function openUpload(orderId, orderNo) {
  document.getElementById('uploadOrderId').value = orderId;
  document.getElementById('uploadOrderLabel').textContent = 'Sipariş No: ' + orderNo;
  document.getElementById('uploadModal').style.display = 'flex';
}
document.getElementById('uploadModal').addEventListener('click', function(e) {
  if (e.target === this) this.style.display = 'none';
});
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.form-group{margin-bottom:12px}
label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}
input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
