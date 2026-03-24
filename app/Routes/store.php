<?php

use App\Core\Router;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CsrfMiddleware;

// ============================================
// MAGAZA ROTALARI
// ============================================

// ----------------------------------------
// ANASAYFA
// ----------------------------------------
$this->router->get('/',         ['App\Controllers\Store\HomeController', 'index']);
$this->router->get('/en',       ['App\Controllers\Store\HomeController', 'index']);
$this->router->get('/en/',      ['App\Controllers\Store\HomeController', 'index']);

// ----------------------------------------
// ARAMA
// ----------------------------------------
$this->router->get('/arama',    ['App\Controllers\Store\SearchController', 'index']);
$this->router->get('/en/search',['App\Controllers\Store\SearchController', 'index']);

// ----------------------------------------
// URUNLER
// ----------------------------------------
$this->router->get('/urun/:slug',    ['App\Controllers\Store\ProductController', 'show']);
$this->router->get('/en/product/:slug', ['App\Controllers\Store\ProductController', 'show']);

// Hizli satin al
$this->router->post('/urun/:slug/hizli-satin-al', ['App\Controllers\Store\ProductController', 'quickBuy'], [CsrfMiddleware::class]);

// Urun sorusu
$this->router->post('/urun/:slug/soru',   ['App\Controllers\Store\ProductController', 'askQuestion'],  [CsrfMiddleware::class]);
$this->router->post('/soru/:id/oyla',     ['App\Controllers\Store\ProductController', 'voteQuestion'],  [CsrfMiddleware::class]);

