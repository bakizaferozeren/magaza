<?php ob_start(); ?>
<div style="margin-bottom:16px"><span style="font-size:15px;font-weight:700">Widget Alanları</span></div>
<div class="card">
  <div class="card-body">
    <p style="font-size:13px;color:#6b7280;margin-bottom:16px">Widget yönetimi yakında eklenecek. Footer, sidebar gibi alanlara içerik bloğu ekleyebileceksiniz.</p>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px">
      <?php foreach (['footer_1'=>'Footer Kolon 1','footer_2'=>'Footer Kolon 2','footer_3'=>'Footer Kolon 3','sidebar'=>'Kenar Çubuğu'] as $loc=>$label): ?>
        <div style="padding:14px;background:#f9fafb;border-radius:8px;border:1px dashed #e5e7eb;text-align:center">
          <div style="font-size:12px;font-weight:500;color:#374151;margin-bottom:4px"><?= $label ?></div>
          <div style="font-size:11px;color:#9ca3af"><?= $loc ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php
$content = ob_get_clean();
$extraStyles = '<style>.card-body{padding:16px}</style>';
require APP_PATH . '/Views/Admin/layouts/main.php';
?>
