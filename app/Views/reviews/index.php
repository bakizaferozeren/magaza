<?php ob_start(); ?>

<!-- Toolbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:10px">
  <div style="display:flex;gap:6px">
    <a href="?status=pending"  class="btn btn-sm <?= $status==='pending' ?'btn-primary':'btn-outline' ?>">
      Bekleyenler <span style="background:<?= $status==='pending'?'rgba(255,255,255,.25)':'#fef2f2' ?>;color:<?= $status==='pending'?'#fff':'#dc2626' ?>;border-radius:8px;padding:1px 6px;font-size:10px;font-weight:700"><?= $counts['pending'] ?></span>
    </a>
    <a href="?status=approved" class="btn btn-sm <?= $status==='approved'?'btn-primary':'btn-outline' ?>">Onaylananlar <span style="background:#f0fdf4;color:#15803d;border-radius:8px;padding:1px 6px;font-size:10px;font-weight:700"><?= $counts['approved'] ?></span></a>
    <a href="?status=all"      class="btn btn-sm <?= $status==='all'     ?'btn-primary':'btn-outline' ?>">Tümü</a>
  </div>
  <form method="GET" style="display:flex;gap:6px">
    <input type="hidden" name="status" value="<?= $status ?>">
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Ürün, kişi, yorum..." style="padding:6px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none;width:200px">
    <button type="submit" class="btn btn-outline btn-sm">Ara</button>
  </form>
</div>

<div class="card" style="padding:0">
  <?php if (empty($reviews)): ?>
    <div style="text-align:center;padding:3rem;color:#9ca3af;font-size:13px">Değerlendirme bulunamadı</div>
  <?php else: ?>
    <div style="display:flex;flex-direction:column">
      <?php foreach ($reviews as $i => $r): ?>
        <div style="padding:14px 16px;<?= $i<count($reviews)-1?'border-bottom:1px solid #f9fafb':'' ?>">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px">
            <div style="flex:1">
              <!-- Yıldızlar -->
              <div style="display:flex;align-items:center;gap:6px;margin-bottom:6px">
                <div style="display:flex;gap:1px">
                  <?php for ($s=1;$s<=5;$s++): ?>
                    <svg viewBox="0 0 20 20" fill="<?= $s<=$r['rating']?'#fbbf24':'#e5e7eb' ?>" style="width:14px;height:14px">
                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                  <?php endfor; ?>
                </div>
                <span style="font-size:13px;font-weight:600"><?= e($r['author_name']) ?></span>
                <?php if ($r['is_verified']): ?>
                  <span class="badge b-success" style="font-size:10px">Doğrulanmış Alım</span>
                <?php endif; ?>
                <?php if ($r['is_manual']): ?>
                  <span class="badge b-info" style="font-size:10px">Admin Girişi</span>
                <?php endif; ?>
              </div>
              <p style="font-size:13px;color:#374151;margin-bottom:6px;line-height:1.5"><?= e($r['comment']) ?></p>
              <div style="font-size:11px;color:#9ca3af">
                <?= e($r['product_name'] ?? '—') ?> · <?= formatDate($r['created_at']) ?>
                <?php if ($r['product_slug']): ?>
                  · <a href="<?= url('urun/'.$r['product_slug']) ?>" target="_blank" style="color:#2563eb">Ürünü Gör</a>
                <?php endif; ?>
              </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:6px;flex-shrink:0">
              <span class="badge <?= $r['is_approved']?'b-success':'b-warning' ?>"><?= $r['is_approved']?'Onaylı':'Bekliyor' ?></span>
              <div style="display:flex;gap:4px">
                <?php if (!$r['is_approved']): ?>
                  <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/onayla') ?>">
                    <?= csrfField() ?>
                    <button class="btn btn-outline btn-sm" style="color:#16a34a;border-color:#bbf7d0">Onayla</button>
                  </form>
                <?php else: ?>
                  <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/reddet') ?>">
                    <?= csrfField() ?>
                    <button class="btn btn-outline btn-sm" style="color:#d97706;border-color:#fde68a">Geri Al</button>
                  </form>
                <?php endif; ?>
                <form method="POST" action="<?= adminUrl('degerlendirmeler/'.$r['id'].'/sil') ?>">
                  <?= csrfField() ?>
                  <button onclick="return confirm('Silmek istediğinizden emin misiniz?')" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca">Sil</button>
                </form>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php $content=ob_get_clean(); require APP_PATH.'/Views/Admin/layouts/main.php'; ?>