// Yorum
$this->router->post('/urun/:slug/yorum',  ['App\Controllers\Store\ReviewController', 'store'],  [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/yorum/:id/oyla',    ['App\Controllers\Store\ReviewController', 'vote'],   [CsrfMiddleware::class]);

// Fiyat alarmi
$this->router->post('/fiyat-alarmi',      ['App\Controllers\Store\ProductController', 'priceAlert'],  [CsrfMiddleware::class]);

// Stok bildirimi
$this->router->post('/stok-bildirimi',    ['App\Controllers\Store\ProductController', 'stockAlert'],  [CsrfMiddleware::class]);

// ----------------------------------------
// KATEGORILER
// ----------------------------------------
$this->router->get('/kategori/:slug',       ['App\Controllers\Store\CategoryController', 'show']);
$this->router->get('/en/category/:slug',    ['App\Controllers\Store\CategoryController', 'show']);

// ----------------------------------------
// SEPET
// ----------------------------------------
$this->router->get('/sepet',               ['App\Controllers\Store\CartController', 'index']);
$this->router->post('/sepet/ekle',         ['App\Controllers\Store\CartController', 'add'],     [CsrfMiddleware::class]);
$this->router->post('/sepet/guncelle',     ['App\Controllers\Store\CartController', 'update'],  [CsrfMiddleware::class]);
$this->router->post('/sepet/sil',          ['App\Controllers\Store\CartController', 'remove'],  [CsrfMiddleware::class]);
$this->router->post('/sepet/temizle',      ['App\Controllers\Store\CartController', 'clear'],   [CsrfMiddleware::class]);
$this->router->post('/sepet/kupon',        ['App\Controllers\Store\CartController', 'coupon'],  [CsrfMiddleware::class]);
$this->router->get('/sepet/ozet',          ['App\Controllers\Store\CartController', 'summary']);

// ----------------------------------------
// ODEME
// ----------------------------------------
$this->router->get('/odeme',               ['App\Controllers\Store\CheckoutController', 'index']);
$this->router->post('/odeme',              ['App\Controllers\Store\CheckoutController', 'process'], [CsrfMiddleware::class]);
$this->router->post('/odeme/adres',        ['App\Controllers\Store\CheckoutController', 'address'], [CsrfMiddleware::class]);
$this->router->get('/odeme/basarili',      ['App\Controllers\Store\CheckoutController', 'success']);
$this->router->get('/odeme/basarisiz',     ['App\Controllers\Store\CheckoutController', 'failed']);
$this->router->post('/webhook/paytr',      ['App\Controllers\Store\CheckoutController', 'webhook']);

// ----------------------------------------
// SIPARIS TAKIBI (UYELIKSIZ)
// ----------------------------------------
$this->router->get('/siparis-takip',       ['App\Controllers\Store\TrackController', 'index']);
$this->router->post('/siparis-takip',      ['App\Controllers\Store\TrackController', 'track'],  [CsrfMiddleware::class]);
$this->router->get('/siparis-takip/:no',   ['App\Controllers\Store\TrackController', 'show']);

// ----------------------------------------
// UYELIK & GIRIS
// ----------------------------------------
$this->router->get('/giris',               ['App\Controllers\Store\AuthController', 'loginForm'],    [GuestMiddleware::class]);
$this->router->post('/giris',              ['App\Controllers\Store\AuthController', 'login'],         [GuestMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/kayit',               ['App\Controllers\Store\AuthController', 'registerForm'],  [GuestMiddleware::class]);
$this->router->post('/kayit',              ['App\Controllers\Store\AuthController', 'register'],      [GuestMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/cikis',               ['App\Controllers\Store\AuthController', 'logout']);
$this->router->get('/sifremi-unuttum',     ['App\Controllers\Store\AuthController', 'forgotForm'],    [GuestMiddleware::class]);
$this->router->post('/sifremi-unuttum',    ['App\Controllers\Store\AuthController', 'forgot'],        [GuestMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/sifre-sifirla/:token',['App\Controllers\Store\AuthController', 'resetForm'],     [GuestMiddleware::class]);
$this->router->post('/sifre-sifirla',      ['App\Controllers\Store\AuthController', 'reset'],         [GuestMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/email-dogrula/:token',['App\Controllers\Store\AuthController', 'verifyEmail']);

// Sosyal Giris
$this->router->get('/auth/google',         ['App\Controllers\Store\AuthController', 'socialGoogle']);
$this->router->get('/auth/google/callback',['App\Controllers\Store\AuthController', 'socialGoogleCallback']);
$this->router->get('/auth/facebook',       ['App\Controllers\Store\AuthController', 'socialFacebook']);
$this->router->get('/auth/facebook/callback', ['App\Controllers\Store\AuthController', 'socialFacebookCallback']);
$this->router->get('/auth/apple',          ['App\Controllers\Store\AuthController', 'socialApple']);
$this->router->get('/auth/apple/callback', ['App\Controllers\Store\AuthController', 'socialAppleCallback']);
$this->router->get('/auth/yandex',         ['App\Controllers\Store\AuthController', 'socialYandex']);
$this->router->get('/auth/yandex/callback',['App\Controllers\Store\AuthController', 'socialYandexCallback']);

// Misafir -> Hesap olustur
$this->router->post('/siparis-ile-kayit',  ['App\Controllers\Store\AuthController', 'registerWithOrder'], [CsrfMiddleware::class]);

// ----------------------------------------
// HESABIM
// ----------------------------------------
$this->router->get('/hesabim',                     ['App\Controllers\Store\AccountController', 'index'],         [AuthMiddleware::class]);
$this->router->get('/hesabim/siparisler',          ['App\Controllers\Store\AccountController', 'orders'],        [AuthMiddleware::class]);
$this->router->get('/hesabim/siparisler/:no',      ['App\Controllers\Store\AccountController', 'orderDetail'],   [AuthMiddleware::class]);
$this->router->post('/hesabim/siparisler/:no/iade',['App\Controllers\Store\AccountController', 'returnRequest'], [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/hesabim/faturalar',           ['App\Controllers\Store\AccountController', 'invoices'],      [AuthMiddleware::class]);
$this->router->get('/hesabim/faturalar/:id/indir', ['App\Controllers\Store\AccountController', 'downloadInvoice'],[AuthMiddleware::class]);
$this->router->get('/hesabim/mesajlar',            ['App\Controllers\Store\AccountController', 'messages'],      [AuthMiddleware::class]);
$this->router->get('/hesabim/adresler',            ['App\Controllers\Store\AccountController', 'addresses'],     [AuthMiddleware::class]);
$this->router->post('/hesabim/adresler/ekle',      ['App\Controllers\Store\AccountController', 'addAddress'],    [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/hesabim/adresler/:id/duzenle',['App\Controllers\Store\AccountController', 'editAddress'],  [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/hesabim/adresler/:id/sil',   ['App\Controllers\Store\AccountController', 'deleteAddress'], [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/hesabim/profil',              ['App\Controllers\Store\AccountController', 'profile'],       [AuthMiddleware::class]);
$this->router->post('/hesabim/profil',             ['App\Controllers\Store\AccountController', 'updateProfile'], [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/hesabim/sifre-degistir',     ['App\Controllers\Store\AccountController', 'changePassword'],[AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->get('/hesabim/kvkk',                ['App\Controllers\Store\AccountController', 'gdpr'],          [AuthMiddleware::class]);
$this->router->post('/hesabim/kvkk/talep',         ['App\Controllers\Store\AccountController', 'gdprRequest'],   [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/hesabim/kvkk/iptal',         ['App\Controllers\Store\AccountController', 'gdprCancel'],    [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------
// FAVORILER
// ----------------------------------------
$this->router->get('/favoriler',           ['App\Controllers\Store\WishlistController', 'index'],  [AuthMiddleware::class]);
$this->router->post('/favoriler/ekle',     ['App\Controllers\Store\WishlistController', 'add'],    [AuthMiddleware::class, CsrfMiddleware::class]);
$this->router->post('/favoriler/sil',      ['App\Controllers\Store\WishlistController', 'remove'], [AuthMiddleware::class, CsrfMiddleware::class]);

// ----------------------------------------
// BLOG
// ----------------------------------------
$this->router->get('/blog',                ['App\Controllers\Store\BlogController', 'index']);
$this->router->get('/blog/:slug',          ['App\Controllers\Store\BlogController', 'show']);
$this->router->get('/en/blog',             ['App\Controllers\Store\BlogController', 'index']);
$this->router->get('/en/blog/:slug',       ['App\Controllers\Store\BlogController', 'show']);

// ----------------------------------------
// BULTEN ABONELIGI
// ----------------------------------------
$this->router->post('/bulten/abone',       ['App\Controllers\Store\HomeController', 'newsletter'],  [CsrfMiddleware::class]);
$this->router->get('/bulten/iptal/:token', ['App\Controllers\Store\HomeController', 'unsubscribe']);

// ----------------------------------------
// CEREZ ONAYI
// ----------------------------------------
$this->router->post('/cerez-onayi',        ['App\Controllers\Store\HomeController', 'cookieConsent'], [CsrfMiddleware::class]);

// ----------------------------------------
// AJAX - IL/ILCE/MAHALLE
// ----------------------------------------
$this->router->get('/ajax/ilceler/:city_id',       ['App\Controllers\Store\CheckoutController', 'districts']);
$this->router->get('/ajax/mahalleler/:district_id', ['App\Controllers\Store\CheckoutController', 'neighborhoods']);

// ----------------------------------------
// SITEMAP & SEO
// ----------------------------------------
$this->router->get('/sitemap.xml',         ['App\Controllers\Store\HomeController', 'sitemap']);
$this->router->get('/sitemap-products.xml',['App\Controllers\Store\HomeController', 'sitemapProducts']);
$this->router->get('/sitemap-categories.xml',['App\Controllers\Store\HomeController', 'sitemapCategories']);
$this->router->get('/sitemap-blog.xml',    ['App\Controllers\Store\HomeController', 'sitemapBlog']);
$this->router->get('/robots.txt',          ['App\Controllers\Store\HomeController', 'robots']);
$this->router->get('/google-merchant.xml', ['App\Controllers\Store\HomeController', 'merchantFeed']);

// ----------------------------------------
// STATIK SAYFALAR (en son olmali)
// ----------------------------------------
$this->router->get('/:slug',               ['App\Controllers\Store\PageController', 'show']);
$this->router->get('/en/:slug',            ['App\Controllers\Store\PageController', 'show']);
