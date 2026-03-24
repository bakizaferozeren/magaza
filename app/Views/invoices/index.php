<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
  <span style="font-size:15px;font-weight:700">Faturalar</span>
  <button onclick="document.getElementById('uploadModal').style.display='flex'" class="btn btn-primary btn-sm">Fatura Yükle</button>
</div>

<div class="card" style="padding:0">
  <?php if (empty($invoices)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Fatura bulunamadı</div>
  <?php else: ?>
    <table style="width:100%;border-collapse:collapse;font-size:13px">
      <thead>
        <tr>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Fatura No</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Sipariş</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Müşteri</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Tip</th>
          <th style="text-align:left;padding:10px 16px;font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;border-bottom:1px solid #f3f4f6">Tarih</th>
          <th style="border-bottom:1px solid #f3f4f6"></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($invoices as $inv): ?>
        <tr style="border-bottom:1px solid #f9fafb">
          <td style="padding:10px 16px;font-family:monospace;font-weight:600"><?= e($inv['invoice_no'] ?? '—') ?></td>
          <td style="padding:10px 16px">
            <a href="<?= adminUrl('siparisler/'.$inv['order_id']) ?>" style="color:#2563eb;text-decoration:none"><?= e($inv['order_no']) ?></a>
          </td>
          <td style="padding:10px 16px;color:#374151"><?= e($inv['customer_name']) ?></td>
          <td style="padding:10px 16px">
            <?php
            $types = ['proforma'=>'Proforma','e_invoice'=>'E-Fatura','e_archive'=>'E-Arşiv','return'=>'İade','cancel'=>'İptal'];
            ?>
            <span class="badge b-info"><?= $types[$inv['type']]??$inv['type'] ?></span>
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

<!-- Fatura Yükle Modal -->
<div id="uploadModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:420px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:16px">Fatura Yükle</h3>
    <form method="POST" action="<?= adminUrl('faturalar/yukle') ?>" enctype="multipart/form-data">
      <?= csrfField() ?>
      <div class="form-group">
        <label>Sipariş No / ID</label>
        <input type="number" name="order_id" placeholder="Sipariş ID" required>
      </div>
      <div class="form-group">
        <label>Fatura No</label>
        <input type="text" name="invoice_no" placeholder="INV-2024-001">
      </div>
      <div class="form-group">
        <label>Fatura Tipi</label>
        <select name="type">
          <option value="e_invoice">E-Fatura</option>
          <option value="e_archive">E-Arşiv</option>
          <option value="proforma">Proforma</option>
          <option value="return">İade Faturası</option>
          <option value="cancel">İptal Faturası</option>
        </select>
      </div>
      <div class="form-group">
        <label>PDF Dosyası</label>
        <input type="file" name="invoice" accept=".pdf" required style="font-size:13px">
      </div>
      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:12px">
        <button type="button" onclick="document.getElementById('uploadModal').style.display='none'" class="btn btn-outline">İptal</button>
        <button type="submit" class="btn btn-primary">Yükle</button>
      </div>
    </form>
  </div>
</div>
<script>document.getElementById('uploadModal').addEventListener('click',function(e){if(e.target===this)this.style.display='none'});</script>

<?php
$content=ob_get_clean();
$extraStyles='<style>.form-group{margin-bottom:12px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input,select{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH.'/Views/Admin/layouts/main.php';
?>
