<?php ob_start(); $pageTitle=($settings['site_name']??'Mağaza').' — Elektronik & Teknoloji'; $extraJs=['home.js']; ?>

<!-- HERO 3 KOLON -->
<section class="hero-section">
  <div class="hero-grid container">

    <!-- SOL: Mini bannerlar -->
    <div class="hero-left">
      <a href="<?= url('urunler') ?>" class="hero-side-card" style="background:#f0f4f0">
        <div>
          <div class="hsc-tag">Dijital Kurulum</div>
          <h2 class="hsc-title">Dijital Kurulumunuzu Güçlendirin</h2>
          <div class="hsc-price">120 ₺'den başlayan</div>
          <span class="hsc-link">Hemen Al →</span>
        </div>
      </a>
      <a href="<?= url('urunler') ?>" class="hero-side-card" style="background:#f0f4ec">
        <div>
          <div class="hsc-tag">Kampanya</div>
          <h2 class="hsc-title">Kolay Alışveriş</h2>
          <div class="hsc-sub">Güvenilir teslimat, sorunsuz iade</div>
          <span class="hsc-link">Keşfet →</span>
        </div>
      </a>
    </div>

    <!-- ORTA: Ana slider -->
    <div class="hero-main-slider" id="heroSlider">
      <?php if(!empty($sliders)): ?>
        <?php foreach($sliders as $i=>$sl): ?>
          <div class="hero-slide <?= $i===0?'active':'' ?>">
            <?php if($sl['image']): ?>
              <div class="slide-bg-img" style="background-image:url('<?= uploadUrl('sliders/'.$sl['image']) ?>')"></div>
            <?php else: ?>
              <div class="slide-bg-img" style="background:linear-gradient(135deg,#e8f0fe 0%,#d0e8ff 100%)"></div>
            <?php endif; ?>
            <div class="slide-body">
              <div class="slide-tag"><span class="slide-tag-dot"></span> Yeni Ürün</div>
              <h1 class="slide-title"><?= e($sl['title'] ?? 'Harika Teknoloji Fırsatları') ?></h1>
              <p class="slide-desc"><?= e($sl['subtitle'] ?? 'En iyi markalar, garantili teslimat, kolay iade') ?></p>
              <?php if($sl['link']): ?>
                <a href="<?= e($sl['link']) ?>" class="slide-cta">Keşfet →</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="hero-slide active">
          <div class="slide-bg-img" style="background:linear-gradient(135deg,#e8f0fe 0%,#dce8ff 100%)"></div>
          <div class="slide-body">
            <div class="slide-tag"><span class="slide-tag-dot"></span> Yeni Sezon</div>
            <h1 class="slide-title">En İyi Teknoloji Fırsatları</h1>
            <p class="slide-desc">Binlerce ürün, garantili teslimat, kolay iade</p>
            <a href="<?= url('urunler') ?>" class="slide-cta">Alışverişe Başla →</a>
          </div>
        </div>
      <?php endif; ?>

      <?php if(count($sliders??[])>1): ?>
      <div class="slider-footer">
        <button class="slider-nav-btn" onclick="hSlide(-1)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <span class="slider-counter" id="sliderCounter">1 / <?= count($sliders) ?></span>
        <button class="slider-nav-btn" onclick="hSlide(1)">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
      <?php endif; ?>
    </div>

    <!-- SAĞ: Mini bannerlar -->
    <div class="hero-right">
      <?php if(!empty($banners)): ?>
        <?php foreach(array_slice($banners,0,2) as $b): ?>
        <a href="<?= e($b['link']??'#') ?>" class="hero-side-card" style="background:#f0f0f8">
          <?php if($b['image']): ?><img src="<?= uploadUrl('banners/'.$b['image']) ?>" alt="" class="hsc-img"><?php endif; ?>
          <div>
            <h3 class="hsc-title" style="font-size:16px">Yeni Gelenler</h3>
            <div class="hsc-price">135 ₺'den başlayan</div>
            <span class="hsc-link">İncele →</span>
          </div>
        </a>
        <?php endforeach; ?>
      <?php else: ?>
        <a href="<?= url('urunler') ?>" class="hero-side-card" style="background:#f0f0f8">
          <div><h3 class="hsc-title" style="font-size:16px">Galaxy Serisini Keşfedin</h3><div class="hsc-price">135 ₺'den başlayan</div><span class="hsc-link">Alışveriş Yap →</span></div>
        </a>
        <a href="<?= url('urunler') ?>" class="hero-side-card" style="background:#f4f0f0">
          <div><h3 class="hsc-title" style="font-size:16px">Harika Ses Deneyimi</h3><div class="hsc-price">135 ₺'den başlayan</div><span class="hsc-link">Keşfet →</span></div>
        </a>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ÖNE ÇIKAN ÜRÜNLER -->
