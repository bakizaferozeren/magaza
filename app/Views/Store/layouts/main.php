<!DOCTYPE html>
<html lang="<?= $lang ?? 'tr' ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= e($pageTitle ?? ($settings['site_name'] ?? 'Mağaza')) ?></title>
  <meta name="description" content="<?= e($metaDesc ?? ($settings['site_description'] ?? '')) ?>">
  <link rel="stylesheet" href="<?= asset('css/store.css') ?>">
  <?php if(!empty($extraCss)):foreach((array)$extraCss as $c): ?>
    <link rel="stylesheet" href="<?= asset('css/'.$c) ?>">
  <?php endforeach;endif; ?>
</head>
<body>
<!-- TOP BAR -->
<div class="top-bar">
  <div class="top-bar-track">
    <?php for($i=0;$i<4;$i++): ?>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Ücretsiz Kargo <?= number_format((float)($settings['free_shipping_over']??500)) ?> ₺ Üzeri</span>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Güvenli Ödeme</span>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> 7/24 Destek</span>
      <span><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg> 14 Gün Kolay İade</span>
    <?php endfor; ?>
  </div>
</div>
<!-- HEADER -->
<header class="site-header" id="siteHeader">
  <div class="header-inner container">
    <a href="<?= url() ?>" class="site-logo">
      <?php if(!empty($settings['site_logo'])): ?>
        <img src="<?= uploadUrl($settings['site_logo']) ?>" alt="<?= e($settings['site_name']) ?>" height="36">
      <?php else: ?>
        <span class="logo-icon">✦</span><?= e($settings['site_name'] ?? 'Mağaza') ?>
      <?php endif; ?>
    </a>
    <div class="header-search">
      <button class="search-cat-btn">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
        Tüm Kategoriler
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <form action="<?= url('arama') ?>" method="GET" class="search-form-wrap" id="headerSearchForm">
        <input type="text" name="q" class="search-input" placeholder="Ürün, kategori veya marka ara..." value="<?= e($_GET['q']??'') ?>" autocomplete="off" id="headerSearchInput">
        <button type="submit" class="search-submit">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          Ara
        </button>
      </form>
      <div class="search-dropdown" id="searchDropdown"></div>
    </div>
    <div class="header-right">
      <button class="deal-btn">
        <span class="deal-top-badge">%20</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e8000d" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
        <div class="deal-main"><strong>Ekstra %20 İndirim*</strong><span>Sınırlı Süre</span></div>
        <svg class="deal-caret" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
      <a href="<?= url('iletisim') ?>" class="hdr-btn">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 014.69 12 19.79 19.79 0 011.62 3.33A2 2 0 013.6 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L7.91 8.59a16 16 0 006 6l.98-.87a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
        Yardım?
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </a>
      <div style="position:relative">
        <button class="hdr-btn" id="userMenuBtn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          <?= isLoggedIn() ? 'Hesabım' : 'Giriş Yap' ?>
        </button>
        <div class="nav-dropdown" id="userDropdown" style="top:100%;right:0;left:auto;min-width:160px;display:none">
          <?php if(isLoggedIn()): ?>
            <a href="<?= url('hesabim') ?>">Hesabım</a>
            <a href="<?= url('hesabim/siparisler') ?>">Siparişlerim</a>
            <a href="<?= url('favoriler') ?>">Favorilerim</a>
            <a href="<?= url('cikis') ?>" style="color:#dc2626">Çıkış</a>
          <?php else: ?>
            <a href="<?= url('giris') ?>">Giriş Yap</a>
            <a href="<?= url('kayit') ?>">Üye Ol</a>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?= url('sepet') ?>" class="cart-btn">
        <span class="cart-badge" id="cartCount">0</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        (<span id="cartTotal">₺0,00</span>)
      </a>
    </div>
  </div>
  <nav class="main-nav">
    <div class="nav-inner container">
      <ul class="nav-list">
        <li class="nav-item"><a href="<?= url() ?>" class="nav-link">Ana Sayfa</a></li>
        <?php foreach(array_slice($navCategories??[],0,6) as $cat): ?>
        <li class="nav-item">
          <a href="<?= url('kategori/'.$cat['slug']) ?>" class="nav-link">
            <?= e($cat['name']??'') ?>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
          </a>
          <div class="nav-dropdown">
            <?php foreach($navCategories??[] as $sub): ?><a href="<?= url('kategori/'.$sub['slug']) ?>"><?= e($sub['name']??'') ?></a><?php endforeach; ?>
          </div>
        </li>
        <?php endforeach; ?>
        <li class="nav-item"><a href="<?= url('blog') ?>" class="nav-link">Blog</a></li>
      </ul>
      <div class="nav-right">
        <?php if(!empty($settings['site_phone'])): ?>
        <a href="tel:<?= e($settings['site_phone']) ?>" class="nav-right-btn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 014.69 12 19.79 19.79 0 011.62 3.33A2 2 0 013.6 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L7.91 8.59a16 16 0 006 6l.98-.87a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
          <?= e($settings['site_phone']) ?>
        </a>
        <?php endif; ?>
        <a href="<?= url('favoriler') ?>" class="nav-right-btn">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
          Favoriler
        </a>
      </div>
    </div>
  </nav>
