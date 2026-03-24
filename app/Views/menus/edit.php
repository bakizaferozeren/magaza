<?php ob_start(); ?>

<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
  <div style="display:flex;align-items:center;gap:10px">
    <a href="<?= adminUrl('menuler') ?>" style="display:flex;align-items:center;gap:4px;font-size:12px;color:#6b7280;padding:5px 10px;border:1px solid #e5e7eb;border-radius:7px;background:#fff;text-decoration:none">
      <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
      Menüler
    </a>
    <span style="font-size:15px;font-weight:700"><?= e($menu['name']) ?></span>
  </div>
  <button onclick="saveMenu()" class="btn btn-primary btn-sm">
    <svg width="13" height="13" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
    Menüyü Kaydet
  </button>
</div>

<div style="display:grid;grid-template-columns:1fr 300px;gap:16px">

<!-- Sol: Mevcut Öğeler -->
<div>
  <div class="card">
    <div class="card-header"><span class="card-title">Menü Öğeleri</span></div>
    <div class="card-body">
      <div id="menuItems" style="min-height:100px">
        <?php foreach ($items as $item): ?>
          <div class="menu-item-row" data-id="<?= $item['id'] ?>" data-type="<?= $item['type'] ?>" data-target="<?= $item['target_id'] ?>" data-url="<?= e($item['url']??'') ?>"
            style="display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:7px;margin-bottom:6px;cursor:move">
            <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:14px;height:14px;flex-shrink:0"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg>
            <input type="text" value="<?= e($item['label']??'') ?>" placeholder="Menü Adı"
              style="flex:1;padding:5px 8px;border:1px solid #e5e7eb;border-radius:5px;font-size:12px;outline:none">
            <span class="badge b-gray" style="font-size:10px"><?= $item['type'] ?></span>
            <button type="button" onclick="this.closest('.menu-item-row').remove()" style="background:#fef2f2;border:1px solid #fecaca;border-radius:5px;color:#dc2626;cursor:pointer;width:24px;height:24px;font-size:13px;display:flex;align-items:center;justify-content:center">×</button>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (empty($items)): ?>
        <p style="font-size:12px;color:#9ca3af;text-align:center;padding:1rem 0">Menü öğesi yok. Sağdan ekleyin.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Sag: Ekle -->
<div style="display:flex;flex-direction:column;gap:12px">

  <!-- Özel URL -->
  <div class="card">
    <div class="card-header"><span class="card-title">Özel Bağlantı</span></div>
    <div class="card-body">
      <div class="form-group">
        <label>URL</label>
        <input type="text" id="customUrl" placeholder="/urunler veya https://...">
      </div>
      <div class="form-group" style="margin-bottom:10px">
        <label>Menü Adı</label>
        <input type="text" id="customLabel" placeholder="Ürünler">
      </div>
      <button onclick="addItem('url',null,document.getElementById('customUrl').value,document.getElementById('customLabel').value)" class="btn btn-outline btn-sm" style="width:100%;justify-content:center">+ Ekle</button>
    </div>
  </div>

  <!-- Kategoriler -->
  <?php if (!empty($categories)): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">Kategoriler</span></div>
    <div class="card-body" style="max-height:200px;overflow-y:auto;padding:8px">
      <?php foreach ($categories as $cat): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 6px;font-size:12px;border-radius:5px" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">
          <?= e($cat['name']??'—') ?>
          <button onclick="addItem('category',<?= $cat['id'] ?>,null,'<?= e($cat['name']??'') ?>')" class="btn btn-outline" style="padding:2px 8px;font-size:11px">+</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Sayfalar -->
  <?php if (!empty($pages)): ?>
  <div class="card">
    <div class="card-header"><span class="card-title">Sayfalar</span></div>
    <div class="card-body" style="max-height:200px;overflow-y:auto;padding:8px">
      <?php foreach ($pages as $page): ?>
        <div style="display:flex;align-items:center;justify-content:space-between;padding:5px 6px;font-size:12px;border-radius:5px" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background=''">
          <?= e($page['title']??$page['slug']) ?>
          <button onclick="addItem('page',<?= $page['id'] ?>,null,'<?= e($page['title']??$page['slug']) ?>')" class="btn btn-outline" style="padding:2px 8px;font-size:11px">+</button>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

</div>
</div>

<script>
function addItem(type, targetId, url, label) {
  const container = document.getElementById('menuItems');
  const div = document.createElement('div');
  div.className = 'menu-item-row';
  div.dataset.type = type;
  div.dataset.target = targetId || '';
  div.dataset.url = url || '';
  div.style.cssText = 'display:flex;align-items:center;gap:8px;padding:8px 10px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:7px;margin-bottom:6px;cursor:move';
  div.innerHTML = `
    <svg viewBox="0 0 20 20" fill="#d1d5db" style="width:14px;height:14px;flex-shrink:0"><path d="M7 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 2zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 7 14zm6-8a2 2 0 1 0-.001-4.001A2 2 0 0 0 13 6zm0 2a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 8zm0 6a2 2 0 1 0 .001 4.001A2 2 0 0 0 13 14z"/></svg>
    <input type="text" value="${label||''}" placeholder="Menü Adı" style="flex:1;padding:5px 8px;border:1px solid #e5e7eb;border-radius:5px;font-size:12px;outline:none">
    <span style="background:#f3f4f6;color:#6b7280;padding:2px 6px;border-radius:4px;font-size:10px;font-weight:500">${type}</span>
    <button type="button" onclick="this.closest('.menu-item-row').remove()" style="background:#fef2f2;border:1px solid #fecaca;border-radius:5px;color:#dc2626;cursor:pointer;width:24px;height:24px;font-size:13px;display:flex;align-items:center;justify-content:center">×</button>
  `;
  container.appendChild(div);
}

function saveMenu() {
  const rows = document.querySelectorAll('.menu-item-row');
  const items = Array.from(rows).map(row => ({
    label: row.querySelector('input').value,
    type: row.dataset.type,
    target_id: row.dataset.target || null,
    url: row.dataset.url || null,
  }));

  fetch('<?= adminUrl('menuler/'.$menu['id'].'/kaydet') ?>', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify({items, _csrf_token: '<?= \App\Core\Session::csrfToken() ?>'})
  }).then(r=>r.json()).then(d=>{
    if (d.success) {
      const btn = document.querySelector('.btn-primary');
      btn.textContent = '✓ Kaydedildi';
      btn.style.background = '#16a34a';
      setTimeout(()=>{ btn.textContent='Menüyü Kaydet'; btn.style.background=''; }, 2000);
    }
  });
}
</script>

<?php
$content=ob_get_clean();
$extraStyles='<style>.card-body{padding:12px}.card-header{padding:12px 16px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}.card-title{font-size:13px;font-weight:600}.form-group{margin-bottom:10px}label{display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px}input{width:100%;padding:7px 10px;border:1px solid #e5e7eb;border-radius:7px;font-size:13px;outline:none}</style>';
require APP_PATH.'/Views/Admin/layouts/main.php';
?>