<?php if(!empty($featuredProducts)): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">Öne Çıkan Ürünler</h2>
        <p class="section-sub">Yeni Pixel Cihazlarında İnanılmaz Tasarruflar</p>
      </div>
      <a href="<?= url('urunler') ?>" class="section-link">Tüm Ürünleri Gör →</a>
    </div>
    <div class="pg-6">
      <?php foreach($featuredProducts as $p): echo productCard($p); endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- KATEGORİLER -->
<?php if(!empty($categories)): ?>
<section style="padding:0 0 48px">
  <div class="container">
    <div class="cat-grid-8">
      <?php foreach($categories as $cat): ?>
        <a href="<?= url('kategori/'.$cat['slug']) ?>" class="cat-card">
          <div class="cat-name"><?= e($cat['name']??'') ?></div>
          <div class="cat-desc">Keşfedin</div>
          <?php if($cat['image']??''): ?><img src="<?= uploadUrl('categories/'.$cat['image']) ?>" alt="" class="cat-img"><?php endif; ?>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FLASH SALE -->
<?php if(!empty($bestSellers)): ?>
<section class="flash-section">
  <div class="container">
    <div class="flash-grid">
      <!-- Sol banner -->
      <div class="flash-banner">
        <div>
          <div class="flash-tag">Flash Fırsat</div>
          <h2 class="flash-title">Sınırlı Süreli Flaş Fırsat</h2>
          <p class="flash-sub">Tüm Ürünlerde %50 İndirim</p>
          <a href="<?= url('urunler') ?>" class="flash-link">Fırsatı Kap →</a>
        </div>
        <div class="flash-timer">
          <div class="flash-timer-label">Sadece Sınırlı Süre</div>
          <div class="timer-units">
            <div class="tu" id="t-days">00</div>
            <span class="tsep">:</span>
            <div class="tu" id="t-hours">00</div>
            <span class="tsep">:</span>
            <div class="tu" id="t-mins">00</div>
            <span class="tsep">:</span>
            <div class="tu" id="t-secs">00</div>
          </div>
        </div>
      </div>
      <!-- Sağ ürünler -->
      <div class="flash-products-wrap">
        <div class="flash-prods-header">
          <div>
            <h3>Sınırlı Süreli Flaş Fırsat</h3>
            <p>Her Üründe %50 İndirim</p>
          </div>
          <div class="flash-nav">
            <button class="flash-nav-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
            </button>
            <button class="flash-nav-btn">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
            </button>
          </div>
        </div>
        <div class="flash-prods-grid">
          <?php foreach(array_slice($bestSellers,0,4) as $p): echo productCard($p); endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- MARQUEE BAR -->
<div class="marquee-bar">
  <div class="marquee-track">
    <?php for($i=0;$i<4;$i++): ?>
      <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/></svg> Akıllı Alışveriş, Büyük Tasarruf</span>
      <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Elektronikte %50'ye Varan İndirim</span>
      <span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg> Sadece 99 ₺'den Başlayan Kulaklıklar</span>
    <?php endfor; ?>
  </div>
</div>

<!-- 4 BANNER -->
<?php if(!empty($banners)&&count($banners)>=2): ?>
<section style="padding:32px 0">
  <div class="container">
    <div class="b4-grid">
      <?php
      $bColors=['#fef3e8','#e8f4fe','#f0f8e8','#fce8f4'];
      $bTitles=['Ultra Netlik 4K QLED','Güç Tableti Satışı','Akıllı Ev Elektroniği','Hassas Günlük Bakım'];
      $bSubs=['Canlı Renklerle Görüntüleme Deneyimini Yükseltin','En Gelişmiş Chromebook Plus Şimdi','%20 İndiriyle Akıllıca Tasarruf Edin','Konforunuzu Her Gün Daha İyiye Taşıyın'];
      foreach(array_slice($banners,0,4) as $bi=>$b):
      ?>
        <a href="<?= e($b['link']??'#') ?>" class="b4-card" style="background:<?= $bColors[$bi%4] ?>">
          <?php if($b['image']): ?><img src="<?= uploadUrl('banners/'.$b['image']) ?>" alt="" class="b4-img"><?php endif; ?>
          <div class="b4-price">Başlangıç 99 ₺</div>
          <div>
            <div class="b4-tag">Sınırlı Süre</div>
            <div class="b4-title"><?= $bTitles[$bi%4] ?></div>
            <div class="b4-sub"><?= $bSubs[$bi%4] ?></div>
            <span class="b4-link">İncele →</span>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if(count($banners)<4): for($bi=count($banners);$bi<4;$bi++): ?>
        <div class="b4-card" style="background:<?= $bColors[$bi%4] ?>">
          <div class="b4-title"><?= $bTitles[$bi%4] ?></div>
          <div class="b4-sub"><?= $bSubs[$bi%4] ?></div>
          <span class="b4-link">İncele →</span>
        </div>
      <?php endfor;endif; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- YENİ GELENLER -->