</header>
<main id="mainContent"><?= $content ?? '' ?></main>
<!-- KAYAN MARKA BANDI -->
<div class="brand-marquee">
  <div class="brand-mq-track">
    <?php for($i=0;$i<3;$i++): for($j=0;$j<8;$j++): ?><span><?= e($settings['site_name']??'Mağaza') ?>.</span><?php endfor;endfor; ?>
  </div>
</div>
<!-- BÜLTEN -->
<section class="newsletter-section">
  <div class="nl-inner container">
    <div>
      <div class="nl-label">Bültenimize Katılın</div>
      <div class="nl-title">Yeni Ürünlerden İlk Haberdar Olan Siz Olun</div>
    </div>
    <form class="nl-form" id="footerNewsletterForm">
      <?= csrfField() ?>
      <div class="nl-row">
        <input type="email" name="email" placeholder="E-posta adresiniz" required>
        <button type="submit">Abone Ol</button>
      </div>
      <p class="nl-agree">Abone olarak <a href="<?= url('gizlilik-politikasi') ?>">gizlilik politikamızı</a> kabul etmiş olursunuz.</p>
    </form>
  </div>
</section>
<!-- FOOTER -->
<footer class="site-footer">
  <div class="footer-top container">
    <div>
      <div class="site-logo footer-logo">
        <?php if(!empty($settings['site_logo'])): ?>
          <img src="<?= uploadUrl($settings['site_logo']) ?>" alt="" height="28" style="filter:brightness(0) invert(1);opacity:.85">
        <?php else: ?>
          <span style="color:#fff;font-size:18px;font-weight:800"><?= e($settings['site_name']??'') ?></span>
        <?php endif; ?>
      </div>
      <?php if(!empty($settings['site_phone'])): ?><div class="footer-phone"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 014.69 12 19.79 19.79 0 011.62 3.33A2 2 0 013.6 1h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L7.91 8.59a16 16 0 006 6l.98-.87a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg><?= e($settings['site_phone']) ?></div><?php endif; ?>
      <div class="footer-support-txt">7/24 Müşteri Desteği</div>
      <div class="footer-follow-lbl">Bizi Takip Edin</div>
      <div class="footer-social">
        <?php
        $fsocials=['facebook_url'=>'<path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/>','instagram_url'=>'<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4" fill="none" stroke="currentColor" stroke-width="2"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="currentColor" stroke-width="2"/>','youtube_url'=>'<path d="M22.54 6.42a2.78 2.78 0 00-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46a2.78 2.78 0 00-1.95 1.96A29 29 0 001 12a29 29 0 00.46 5.58 2.78 2.78 0 001.95 1.95C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 001.95-1.95A29 29 0 0023 12a29 29 0 00-.46-5.58z"/><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02" fill="#111"/>','twitter_url'=>'<path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"/>'];
        foreach($fsocials as $k=>$p): if(!empty($settings[$k])): ?>
          <a href="<?= e($settings[$k]) ?>" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="currentColor" width="15" height="15"><?= $p ?></svg></a>
        <?php endif;endforeach; ?>
      </div>
    </div>
    <div class="footer-col"><h4>Kategoriler</h4><ul><?php foreach(array_slice($navCategories??[],0,6) as $c): ?><li><a href="<?= url('kategori/'.$c['slug']) ?>"><?= e($c['name']??'') ?></a></li><?php endforeach; ?></ul></div>
    <div class="footer-col"><h4>Yardım</h4><ul><li><a href="<?= url('hakkimizda') ?>">Hakkımızda</a></li><li><a href="<?= url('blog') ?>">Blog</a></li><li><a href="<?= url('iletisim') ?>">İletişim</a></li><li><a href="<?= url('sss') ?>">SSS</a></li><li><a href="<?= url('siparis-takip') ?>">Sipariş Takibi</a></li></ul></div>
    <div class="footer-col"><h4>Bilgi</h4><ul><li><a href="<?= url('gizlilik-politikasi') ?>">Gizlilik Politikası</a></li><li><a href="<?= url('iade-kosullari') ?>">İade Koşulları</a></li><li><a href="<?= url('kullanim-kosullari') ?>">Kullanım Koşulları</a></li><li><a href="<?= url('iptal-politikasi') ?>">İptal Politikası</a></li></ul></div>
    <div class="footer-office">
      <h4>İletişim</h4>
      <div class="footer-office-name"><?= e($settings['site_name']??'') ?></div>
      <p><?= e($settings['site_address']??'') ?></p>
      <?php if(!empty($settings['site_phone'])): ?><a href="tel:<?= e($settings['site_phone']) ?>"><?= e($settings['site_phone']) ?></a><?php endif; ?>
      <?php if(!empty($settings['site_email'])): ?><a href="mailto:<?= e($settings['site_email']) ?>"><?= e($settings['site_email']) ?></a><?php endif; ?>
    </div>
  </div>
  <div class="footer-bottom container">
    <div class="footer-bottom-left">
      <button class="f-select-btn">🇹🇷 Türkiye (TRY ₺) <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></button>
      <button class="f-select-btn">Türkçe <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></button>
    </div>
    <div class="footer-copy">© <?= date('Y') ?> <?= e($settings['site_name']??'') ?>. Tüm hakları saklıdır.</div>
    <div style="display:flex;align-items:center;gap:16px">
      <div class="footer-policy-links"><a href="<?= url('gizlilik-politikasi') ?>">Gizlilik</a><span style="opacity:.3">|</span><a href="<?= url('iade-kosullari') ?>">İade</a><span style="opacity:.3">|</span><a href="<?= url('kullanim-kosullari') ?>">Koşullar</a></div>
      <div class="footer-pay">
        <img src="<?= asset('images/payment/visa.svg') ?>" alt="Visa" height="20">
        <img src="<?= asset('images/payment/mastercard.svg') ?>" alt="MC" height="20">
        <img src="<?= asset('images/payment/troy.svg') ?>" alt="Troy" height="20">
        <img src="<?= asset('images/payment/paytr.svg') ?>" alt="PayTR" height="20">
      </div>
    </div>
  </div>
</footer>
<?php if(!empty($settings['whatsapp_number'])): ?>
<a href="https://wa.me/<?= preg_replace('/\D/','',$settings['whatsapp_number']) ?>?text=Merhaba" class="whatsapp-float" target="_blank" rel="noopener">
  <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.855L.057 23.43a.5.5 0 00.611.61l5.693-1.493A11.94 11.94 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.942 9.942 0 01-5.098-1.396l-.365-.218-3.781.992.993-3.678-.24-.38A9.944 9.944 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
</a>
<?php endif; ?>
<?php if(!session('cookie_consent')): ?>
<div class="cookie-bar" id="cookieBar">
  <p>Bu sitede çerezler kullanılmaktadır. <a href="<?= url('gizlilik-politikasi') ?>">Daha fazla bilgi</a></p>
  <button onclick="acceptCookies()" class="btn-cookie-accept">Kabul Et</button>
</div>
<?php endif; ?>
<script src="<?= asset('js/store.js') ?>"></script>
<?php if(!empty($extraJs)):foreach((array)$extraJs as $j): ?><script src="<?= asset('js/'.$j) ?>"></script><?php endforeach;endif; ?>
</body>
</html>
