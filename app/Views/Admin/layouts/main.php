<!DOCTYPE html>
<html lang="tr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title ?? 'Dashboard') ?> — <?= e($siteName ?? 'Yönetim') ?></title>
<meta name="robots" content="noindex,nofollow">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --sidebar-w:   220px;
  --header-h:    52px;
  --bg:          #f6f6f7;
  --sidebar-bg:  #1a1a1a;
  --card:        #ffffff;
  --primary:     #2563eb;
  --primary-h:   #1d4ed8;
  --text:        #1a1a1a;
  --muted:       #6b7280;
  --border:      #e5e7eb;
  --radius:      10px;
  --success:     #16a34a;
  --warning:     #d97706;
  --danger:      #dc2626;
  --info:        #0284c7;
}

body {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
}

/* ---- SIDEBAR ---- */
.sidebar {
  position: fixed;
  top: 0; left: 0;
  width: var(--sidebar-w);
  height: 100vh;
  background: var(--sidebar-bg);
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  overflow-x: hidden;
  z-index: 200;
  transition: transform .25s ease;
}

.sidebar::-webkit-scrollbar { width: 3px; }
.sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.12); border-radius: 2px; }

.sb-logo {
  padding: 14px 16px;
  border-bottom: 1px solid rgba(255,255,255,.08);
  display: flex;
  align-items: center;
  gap: 8px;
  text-decoration: none;
  flex-shrink: 0;
}

.sb-logo-icon {
  width: 28px; height: 28px;
  background: var(--primary);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 12px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.sb-logo-img {
  height: 28px;
  object-fit: contain;
}

.sb-logo-name {
  font-size: 13px;
  font-weight: 600;
  color: #fff;
  flex: 1;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.sb-logo-badge {
  font-size: 10px;
  font-weight: 600;
  background: rgba(255,255,255,.1);
  color: rgba(255,255,255,.55);
  padding: 2px 6px;
  border-radius: 10px;
  flex-shrink: 0;
}

.sb-section { padding: 8px 0 4px; }

.sb-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(255,255,255,.28);
  padding: 4px 16px 6px;
}

.sb-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 7px 16px;
  font-size: 13px;
  color: rgba(255,255,255,.62);
  text-decoration: none;
  cursor: pointer;
  transition: background .15s, color .15s;
  position: relative;
}

.sb-item:hover {
  background: rgba(255,255,255,.06);
  color: #fff;
}

.sb-item.active {
  background: rgba(255,255,255,.1);
  color: #fff;
}

.sb-item.active::before {
  content: '';
  position: absolute;
  left: 0; top: 20%; bottom: 20%;
  width: 3px;
  background: var(--primary);
  border-radius: 0 2px 2px 0;
}

.sb-item svg {
  width: 15px; height: 15px;
  flex-shrink: 0;
  opacity: .65;
}

.sb-item.active svg,
.sb-item:hover svg { opacity: 1; }

.sb-badge {
  margin-left: auto;
  background: var(--danger);
  color: #fff;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 6px;
  border-radius: 10px;
  min-width: 18px;
  text-align: center;
}

.sb-bottom {
  margin-top: auto;
  padding-bottom: .75rem;
  border-top: 1px solid rgba(255,255,255,.06);
}

/* ---- HEADER ---- */
.header {
  position: fixed;
  top: 0;
  left: var(--sidebar-w);
  right: 0;
  height: var(--header-h);
  background: #fff;
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  z-index: 100;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-title {
  font-size: 14px;
  font-weight: 600;
  color: var(--text);
}

.header-right {
  display: flex;
  align-items: center;
  gap: 8px;
}

.h-btn {
  width: 30px; height: 30px;
  border: 1px solid var(--border);
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  background: #fff;
  text-decoration: none;
  color: var(--muted);
  transition: background .15s, color .15s;
  position: relative;
}

.h-btn:hover { background: var(--bg); color: var(--text); }
.h-btn svg { width: 14px; height: 14px; }

.h-btn .notif-dot {
  position: absolute;
  top: 5px; right: 5px;
  width: 7px; height: 7px;
  background: var(--danger);
  border-radius: 50%;
  border: 1.5px solid #fff;
}

.user-btn {
  display: flex;
  align-items: center;
  gap: 7px;
  padding: 4px 8px 4px 4px;
  border: 1px solid var(--border);
  border-radius: 7px;
  cursor: pointer;
  background: #fff;
  text-decoration: none;
  color: var(--text);
  transition: background .15s;
}

.user-btn:hover { background: var(--bg); }

.user-avatar {
  width: 24px; height: 24px;
  background: var(--primary);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  color: #fff;
  flex-shrink: 0;
}

.user-name {
  font-size: 12px;
  font-weight: 500;
  max-width: 110px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.menu-toggle {
  display: none;
  background: none;
  border: none;
  cursor: pointer;
  color: var(--text);
  padding: .25rem;
  align-items: center;
}

/* ---- MAIN ---- */
.main {
  margin-left: var(--sidebar-w);
  margin-top: var(--header-h);
  padding: 20px;
  min-height: calc(100vh - var(--header-h));
}

/* ---- STAT KARTLAR ---- */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 16px;
}

.stat-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 16px;
}

