<?php ob_start(); ?>
<div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
  <a href="<?= adminUrl('ayarlar/loglar') ?>" class="btn btn-outline btn-sm">← Loglar</a>
  <span style="font-size:14px;font-weight:600;font-family:monospace"><?= e($file) ?></span>
</div>
<div class="card">
  <div class="card-body">
    <pre style="font-size:11px;line-height:1.6;overflow:auto;max-height:600px;white-space:pre-wrap;color:#374151;background:#f9fafb;padding:12px;border-radius:6px"><?= e($content) ?></pre>
  </div>
</div>
<?php
$content_page = ob_get_clean();
$content = $content_page;
$extraStyles = '<style>.card-body{padding:16px}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
