<?php ob_start(); ?>

<!-- Topbar -->
<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <span style="font-size:15px;font-weight:700">Güvenlik & Profil</span>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">

<!-- SOL — Profil -->
<div>
  <form method="POST" action="<?= adminUrl('ayarlar/guvenlik') ?>" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="_action" value="profile">
    <div class="card" style="margin-bottom:12px">
      <div class="card-header"><span class="card-title">Profil Bilgileri</span></div>
      <div class="card-body">
        <!-- Avatar -->
        <div style="display:flex;align-items:center;gap:14px;margin-bottom:16px;padding-bottom:16px;border-bottom:1px solid #f3f4f6">
          <div style="width:60px;height:60px;border-radius:50%;background:#eff6ff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:#2563eb;flex-shrink:0;overflow:hidden">
            <?php if (!empty($currentUser['avatar'])): ?>
              <img src="<?= uploadUrl($currentUser['avatar']) ?>" style="width:100%;height:100%;object-fit:cover">
            <?php else: ?>
              <?= strtoupper(substr($currentUser['name']??'A',0,1)) ?>
            <?php endif; ?>
          </div>
          <div>
            <div style="font-size:13px;font-weight:600"><?= e(($currentUser['name']??'').' '.($currentUser['surname']??'')) ?></div>
            <div style="font-size:12px;color:#6b7280;margin-bottom:6px"><?= e($currentUser['email']??'') ?></div>
            <input type="file" name="avatar" accept="image/*" style="font-size:12px">
          </div>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
          <div class="form-group">
            <label>Ad</label>
            <input type="text" name="name" value="<?= e($currentUser['name']??'') ?>" required>
          </div>
          <div class="form-group">
            <label>Soyad</label>
            <input type="text" name="surname" value="<?= e($currentUser['surname']??'') ?>" required>
          </div>
        </div>
        <div class="form-group">
          <label>E-posta</label>
          <input type="email" name="email" value="<?= e($currentUser['email']??'') ?>" required>
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Telefon</label>
          <input type="text" name="phone" value="<?= e($currentUser['phone']??'') ?>" placeholder="+90 500 000 00 00">
        </div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Profili Kaydet</button>
  </form>
</div>

<!-- SAG — Şifre + Güvenlik -->
<div>
  <!-- Şifre Değiştir -->
  <form method="POST" action="<?= adminUrl('ayarlar/guvenlik') ?>">
    <?= csrfField() ?>
    <input type="hidden" name="_action" value="password">
    <div class="card" style="margin-bottom:12px">
      <div class="card-header"><span class="card-title">Şifre Değiştir</span></div>
      <div class="card-body">
        <div class="form-group">
          <label>Mevcut Şifre</label>
          <input type="password" name="current_password" placeholder="••••••••" autocomplete="current-password">
        </div>
        <div class="form-group">
          <label>Yeni Şifre</label>
          <input type="password" name="new_password" id="newPwd" placeholder="Min. 8 karakter" autocomplete="new-password" oninput="checkPwd()">
        </div>
        <div class="form-group" style="margin-bottom:0">
          <label>Yeni Şifre (Tekrar)</label>
          <input type="password" name="new_password_confirm" id="newPwd2" placeholder="••••••••" autocomplete="new-password" oninput="checkPwd()">
          <div id="pwdMsg" style="font-size:11px;margin-top:3px"></div>
        </div>
      </div>
    </div>
    <button type="submit" class="btn btn-primary btn-sm">Şifreyi Değiştir</button>
  </form>

  <!-- Oturum Bilgisi -->
  <div class="card" style="margin-top:12px">
    <div class="card-header"><span class="card-title">Oturum Bilgisi</span></div>
    <div class="card-body">
      <div style="display:flex;flex-direction:column;gap:8px;font-size:13px">
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Rol</span>
          <span class="badge b-primary"><?= ucfirst($currentUser['role']??'admin') ?></span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">Son Giriş</span>
          <span><?= !empty($currentUser['last_login_at']) ? formatDate($currentUser['last_login_at'], 'd M Y H:i') : '—' ?></span>
        </div>
        <div style="display:flex;justify-content:space-between">
          <span style="color:#6b7280">IP Adresi</span>
          <span style="font-family:monospace;font-size:12px"><?= $_SERVER['REMOTE_ADDR'] ?? '—' ?></span>
        </div>
      </div>
      <div style="margin-top:12px;padding-top:12px;border-top:1px solid #f3f4f6">
        <a href="<?= adminUrl('cikis') ?>" class="btn btn-outline btn-sm" style="color:#dc2626;border-color:#fecaca;width:100%;justify-content:center">
          <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
          Çıkış Yap
        </a>
      </div>
    </div>
  </div>
</div>

</div>

<script>
function checkPwd() {
  const p1  = document.getElementById('newPwd').value;
  const p2  = document.getElementById('newPwd2').value;
  const msg = document.getElementById('pwdMsg');
  if (!p1 || !p2) { msg.textContent=''; return; }
  if (p1.length < 8) { msg.textContent='Min. 8 karakter gerekli'; msg.style.color='#dc2626'; return; }
  if (p1 !== p2) { msg.textContent='Şifreler eşleşmiyor'; msg.style.color='#dc2626'; }
  else { msg.textContent='✓ Şifreler eşleşiyor'; msg.style.color='#16a34a'; }
}
</script>

<?php
$content = ob_get_clean();
$extraStyles = '<style>
.card-body{padding:16px}
.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
.card-title{font-size:13px;font-weight:600}
.form-group{margin-bottom:12px}
.form-group:last-child{margin-bottom:0}
@media(max-width:900px){
  div[style*="grid-template-columns: 1fr 1fr"]{grid-template-columns:1fr!important}
}
</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