.stat-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 12px;
  color: var(--muted);
  font-weight: 500;
}

.stat-icon {
  width: 32px; height: 32px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.stat-icon svg { width: 16px; height: 16px; }
.si-blue   { background: #eff6ff; color: var(--primary); }
.si-green  { background: #f0fdf4; color: var(--success); }
.si-amber  { background: #fffbeb; color: var(--warning); }
.si-purple { background: #f5f3ff; color: #7c3aed; }
.si-red    { background: #fef2f2; color: var(--danger); }

.stat-value {
  font-size: 22px;
  font-weight: 700;
  color: var(--text);
  line-height: 1;
  margin-bottom: 5px;
}

.stat-sub {
  font-size: 11px;
  color: var(--muted);
  display: flex;
  align-items: center;
  gap: 3px;
}

.trend-up   { color: var(--success); font-weight: 600; }
.trend-down { color: var(--danger);  font-weight: 600; }

/* ---- KART ---- */
.card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 16px;
}

.card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 14px;
}

.card-title {
  font-size: 13px;
  font-weight: 600;
  color: var(--text);
}

.card-link {
  font-size: 12px;
  color: var(--primary);
  text-decoration: none;
  font-weight: 500;
}

.card-link:hover { text-decoration: underline; }

/* ---- TABLO ---- */
.table-wrap { overflow-x: auto; }

table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

thead th {
  text-align: left;
  padding: 0 8px 8px;
  font-size: 11px;
  font-weight: 600;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .04em;
  border-bottom: 1px solid #f3f4f6;
  white-space: nowrap;
}

tbody td {
  padding: 9px 8px;
  border-bottom: 1px solid #f9fafb;
  color: #374151;
  vertical-align: middle;
}

tbody tr:last-child td { border-bottom: none; }
tbody tr:hover td { background: #fafafa; }

/* ---- BADGE ---- */
.badge {
  display: inline-flex;
  align-items: center;
  padding: 2px 7px;
  border-radius: 12px;
  font-size: 11px;
  font-weight: 500;
  white-space: nowrap;
}

.b-success { background: #f0fdf4; color: #15803d; }
.b-warning { background: #fffbeb; color: #b45309; }
.b-danger  { background: #fef2f2; color: #b91c1c; }
.b-info    { background: #f0f9ff; color: #0369a1; }
.b-gray    { background: #f9fafb; color: #4b5563; }
.b-primary { background: #eff6ff; color: #1d4ed8; }
.b-purple  { background: #f5f3ff; color: #6d28d9; }

/* ---- BUTON ---- */
.btn {
  display: inline-flex;
  align-items: center;
  gap: .375rem;
  padding: .5rem .875rem;
  border-radius: 7px;
  font-size: .8125rem;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  border: 1px solid transparent;
  transition: all .15s;
  white-space: nowrap;
}

.btn svg { width: 14px; height: 14px; }
.btn-primary  { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-primary:hover { background: var(--primary-h); border-color: var(--primary-h); }
.btn-outline  { background: transparent; color: var(--text); border-color: var(--border); }
.btn-outline:hover { background: var(--bg); }
.btn-danger   { background: var(--danger); color: #fff; }
.btn-danger:hover { background: #b91c1c; }
.btn-sm { padding: .3125rem .625rem; font-size: .75rem; }

/* ---- FLASH ---- */
.flash {
  padding: .625rem 1rem;
  border-radius: var(--radius);
  font-size: .8125rem;
  margin-bottom: 1rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.flash svg { width: 15px; height: 15px; flex-shrink: 0; }
.flash-success { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
.flash-error   { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
.flash-warning { background: #fffbeb; color: #b45309; border: 1px solid #fde68a; }
.flash-info    { background: #f0f9ff; color: #0369a1; border: 1px solid #bae6fd; }

/* ---- FORM ---- */
.form-group { margin-bottom: 1rem; }

label {
  display: block;
  font-size: .8125rem;
  font-weight: 500;
  color: var(--text);
  margin-bottom: .375rem;
}

input[type="text"],
input[type="email"],
input[type="number"],
input[type="password"],
input[type="url"],
input[type="date"],
select,
textarea {
  width: 100%;
  padding: .5625rem .75rem;
  border: 1px solid var(--border);
  border-radius: 7px;
  font-size: .875rem;
  color: var(--text);
  background: #fff;
  outline: none;
  transition: border-color .15s, box-shadow .15s;
}

input:focus,
select:focus,
textarea:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(37,99,235,.1);
}

textarea { resize: vertical; min-height: 100px; }

.form-hint {
  font-size: .75rem;
  color: var(--muted);
  margin-top: .25rem;
}

/* ---- OVERLAY & RESPONSIVE ---- */
.overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 199;
}

.overlay.show { display: block; }

@media (max-width: 1024px) {
  .sidebar { transform: translateX(-100%); }
  .sidebar.open { transform: translateX(0); }
  .header { left: 0; }
  .main { margin-left: 0; }
  .menu-toggle { display: flex; }
  .stat-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 640px) {
  .stat-grid { grid-template-columns: 1fr; }
  .main { padding: 12px; }
}
</style>
<?php if (isset($extraStyles)) echo $extraStyles; ?>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

  <!-- Logo -->
  <a href="<?= adminUrl() ?>" class="sb-logo">
    <?php if (!empty($siteLogo)): ?>
      <img src="<?= uploadUrl($siteLogo) ?>" alt="<?= e($siteName) ?>" class="sb-logo-img">
    <?php else: ?>
      <div class="sb-logo-icon"><?= strtoupper(substr($siteName ?? 'M', 0, 1)) ?></div>
    <?php endif; ?>
    <span class="sb-logo-name"><?= e($siteName ?? 'Magazam') ?></span>
    <span class="sb-logo-badge">Admin</span>
  </a>

  <?php
  $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  function sbItem(string $url, string $label, string $svgPath, string $badge = ''): void {
    $path    = parse_url($url, PHP_URL_PATH);
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $active  = ($path !== '/' && str_starts_with($current, $path)) ? 'active' : '';
    if ($path === '/' . ($_ENV['APP_ADMIN_PATH'] ?? 'yonetim') || $path === '/' . ($_ENV['APP_ADMIN_PATH'] ?? 'yonetim') . '/') {
        $active = ($current === $path || $current === $path . '/') ? 'active' : '';
    }
    echo '<a href="' . $url . '" class="sb-item ' . $active . '">';
    echo '<svg viewBox="0 0 20 20" fill="currentColor">' . $svgPath . '</svg>';
    echo '<span>' . $label . '</span>';
    if ($badge) echo '<span class="sb-badge">' . $badge . '</span>';
    echo '</a>';
  }
  ?>

  <nav style="flex:1">

    <!-- Ana -->
    <div class="sb-section">
      <?php sbItem(adminUrl(), 'Dashboard', '<path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>'); ?>
    </div>

    <!-- Katalog -->
    <div class="sb-section">
      <div class="sb-label">Katalog</div>
      <?php sbItem(adminUrl('urunler'), 'Ürünler', '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8zm5 3a1 1 0 000 2h.01a1 1 0 100-2H8zm2 0a1 1 0 000 2h2a1 1 0 100-2h-2z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('kategoriler'), 'Kategoriler', '<path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>'); ?>
      <?php sbItem(adminUrl('markalar'), 'Markalar', '<path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('nitelikler'), 'Varyasyon Nitelikleri', '<path d="M5 4a1 1 0 00-2 0v7.268a2 2 0 000 3.464V16a1 1 0 102 0v-1.268a2 2 0 000-3.464V4zM11 4a1 1 0 10-2 0v1.268a2 2 0 000 3.464V16a1 1 0 102 0V8.732a2 2 0 000-3.464V4zM16 3a1 1 0 011 1v7.268a2 2 0 010 3.464V16a1 1 0 11-2 0v-1.268a2 2 0 010-3.464V4a1 1 0 011-1z"/>'); ?>
      <?php sbItem(adminUrl('degerlendirmeler'), 'Değerlendirmeler', '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>'); ?>
    </div>

    <!-- Satis -->
    <div class="sb-section">
      <div class="sb-label">Satış</div>
      <?php sbItem(adminUrl('siparisler'), 'Siparişler', '<path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('iadeler'), 'İadeler', '<path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('faturalar'), 'Faturalar', '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('kuponlar'), 'Kuponlar', '<path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/><path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>'); ?>
    </div>

    <!-- Musteriler -->
    <div class="sb-section">
      <div class="sb-label">Müşteriler</div>
      <?php sbItem(adminUrl('musteriler'), 'Müşteri Listesi', '<path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>'); ?>
      <?php sbItem(adminUrl('bulten'), 'Bülten Aboneleri', '<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>'); ?>
      <?php sbItem(adminUrl('kvkk'), 'KVKK Talepleri', '<path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>'); ?>
    </div>

    <!-- Pazarlama -->
    <div class="sb-section">
      <div class="sb-label">Pazarlama</div>
      <?php sbItem(adminUrl('sliderlar'), 'Slider & Banner', '<path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('popuplar'), 'Popup', '<path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('terk-sepetler'), 'Terk Edilmiş Sepetler', '<path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3z"/>'); ?>
    </div>

    <!-- Icerik -->
    <div class="sb-section">
      <div class="sb-label">İçerik</div>
      <?php sbItem(adminUrl('blog'), 'Blog / Haber', '<path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>'); ?>
      <?php sbItem(adminUrl('sayfalar'), 'Sayfalar', '<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('menuler'), 'Menü Yönetimi', '<path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('sss'), 'SSS', '<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>'); ?>
    </div>

    <!-- Raporlar -->
    <div class="sb-section">
      <div class="sb-label">Raporlar</div>
      <?php sbItem(adminUrl('raporlar/satis'), 'Satış Raporları', '<path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>'); ?>
      <?php sbItem(adminUrl('raporlar/stok'), 'Stok Raporları', '<path d="M4 3a2 2 0 100 4h12a2 2 0 100-4H4z"/><path fill-rule="evenodd" d="M3 8h14v7a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" clip-rule="evenodd"/>'); ?>
    </div>

    <!-- Ayarlar -->
    <div class="sb-section">
      <div class="sb-label">Ayarlar</div>
      <?php sbItem(adminUrl('ayarlar/genel'), 'Genel Ayarlar', '<path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/>'); ?>
      <?php sbItem(adminUrl('ayarlar/google'), 'Google Entegrasyonları', '<path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16A8 8 0 0010 2zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"/>'); ?>
    </div>
  </nav>

  <!-- Alt kisim -->
  <div class="sb-bottom">
    <a href="<?= url() ?>" target="_blank" class="sb-item">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16A8 8 0 0010 2zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z" clip-rule="evenodd"/></svg>
      <span>Siteyi Görüntüle</span>
    </a>
    <a href="<?= adminUrl('cikis') ?>" class="sb-item">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/></svg>
      <span>Çıkış Yap</span>
    </a>
  </div>

</aside>

<!-- Overlay -->
<div class="overlay" id="overlay" onclick="closeSidebar()"></div>

<!-- Header -->
<header class="header">
  <div class="header-left">
    <button class="menu-toggle" onclick="toggleSidebar()" style="align-items:center">
      <svg width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
      </svg>
    </button>
    <span class="header-title"><?= e($title ?? 'Dashboard') ?></span>
  </div>

  <div class="header-right">
    <a href="<?= adminUrl('siparisler') ?>" class="h-btn" title="Siparişler">
      <svg viewBox="0 0 20 20" fill="currentColor"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5z" clip-rule="evenodd"/></svg>
    </a>
    <a href="<?= adminUrl('ayarlar/genel') ?>" class="h-btn" title="Ayarlar">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"/></svg>
    </a>
    <?php
    $initials = '';
    if (!empty($user)) {
        $initials = strtoupper(substr($user['name'] ?? '', 0, 1) . substr($user['surname'] ?? '', 0, 1));
    }
    ?>
    <a href="<?= adminUrl('ayarlar/guvenlik') ?>" class="user-btn">
      <div class="user-avatar"><?= e($initials) ?></div>
      <span class="user-name"><?= e($user['name'] ?? 'Admin') ?></span>
    </a>
  </div>
</header>

<!-- Main -->
<main class="main">

  <?php if (hasFlash('success')): ?>
    <div class="flash flash-success">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
      <?= e(flash('success')) ?>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('error')): ?>
    <div class="flash flash-error">
      <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
      <?= e(flash('error')) ?>
    </div>
  <?php endif; ?>

  <?php if (hasFlash('warning')): ?>
    <div class="flash flash-warning"><?= e(flash('warning')) ?></div>
  <?php endif; ?>

  <?php if (hasFlash('info')): ?>
    <div class="flash flash-info"><?= e(flash('info')) ?></div>
  <?php endif; ?>

  <?php if (isset($content)) echo $content; ?>

</main>

<script>
function toggleSidebar() {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('show');
}
</script>

<?php if (isset($extraScripts)) echo $extraScripts; ?>
</body>
</html>
