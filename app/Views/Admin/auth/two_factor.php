<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>İki Faktörlü Doğrulama</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{min-height:100vh;background:#f3f4f6;display:flex;align-items:center;justify-content:center;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif}
.card{background:#fff;border-radius:12px;padding:32px;width:360px;box-shadow:0 4px 24px rgba(0,0,0,.08);text-align:center}
h1{font-size:18px;font-weight:600;margin-bottom:8px;color:#374151}
p{font-size:13px;color:#6b7280;margin-bottom:24px}
input{width:100%;padding:12px;border:1px solid #e5e7eb;border-radius:8px;font-size:20px;text-align:center;letter-spacing:.4em;outline:none;margin-bottom:16px}
input:focus{border-color:#2563eb}
button{width:100%;padding:10px;background:#2563eb;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:500;cursor:pointer}
.error{background:#fef2f2;border:1px solid #fecaca;border-radius:8px;padding:10px 12px;color:#dc2626;font-size:13px;margin-bottom:16px}
a{color:#2563eb;font-size:12px;display:block;margin-top:12px}
</style>
</head>
<body>
<div class="card">
  <h1>🔐 Doğrulama Kodu</h1>
  <p>Google Authenticator uygulamasındaki 6 haneli kodu girin.</p>

  <?php if ($error = flash('error')): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="<?= adminUrl('2fa') ?>">
    <?= csrfField() ?>
    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" inputmode="numeric" autofocus placeholder="000000" required>
    <button type="submit">Doğrula</button>
  </form>
  <a href="<?= adminUrl('giris') ?>">← Giriş sayfasına dön</a>
</div>
</body>
</html>