<?php if(!empty($newArrivals)): ?>
<section class="section" style="background:#fafafa;border-top:1px solid #eee;border-bottom:1px solid #eee">
  <div class="container">
    <div class="section-header">
      <div>
        <h2 class="section-title">Sizi Birbirine Bağlayan Teknoloji</h2>
        <p class="section-sub">En yeni ve trend ürünleri keşfedin</p>
      </div>
      <a href="<?= url('urunler') ?>" class="section-link">Tümünü Gör →</a>
    </div>
    <div class="pg-6">
      <?php foreach($newArrivals as $p): echo productCard($p); endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- TECH DROPS (Sayaçlı) -->
<section class="tech-drops-section">
  <div class="container">
    <div class="tech-drops-box">
      <div class="tdb-inner">
        <div>
          <div class="tdb-head-label">⚡ TECH DROPS — Site Özel</div>
          <div class="tdb-sub">Sınırlı Sayıda Ürün & Erken Erişim Fırsatları</div>
          <div class="tdb-next" style="margin-top:16px">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Sonraki Drop
          </div>
          <div class="tdb-counter">
            <div class="tdb-unit"><span id="d-days">00</span><small>Gün</small></div>
            <span class="tdb-sep">:</span>
            <div class="tdb-unit"><span id="d-hours">00</span><small>Saat</small></div>
            <span class="tdb-sep">:</span>
            <div class="tdb-unit"><span id="d-mins">00</span><small>Dk</small></div>
            <span class="tdb-sep">:</span>
            <div class="tdb-unit"><span id="d-secs">00</span><small>Sn</small></div>
          </div>
          <a href="<?= url('urunler') ?>" class="tdb-link">Tüm Fırsatları Gör →</a>
        </div>
        <div class="drops-wrap">
          <button class="drops-nav" onclick="dropsNav(-1)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M15 18l-6-6 6-6"/></svg>
          </button>
          <div class="drops-scr" id="dropsScroll">
            <?php foreach(array_slice($newArrivals,0,4) as $p):
              $img=$p['cover_image']??null;
            ?>
            <div class="drop-card">
              <div class="drop-date">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Yarın Düşüyor
              </div>
              <?php if($img): ?>
                <img src="<?= uploadUrl('products/'.$img) ?>" alt="" class="drop-img">
              <?php else: ?>
                <div class="drop-img" style="background:#f5f5f5;border-radius:6px"></div>
              <?php endif; ?>
              <div class="drop-name"><?= e($p['name']??'') ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <button class="drops-nav" onclick="dropsNav(1)">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 18l6-6-6-6"/></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MARKALAR -->
<section style="padding:32px 0;border-top:1px solid #eee">
  <div class="container">
    <div class="section-header" style="margin-bottom:16px">
      <div>
        <h2 class="section-title">Önde Gelen Markalar</h2>
        <p class="section-sub">İnovasyon ve kalitenin buluştuğu markaları keşfedin</p>
      </div>
      <a href="<?= url('markalar') ?>" class="section-link">Tüm Markaları Gör →</a>
    </div>
    <div class="brands-grid">
      <?php
      $brandNames=['Samsung','Apple','Sony','LG','Huawei','Xiaomi','ASUS','Lenovo'];
      for($i=0;$i<8;$i++): ?>
        <div class="brand-card">
          <span class="brand-card-text"><?= $brandNames[$i] ?></span>
        </div>
      <?php endfor; ?>
    </div>
  </div>
</section>

