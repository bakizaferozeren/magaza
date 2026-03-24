<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <form method="GET" action="<?= adminUrl('musteriler') ?>" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="İsim, e-posta, telefon..."
      style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:220px">
    <select name="status" onchange="this.form.submit()" style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:12px;outline:none;color:#374151">
      <option value="">Tüm Müşteriler</option>
      <option value="verified"   <?= $status==='verified'  ?'selected':'' ?>>E-posta Doğrulandı</option>
      <option value="unverified" <?= $status==='unverified'?'selected':'' ?>>Doğrulanmadı</option>
    </select>
    <button type="submit" class="btn btn-outline btn-sm">Ara</button>
    <?php if ($search || $status): ?>
      <a href="<?= adminUrl('musteriler') ?>" class="btn btn-outline btn-sm">Temizle</a>
    <?php endif; ?>
  </form>
  <span style="font-size:13px;color:#6b7280"><?= $pagination['total'] ?> müşteri</span>
</div>

<div class="card" style="padding:0">
  <?php if (empty($customers)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af">
      <p style="font-size:14px;font-weight:500;color:#6b7280">Müşteri bulunamadı</p>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Müşteri</th>
            <th>Telefon</th>
            <th>Sipariş</th>
            <th>Harcama</th>
            <th>Kayıt</th>
            <th>Durum</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customers as $c): ?>
          <tr>
            <td>
              <div style="display:flex;align-items:center;gap:10px">
                <div style="width:34px;height:34px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#2563eb;flex-shrink:0">
                  <?= strtoupper(substr($c['name'],0,1).substr($c['surname'],0,1)) ?>
                </div>
                <div>
                  <div style="font-size:13px;font-weight:500"><?= e($c['name'].' '.$c['surname']) ?></div>
                  <div style="font-size:11px;color:#9ca3af"><?= e($c['email']) ?></div>
                </div>
              </div>
            </td>
            <td style="font-size:12px;color:#6b7280"><?= e($c['phone'] ?? '—') ?></td>
            <td style="font-weight:500"><?= $c['order_count'] ?></td>
            <td style="font-weight:600;color:#2563eb"><?= formatPriceTRY($c['total_spent']) ?></td>
            <td style="font-size:12px;color:#9ca3af"><?= formatDate($c['created_at']) ?></td>
            <td>
              <?php if ($c['email_verified']): ?>
                <span class="badge b-success">Doğrulandı</span>
              <?php else: ?>
                <span class="badge b-warning">Bekliyor</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="<?= adminUrl('musteriler/'.$c['id']) ?>" class="btn btn-outline btn-sm">Detay</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php if ($pagination['last_page'] > 1): ?>
    <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #f3f4f6">
      <span style="font-size:12px;color:#6b7280"><?= $pagination['from'] ?>–<?= $pagination['to'] ?> / <?= $pagination['total'] ?></span>
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

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
