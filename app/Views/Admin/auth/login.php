<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Yönetici Girişi</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
.card{background:#fff;border-radius:12px;padding:32px;width:360px;box-shadow:0 4px 24px rgba(0,0,0,.08)}
.logo{text-align:center;margin-bottom:24px}
.logo span{font-size:22px;font-weight:700;color:#1a1a1a}
h1{font-size:18px;font-weight:600;text-align:center;margin-bottom:24px;color:#374151}
.form-group{margin-bottom:16px}
label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:5px}
input{width:100%;padding:9px 12px;border:1px solid #e5e7eb;border-radius:8px;font-size:14px;outline:none;transition:border .2s}
input:focus{border-color:#2563eb}
button{width:100%;padding:10px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer;margin-top:4px;transition:background .2s}
button:hover{background:#1d4ed8}
.error{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 12px;color:#dc2626;font-size:13px;margin-bottom:16px}
</style>
</head>
<body>
<div class="card">
  <div class="logo"><span><?= e(setting('site_name','Magazam')) ?></span></div>
  <h1>Yönetici Girişi</h1>

  <?php if ($error = flash('error')): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= adminUrl('giris') ?>">
    <?= csrfField() ?>
    <div class="form-group">
      <label>E-posta</label>
      <input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required autofocus placeholder="admin@magazam.com">
    </div>
    <div class="form-group">
      <label>Şifre</label>
      <input type="password" name="password" required placeholder="••••••••" autocomplete="current-password">
    </div>
    <button type="submit">Giriş Yap</button>
  </form>
</div>
</body>
</html>