<!-- BLOG -->
<?php if(!empty($blogs)): ?>
<section class="section" style="background:#fafafa;border-top:1px solid #eee">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Blog</h2>
      <a href="<?= url('blog') ?>" class="section-link">Tüm Yazılar →</a>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px">
      <?php foreach($blogs as $post): ?>
        <a href="<?= url('blog/'.($post['blog_slug']??$post['slug']??'')) ?>"
           style="background:#fff;border:1px solid #e8e8e8;border-radius:8px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow .2s">
          <?php if($post['image']??''): ?>
            <div style="aspect-ratio:16/9;overflow:hidden"><img src="<?= uploadUrl('blog/'.$post['image']) ?>" alt="" style="width:100%;height:100%;object-fit:cover"></div>
          <?php endif; ?>
          <div style="padding:16px">
            <div style="font-size:11px;color:#999;margin-bottom:6px"><?= date('d M Y',strtotime($post['published_at']??$post['created_at']??'now')) ?></div>
            <div style="font-size:14px;font-weight:700;line-height:1.4;margin-bottom:6px"><?= e($post['title']??'') ?></div>
            <div style="font-size:12.5px;color:#555;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden"><?= e($post['excerpt']??'') ?></div>
            <div style="font-size:12.5px;font-weight:600;color:#e8000d;margin-top:10px">Devamını Oku →</div>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- GÜVEN BANTLARI -->
<div class="container">
  <div class="trust-5">
    <?php
    $trusts=[
      ['icon'=>'<path d="M5 12h14M12 5l7 7-7 7"/>','title'=>'Aynı Gün Teslimat','desc'=>'Belirli siparişlerde hızlı teslimat'],
      ['icon'=>'<path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/>','title'=>'En Son Ürün & Fırsatlar','desc'=>'Yeni Gelişmeleri Takip Edin'],
      ['icon'=>'<rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>','title'=>'Kolay & Güvenli Ödeme','desc'=>'Güvenle Aylık Ödeme Yapın'],
      ['icon'=>'<polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/>','title'=>'Cihaz Takas Programı','desc'=>'Eski Cihazınızı Daha Fazlası İçin Verin'],
      ['icon'=>'<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>','title'=>'%100 Orijinal Ürünler','desc'=>'Güvenle Alışveriş Yapın'],
    ];
    foreach($trusts as $t): ?>
    <div class="trust-item">
      <div class="trust-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><?= $t['icon'] ?></svg>
      </div>
      <strong><?= $t['title'] ?></strong>
      <span><?= $t['desc'] ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
// ─── PRODUCT CARD ─────────────────────────────────────────
function productCard(array $p): string {
  $price   = (float)($p['sale_price'] ?: $p['price']);
  $old     = $p['sale_price'] ? (float)$p['price'] : null;
  $disc    = $p['discount_pct'] ?? 0;
  $cover   = $p['cover_image'] ?? null;
  $hover   = $p['hover_image'] ?? null;
  $name    = e($p['name'] ?? 'Ürün');
  $slug    = $p['slug'];
  $catName = e($p['category_name'] ?? '');
  $inStock = ($p['stock_status'] ?? 'in_stock') === 'in_stock';
  ob_start(); ?>
  <div class="product-card">
    <a href="<?= url('urun/'.$slug) ?>" style="display:block;position:relative;aspect-ratio:1;background:#f8f8f8;border-radius:6px;overflow:hidden">
      <?php if($cover): ?>
        <img src="<?= uploadUrl('products/'.$cover) ?>" alt="<?= $name ?>" class="pc-main-img">
        <?php if($hover): ?><img src="<?= uploadUrl('products/'.$hover) ?>" alt="" class="pc-hover-img"><?php endif; ?>
      <?php else: ?>
        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#d0d0d0" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
        </div>
      <?php endif; ?>
      <?php if($disc>=5): ?><span class="sale-badge">İndirim</span><?php endif; ?>
      <?php if($inStock): ?>
        <button class="quick-add" onclick="event.preventDefault();quickAddToCart(<?= $p['id'] ?>,this)" title="Sepete Ekle">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        </button>
      <?php endif; ?>
    </a>
    <div class="pc-name"><a href="<?= url('urun/'.$slug) ?>"><?= $name ?></a></div>
    <?php if($catName): ?><div class="pc-color-label"><strong><?= $catName ?></strong></div><?php endif; ?>
    <div class="pc-price-row">
      <span class="pc-price"><?= formatPrice($price) ?></span>
      <?php if($old): ?><span class="pc-old"><?= formatPrice($old) ?></span><?php endif; ?>
    </div>
  <div class="pc-tag <?= $inStock ? 'green' : '' ?>">
      <?php if($inStock): ?>
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg> Güvenli Teslimat
      <?php else: ?>
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg> Tükendi
      <?php endif; ?>
    </div>
  </div>
  <?php return ob_get_clean();
}
?>

<?php
$content = ob_get_clean();
require APP_PATH . '/Views/Store/layouts/main.php';
?>
