<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;gap:10px;margin-bottom:20px">
  <a href="<?= adminUrl('musteriler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
    <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
    Müşteriler
  </a>
  <span style="font-size:15px;font-weight:700"><?= e($customer['name'].' '.$customer['surname']) ?></span>
  <span class="badge <?= $customer['email_verified']?'b-success':'b-warning' ?>"><?= $customer['email_verified']?'Doğrulandı':'Doğrulanmadı' ?></span>
</div>

<div style="display:grid;grid-template-columns:1fr 280px;gap:16px">

<!-- SOL -->
<div>

  <!-- İstatistikler -->
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
    <div class="card" style="padding:14px 16px">
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Toplam Sipariş</div>
      <div style="font-size:22px;font-weight:700"><?= $stats['total_orders'] ?></div>
    </div>
    <div class="card" style="padding:14px 16px">
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Toplam Harcama</div>
      <div style="font-size:20px;font-weight:700;color:#2563eb"><?= formatPriceTRY($stats['total_spent']) ?></div>
    </div>
    <div class="card" style="padding:14px 16px">
      <div style="font-size:12px;color:#6b7280;margin-bottom:6px">Ort. Sipariş</div>
      <div style="font-size:20px;font-weight:700"><?= formatPriceTRY($stats['avg_order']) ?></div>
    </div>
  </div>

  <!-- Siparişler -->
  <div class="card" style="margin-bottom:16px">
    <div class="card-header">
      <span class="card-title">Son Siparişler</span>
      <a href="<?= adminUrl('siparisler?user_id='.$customer['id']) ?>" style="font-size:12px;color:#2563eb;text-decoration:none">Tümünü Gör →</a>
    </div>
    <?php if (empty($orders)): ?>
      <div style="text-align:center;padding:2rem;color:#9ca3af;font-size:13px">Henüz sipariş yok</div>
    <?php else: ?>
      <div class="table-wrap">
        <table>
          <thead>
            <tr><th>Sipariş No</th><th>Tutar</th><th>Durum</th><th>Tarih</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($orders as $o): ?>
            <tr>
              <td style="font-weight:600;color:#2563eb"><a href="<?= adminUrl('siparisler/'.$o['id']) ?>" style="color:inherit;text-decoration:none"><?= e($o['order_no']) ?></a></td>
              <td style="font-weight:600"><?= formatPriceTRY($o['total']) ?></td>
              <td><span class="badge b-<?= orderStatusColor($o['status']) ?>"><?= orderStatusLabel($o['status']) ?></span></td>
              <td style="font-size:12px;color:#9ca3af"><?= formatDate($o['created_at']) ?></td>
              <td><a href="<?= adminUrl('siparisler/'.$o['id']) ?>" class="btn btn-outline btn-sm">Detay</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Adresler -->
  <?php if (!empty($addresses)): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">Kayıtlı Adresler</span></div>
    <div class="card-body">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
        <?php foreach ($addresses as $addr): ?>
          <div style="padding:12px;background:#f9fafb;border-radius:8px;border:1px solid <?= $addr['is_default']?'#bfdbfe':'#f3f4f6' ?>">
            <?php if ($addr['is_default']): ?>
              <div style="font-size:10px;color:#2563eb;font-weight:600;margin-bottom:4px">VARSAYILAN</div>
            <?php endif; ?>
            <div style="font-size:13px;font-weight:500"><?= e($addr['title'] ?? '') ?></div>
            <div style="font-size:12px;color:#6b7280;margin-top:3px;line-height:1.5">
              <?= e($addr['full_name']) ?><br>
              <?= e($addr['phone']) ?><br>
              <?= e($addr['address']) ?><br>
              <?= e($addr['district'].' / '.$addr['city']) ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

</div>

<!-- SAG -->
<div style="display:flex;flex-direction:column;gap:12px">

  <!-- Profil -->
  <div class="card">
    <div class="card-header"><span class="card-title">Müşteri Bilgileri</span></div>
    <div class="card-body">
      <div style="text-align:center;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid #f3f4f6">
        <div style="width:60px;height:60px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#2563eb;margin:0 auto 8px">
          <?= strtoupper(substr($customer['name'],0,1).substr($customer['surname'],0,1)) ?>
        </div>
        <div style="font-size:14px;font-weight:600"><?= e($customer['name'].' '.$customer['surname']) ?></div>
        <div style="font-size:12px;color:#9ca3af"><?= e($customer['email']) ?></div>
      </div>
      <div style="display:flex;flex-direction:column;gap:7px;font-size:12px">
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Telefon</span>
          <span><?= e($customer['phone'] ?? '—') ?></span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Kayıt Tarihi</span>
          <span><?= formatDate($customer['created_at']) ?></span>
        </div>
        <?php if ($stats['first_order']): ?>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">İlk Sipariş</span>
          <span><?= formatDate($stats['first_order']) ?></span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Son Sipariş</span>
          <span><?= formatDate($stats['last_order']) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- İşlemler -->
  <div class="card">
    <div class="card-header"><span class="card-title">İşlemler</span></div>
    <div class="card-body" style="display:flex;flex-direction:column;gap:8px">
      <a href="mailto:<?= e($customer['email']) ?>" class="btn btn-outline btn-sm" style="width:100%;justify-content:center">
        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/></svg>
        E-posta Gönder
      </a>
      <button onclick="deleteCustomer(<?= $customer['id'] ?>, '<?= e($customer['name']) ?>')"
        class="btn btn-outline btn-sm" style="width:100%;justify-content:center;color:#dc2626;border-color:#fecaca">
        Müşteriyi Sil
      </button>
    </div>
  </div>

</div>
</div>

<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:1000;align-items:center;justify-content:center">
  <div style="background:#fff;border-radius:12px;padding:24px;width:380px;max-width:90vw">
    <h3 style="font-size:15px;font-weight:600;margin-bottom:8px">Müşteriyi Sil</h3>
    <p style="font-size:13px;color:#6b7280;margin-bottom:20px" id="deleteMsg"></p>
    <div style="display:flex;gap:8px;justify-content:flex-end">
      <button onclick="document.getElementById('deleteModal').style.display='none'" class="btn btn-outline">İptal</button>
      <form id="deleteForm" method="POST">
        <?= csrfField() ?>
        <button type="submit" class="btn btn-danger">Evet, Sil</button>
      </form>
    </div>
  </div>
</div>

<script>
function deleteCustomer(id, name) {
  document.getElementById('deleteMsg').textContent = '"' + name + '" müşterisini silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.';
  document.getElementById('deleteForm').action = '<?= adminUrl('musteriler/') ?>' + id + '/sil';
  document.getElementById('deleteModal').style.display = 'flex';
}
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 280px"]{grid-template-columns:1fr!important}
  div[style*="grid-template-columns: repeat(3"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
