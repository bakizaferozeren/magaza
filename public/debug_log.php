<?php
/**
 * HATA LOG GÖRÜNTÜLEYİCİ
 * Bu dosyayı: C:\laragon\www\magaza\public\debug_log.php olarak kaydedin
 * Tarayıcıda: http://localhost/magaza/public/debug_log.php
 *
 * ÖNEMLİ: Kullandıktan sonra bu dosyayı SİLİN (güvenlik riski)
 */

// Güvenlik: sadece localhost'tan erişime izin ver
if (!in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('Sadece localhost erişimi.');
}

$root = dirname(__DIR__);

// Olası log dosyası yerleri
$logPaths = [
    $root . '/storage/logs/error.log',
    $root . '/storage/logs/app.log',
    dirname($_SERVER['DOCUMENT_ROOT']) . '/logs/php_error.log',
    ini_get('error_log'),
];

// PHP ayarları
$action = $_GET['action'] ?? 'show';

if ($action === 'clear') {
    foreach ($logPaths as $p) {
        if ($p && file_exists($p)) file_put_contents($p, '');
    }
    header('Location: debug_log.php');
    exit;
}

if ($action === 'phpinfo') {
    phpinfo();
    exit;
}

// Upload test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $uploadDir = $root . '/public/uploads/products/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $result = move_uploaded_file($_FILES['test_image']['tmp_name'], $uploadDir . 'test_upload.jpg');
    $uploadMsg = $result ? '✅ Yükleme BAŞARILI! Dosya: ' . $uploadDir . 'test_upload.jpg' : '❌ Yükleme BAŞARISIZ! Hata: ' . $_FILES['test_image']['error'];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<title>Debug Log</title>
<style>
body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;margin:0}
h2{color:#00d4ff;border-bottom:1px solid #333;padding-bottom:8px}
.card{background:#16213e;border:1px solid #0f3460;border-radius:8px;padding:16px;margin-bottom:16px}
.log{background:#0d0d0d;padding:12px;border-radius:6px;max-height:400px;overflow-y:auto;font-size:12px;white-space:pre-wrap;word-break:break-all}
.ok{color:#00ff88}.err{color:#ff4757}.warn{color:#ffa502}
a{color:#00d4ff;text-decoration:none;margin-right:12px}
input[type=file]{color:#e0e0e0;background:#0d0d0d;border:1px solid #333;padding:6px;border-radius:4px;width:100%}
button{background:#0f3460;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer}
table{width:100%;border-collapse:collapse}
td,th{padding:6px 10px;border-bottom:1px solid #222;text-align:left;font-size:12px}
th{color:#00d4ff}
</style>
</head>
<body>

<h2>🔧 Debug Paneli</h2>
<p>
  <a href="?action=phpinfo">PHP Bilgisi</a>
  <a href="?action=clear">Logları Temizle</a>
  <a href="?">Yenile</a>
</p>

<!-- PHP Ayarları -->
<div class="card">
<h2>⚙️ PHP & Upload Ayarları</h2>
<table>
<tr><th>Ayar</th><th>Değer</th><th>Durum</th></tr>
<?php
$checks = [
    'PHP Sürümü'          => [PHP_VERSION, true],
    'upload_max_filesize' => [ini_get('upload_max_filesize'), true],
    'post_max_size'       => [ini_get('post_max_size'), true],
    'max_file_uploads'    => [ini_get('max_file_uploads'), true],
    'file_uploads'        => [ini_get('file_uploads') ? 'Açık' : 'Kapalı', ini_get('file_uploads')],
    'display_errors'      => [ini_get('display_errors') ? 'Açık' : 'Kapalı', true],
    'error_log'           => [ini_get('error_log') ?: '(yok)', true],
];
foreach ($checks as $k => $v) {
    $icon = $v[1] ? '<span class="ok">✓</span>' : '<span class="err">✗</span>';
    echo "<tr><td>$k</td><td>{$v[0]}</td><td>$icon</td></tr>";
}
?>
</table>
</div>

<!-- Yol Sabitleri -->
<div class="card">
<h2>📂 Yol Sabitleri</h2>
<table>
<?php
$paths = [
    'ROOT_PATH'  => defined('ROOT_PATH') ? ROOT_PATH : '<span class="err">TANIMLI DEĞİL</span>',
    'APP_PATH'   => defined('APP_PATH')  ? APP_PATH  : '<span class="err">TANIMLI DEĞİL</span>',
    'PUB_PATH'   => defined('PUB_PATH')  ? PUB_PATH  : '<span class="err">TANIMLI DEĞİL</span>',
    'STR_PATH'   => defined('STR_PATH')  ? STR_PATH  : '<span class="err">TANIMLI DEĞİL</span>',
    '__DIR__'    => __DIR__,
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
];
foreach ($paths as $k => $v) {
    echo "<tr><td>$k</td><td>$v</td></tr>";
}
?>
</table>
</div>

<!-- Upload Klasörü Kontrolü -->
<div class="card">
<h2>📁 Upload Klasörleri</h2>
<table>
<?php
$uploadDirs = [
    dirname(__DIR__) . '/public/uploads/products/',
    dirname(__DIR__) . '/public/uploads/categories/',
    dirname(__DIR__) . '/public/uploads/brands/',
];
foreach ($uploadDirs as $dir) {
    $exists = is_dir($dir);
    $writable = $exists && is_writable($dir);
    $status = !$exists ? '<span class="err">YOK</span>' : (!$writable ? '<span class="warn">YAZMA İZNİ YOK</span>' : '<span class="ok">OK</span>');
    echo "<tr><td>$dir</td><td>$status</td></tr>";
}
?>
</table>
</div>

<!-- Upload Test -->
<div class="card">
<h2>🖼️ Görsel Yükleme Testi</h2>
<?php if (isset($uploadMsg)): ?>
    <p style="font-size:14px"><?= $uploadMsg ?></p>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="test_image" accept="image/*" style="margin-bottom:8px">
    <br><button type="submit" style="margin-top:8px">Test Yükle</button>
</form>
</div>

<!-- Log Dosyaları -->
<div class="card">
<h2>📋 Hata Logları</h2>
<?php
$found = false;
foreach ($logPaths as $logFile) {
    if (!$logFile || !file_exists($logFile)) continue;
    $found = true;
    $size = filesize($logFile);
    echo "<p style='color:#aaa'>📄 $logFile ($size bytes)</p>";
    $content = file_get_contents($logFile);
    $lines = array_slice(explode("\n", $content), -100); // Son 100 satır
    $html = htmlspecialchars(implode("\n", $lines));
    // Renklendirme
    $html = preg_replace('/\[ERROR\].*/', '<span class="err">$0</span>', $html);
    $html = preg_replace('/\[WARNING\].*/', '<span class="warn">$0</span>', $html);
    $html = preg_replace('/\[INFO\].*/', '<span class="ok">$0</span>', $html);
    echo "<div class='log'>$html</div>";
}
if (!$found): ?>
    <p class="warn">⚠️ Log dosyası bulunamadı.</p>
    <p style="font-size:12px">Muhtemel konumlar:</p>
    <ul style="font-size:12px">
        <?php foreach ($logPaths as $p): ?>
            <li><?= $p ?: '(boş)' ?></li>
        <?php endforeach; ?>
    </ul>
    <p style="font-size:12px">Laragon log için: <code>C:\laragon\log\php_error.log</code> dosyasını kontrol edin.</p>
<?php endif; ?>
</div>

</body>
</html>
