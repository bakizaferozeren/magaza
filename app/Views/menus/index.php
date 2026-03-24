<?php ob_start(); ?>

<div style="font-size:15px;font-weight:700;margin-bottom:16px">Menü Yönetimi</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
  <?php foreach ($menus as $menu): ?>
    <div class="card">
      <div class="card-body">
        <div style="display:flex;align-items:center;justify-content:space-between">
          <div>
            <div style="font-size:14px;font-weight:600"><?= e($menu['name']) ?></div>
            <div style="font-size:12px;color:#9ca3af;margin-top:2px;font-family:monospace"><?= e($menu['location']) ?></div>
          </div>
          <a href="<?= adminUrl('menuler/'.$menu['id']) ?>" class="btn btn-primary btn-sm">Düzenle</a>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php $content=ob_get_clean(); $extraStyles='<style>.card-body{padding:16px}</style>'; require APP_PATH.'/Views/Admin/layouts/main.php'; ?>
