<?php

use App\Core\Router;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;

$adminPath = $_ENV['APP_ADMIN_PATH'] ?? 'yonetim';

// ============================================
// ADMIN ROTALARI
// ============================================

$this->router->group('/' . $adminPath, function(Router $r) {

    // ----------------------------------------
    // GIRIS / CIKIS (middleware yok)
    // ----------------------------------------
    $r->get('/giris',   ['App\Controllers\Admin\AuthController', 'loginForm']);
    $r->post('/giris',  ['App\Controllers\Admin\AuthController', 'login'],   [CsrfMiddleware::class]);
    $r->get('/cikis',   ['App\Controllers\Admin\AuthController', 'logout']);
    $r->get('/2fa',     ['App\Controllers\Admin\AuthController', 'twoFactorForm']);
    $r->post('/2fa',    ['App\Controllers\Admin\AuthController', 'twoFactor'], [CsrfMiddleware::class]);

    // ----------------------------------------
    // DASHBOARD
    // ----------------------------------------
    $r->get('',         ['App\Controllers\Admin\DashboardController', 'index'],  [AdminMiddleware::class]);
    $r->get('/',        ['App\Controllers\Admin\DashboardController', 'index'],  [AdminMiddleware::class]);

    // ----------------------------------------
    // URUNLER
    // ----------------------------------------
    $r->get('/urunler',                     ['App\Controllers\Admin\ProductController', 'index'],   [AdminMiddleware::class]);
    $r->get('/urunler/ekle',                ['App\Controllers\Admin\ProductController', 'create'],  [AdminMiddleware::class]);
    $r->post('/urunler/ekle',               ['App\Controllers\Admin\ProductController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/urunler/:id/duzenle',         ['App\Controllers\Admin\ProductController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/urunler/:id/duzenle',        ['App\Controllers\Admin\ProductController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/urunler/:id/sil',            ['App\Controllers\Admin\ProductController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/urunler/:id/gorsel-sil',     ['App\Controllers\Admin\ProductController', 'deleteImage'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/urunler/:id/gorsel-sirala',  ['App\Controllers\Admin\ProductController', 'sortImages'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/urunler/csv-indir',           ['App\Controllers\Admin\ProductController', 'exportCsv'],   [AdminMiddleware::class]);
    $r->post('/urunler/csv-yukle',          ['App\Controllers\Admin\ProductController', 'importCsv'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/urunler/slug-kontrol',       ['App\Controllers\Admin\ProductController', 'checkSlug'],   [AdminMiddleware::class]);

    // ----------------------------------------
    // KATEGORILER
    // ----------------------------------------
    $r->get('/kategoriler',              ['App\Controllers\Admin\CategoryController', 'index'],   [AdminMiddleware::class]);
    $r->get('/kategoriler/ekle',         ['App\Controllers\Admin\CategoryController', 'create'],  [AdminMiddleware::class]);
    $r->post('/kategoriler/ekle',        ['App\Controllers\Admin\CategoryController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/kategoriler/ekle-ajax',   ['App\Controllers\Admin\CategoryController', 'storeAjax'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/kategoriler/:id/duzenle',  ['App\Controllers\Admin\CategoryController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/kategoriler/:id/duzenle', ['App\Controllers\Admin\CategoryController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/kategoriler/:id/sil',     ['App\Controllers\Admin\CategoryController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // MARKALAR
    // ----------------------------------------
    $r->get('/markalar',              ['App\Controllers\Admin\BrandController', 'index'],   [AdminMiddleware::class]);
    $r->get('/markalar/ekle',         ['App\Controllers\Admin\BrandController', 'create'],  [AdminMiddleware::class]);
    $r->post('/markalar/ekle',        ['App\Controllers\Admin\BrandController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/markalar/ekle-ajax',   ['App\Controllers\Admin\BrandController', 'storeAjax'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/markalar/:id/duzenle',  ['App\Controllers\Admin\BrandController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/markalar/:id/duzenle', ['App\Controllers\Admin\BrandController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/markalar/:id/sil',     ['App\Controllers\Admin\BrandController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // VARYASYON NİTELİKLERİ
    // ----------------------------------------
    $r->get('/nitelikler',                   ['App\Controllers\Admin\AttributeController', 'index'],        [AdminMiddleware::class]);
    $r->post('/nitelikler/ekle',             ['App\Controllers\Admin\AttributeController', 'store'],        [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/nitelikler/ekle-ajax',        ['App\Controllers\Admin\AttributeController', 'storeAjax'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/nitelikler/:id/duzenle',      ['App\Controllers\Admin\AttributeController', 'update'],       [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/nitelikler/:id/sil',          ['App\Controllers\Admin\AttributeController', 'destroy'],      [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/nitelikler/:id/secenek',      ['App\Controllers\Admin\AttributeController', 'storeOption'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/nitelikler/secenek/:id/sil',  ['App\Controllers\Admin\AttributeController', 'destroyOption'],[AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // DEGERLENDIRMELER
    // ----------------------------------------
    $r->get('/degerlendirmeler',               ['App\Controllers\Admin\ReviewController', 'index'],   [AdminMiddleware::class]);
    $r->get('/degerlendirmeler/ekle',          ['App\Controllers\Admin\ReviewController', 'create'],  [AdminMiddleware::class]);
    $r->post('/degerlendirmeler/ekle',         ['App\Controllers\Admin\ReviewController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/degerlendirmeler/:id/onayla',   ['App\Controllers\Admin\ReviewController', 'approve'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/degerlendirmeler/:id/reddet',   ['App\Controllers\Admin\ReviewController', 'reject'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/degerlendirmeler/:id/sil',      ['App\Controllers\Admin\ReviewController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // SIPARISLER
    // ----------------------------------------
    $r->get('/siparisler',                      ['App\Controllers\Admin\OrderController', 'index'],        [AdminMiddleware::class]);
    $r->get('/siparisler/:id',                  ['App\Controllers\Admin\OrderController', 'show'],         [AdminMiddleware::class]);
    $r->post('/siparisler/:id/durum',           ['App\Controllers\Admin\OrderController', 'updateStatus'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/siparisler/:id/kargo',           ['App\Controllers\Admin\OrderController', 'updateCargo'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/siparisler/:id/not',             ['App\Controllers\Admin\OrderController', 'addNote'],      [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/siparisler/:id/kargo-etiketi',    ['App\Controllers\Admin\OrderController', 'cargoLabel'],   [AdminMiddleware::class]);
    $r->post('/siparisler/toplu-guncelle',      ['App\Controllers\Admin\OrderController', 'bulkUpdate'],   [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // IADELER
    // ----------------------------------------
    $r->get('/iadeler',                  ['App\Controllers\Admin\ReturnController', 'index'],   [AdminMiddleware::class]);
    $r->get('/iadeler/:id',              ['App\Controllers\Admin\ReturnController', 'show'],    [AdminMiddleware::class]);
    $r->post('/iadeler/:id/onayla',      ['App\Controllers\Admin\ReturnController', 'approve'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/iadeler/:id/reddet',      ['App\Controllers\Admin\ReturnController', 'reject'],  [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // FATURALAR
    // ----------------------------------------
    $r->get('/faturalar',                ['App\Controllers\Admin\InvoiceController', 'index'],  [AdminMiddleware::class]);
    $r->post('/faturalar/yukle',         ['App\Controllers\Admin\InvoiceController', 'upload'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/faturalar/:id/indir',      ['App\Controllers\Admin\InvoiceController', 'download'], [AdminMiddleware::class]);
    $r->post('/faturalar/:id/sil',       ['App\Controllers\Admin\InvoiceController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // KUPONLAR
    // ----------------------------------------
    $r->get('/kuponlar',                    ['App\Controllers\Admin\CouponController', 'index'],    [AdminMiddleware::class]);
    $r->get('/kuponlar/ekle',               ['App\Controllers\Admin\CouponController', 'create'],   [AdminMiddleware::class]);
    $r->post('/kuponlar/ekle',              ['App\Controllers\Admin\CouponController', 'store'],    [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/kuponlar/toplu-olustur',     ['App\Controllers\Admin\CouponController', 'generate'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/kuponlar/:id/duzenle',        ['App\Controllers\Admin\CouponController', 'edit'],     [AdminMiddleware::class]);
    $r->post('/kuponlar/:id/duzenle',       ['App\Controllers\Admin\CouponController', 'update'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/kuponlar/:id/sil',           ['App\Controllers\Admin\CouponController', 'destroy'],  [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // MUSTERILER
    // ----------------------------------------
    $r->get('/musteriler',              ['App\Controllers\Admin\CustomerController', 'index'],  [AdminMiddleware::class]);
    $r->get('/musteriler/:id',          ['App\Controllers\Admin\CustomerController', 'show'],   [AdminMiddleware::class]);
    $r->post('/musteriler/:id/duzenle', ['App\Controllers\Admin\CustomerController', 'update'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/musteriler/:id/sil',     ['App\Controllers\Admin\CustomerController', 'destroy'],[AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/misafir-siparisler',      ['App\Controllers\Admin\CustomerController', 'guests'], [AdminMiddleware::class]);

    // ----------------------------------------
    // BULTEN
    // ----------------------------------------
    $r->get('/bulten',              ['App\Controllers\Admin\NewsletterController', 'index'],  [AdminMiddleware::class]);
    $r->post('/bulten/gonder',      ['App\Controllers\Admin\NewsletterController', 'send'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/bulten/:id/sil',     ['App\Controllers\Admin\NewsletterController', 'destroy'],[AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/bulten/csv-indir',    ['App\Controllers\Admin\NewsletterController', 'export'], [AdminMiddleware::class]);

    // ----------------------------------------
    // KVKK
    // ----------------------------------------
    $r->get('/kvkk',                   ['App\Controllers\Admin\GdprController', 'index'],   [AdminMiddleware::class]);
    $r->post('/kvkk/:id/onayla',       ['App\Controllers\Admin\GdprController', 'approve'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/kvkk/:id/reddet',       ['App\Controllers\Admin\GdprController', 'reject'],  [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // PAZARLAMA
    // ----------------------------------------
    $r->get('/sliderlar',              ['App\Controllers\Admin\SliderController', 'index'],   [AdminMiddleware::class]);
    $r->get('/sliderlar/ekle',         ['App\Controllers\Admin\SliderController', 'create'],  [AdminMiddleware::class]);
    $r->post('/sliderlar/ekle',        ['App\Controllers\Admin\SliderController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/sliderlar/:id/duzenle',  ['App\Controllers\Admin\SliderController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/sliderlar/:id/duzenle', ['App\Controllers\Admin\SliderController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/sliderlar/:id/sil',     ['App\Controllers\Admin\SliderController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/sliderlar/sirala',      ['App\Controllers\Admin\SliderController', 'sort'],    [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/bannerlar',              ['App\Controllers\Admin\BannerController', 'index'],   [AdminMiddleware::class]);
    $r->post('/bannerlar/ekle',        ['App\Controllers\Admin\BannerController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/bannerlar/:id/duzenle', ['App\Controllers\Admin\BannerController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/bannerlar/:id/sil',     ['App\Controllers\Admin\BannerController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/popuplar',               ['App\Controllers\Admin\PopupController', 'index'],   [AdminMiddleware::class]);
    $r->post('/popuplar/ekle',         ['App\Controllers\Admin\PopupController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/popuplar/:id/duzenle',  ['App\Controllers\Admin\PopupController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/popuplar/:id/sil',      ['App\Controllers\Admin\PopupController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/terk-sepetler',          ['App\Controllers\Admin\OrderController', 'abandonedCarts'], [AdminMiddleware::class]);

    // ----------------------------------------
    // BLOG
    // ----------------------------------------
    $r->get('/blog',              ['App\Controllers\Admin\BlogController', 'index'],   [AdminMiddleware::class]);
    $r->get('/blog/ekle',         ['App\Controllers\Admin\BlogController', 'create'],  [AdminMiddleware::class]);
    $r->post('/blog/ekle',        ['App\Controllers\Admin\BlogController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/blog/:id/duzenle',  ['App\Controllers\Admin\BlogController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/blog/:id/duzenle', ['App\Controllers\Admin\BlogController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/blog/:id/sil',     ['App\Controllers\Admin\BlogController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // ICERIK
    // ----------------------------------------
    $r->get('/sayfalar',              ['App\Controllers\Admin\PageController', 'index'],   [AdminMiddleware::class]);
    $r->get('/sayfalar/ekle',         ['App\Controllers\Admin\PageController', 'create'],  [AdminMiddleware::class]);
    $r->post('/sayfalar/ekle',        ['App\Controllers\Admin\PageController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/sayfalar/:id/duzenle',  ['App\Controllers\Admin\PageController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/sayfalar/:id/duzenle', ['App\Controllers\Admin\PageController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/sayfalar/:id/sil',     ['App\Controllers\Admin\PageController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/menuler',               ['App\Controllers\Admin\MenuController', 'index'],  [AdminMiddleware::class]);
    $r->get('/menuler/:id',           ['App\Controllers\Admin\MenuController', 'edit'],   [AdminMiddleware::class]);
    $r->post('/menuler/:id/kaydet',   ['App\Controllers\Admin\MenuController', 'save'],   [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/sss',                   ['App\Controllers\Admin\FaqController', 'index'],   [AdminMiddleware::class]);
    $r->get('/sss/ekle',              ['App\Controllers\Admin\FaqController', 'create'],  [AdminMiddleware::class]);
    $r->post('/sss/ekle',             ['App\Controllers\Admin\FaqController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/sss/:id/duzenle',       ['App\Controllers\Admin\FaqController', 'edit'],    [AdminMiddleware::class]);
    $r->post('/sss/:id/duzenle',      ['App\Controllers\Admin\FaqController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/sss/:id/sil',          ['App\Controllers\Admin\FaqController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/widgetlar',             ['App\Controllers\Admin\WidgetController', 'index'], [AdminMiddleware::class]);
    $r->post('/widgetlar/kaydet',     ['App\Controllers\Admin\WidgetController', 'save'],  [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/yonlendirmeler',              ['App\Controllers\Admin\RedirectController', 'index'],   [AdminMiddleware::class]);
    $r->post('/yonlendirmeler/ekle',        ['App\Controllers\Admin\RedirectController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/yonlendirmeler/:id/sil',     ['App\Controllers\Admin\RedirectController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    // ----------------------------------------
    // RAPORLAR
    // ----------------------------------------
    $r->get('/raporlar/satis',          ['App\Controllers\Admin\ReportController', 'sales'],           [AdminMiddleware::class]);
    $r->get('/raporlar/satis/csv',      ['App\Controllers\Admin\ReportController', 'exportSalesCsv'], [AdminMiddleware::class]);
    $r->get('/raporlar/urunler',        ['App\Controllers\Admin\ReportController', 'products'],       [AdminMiddleware::class]);
    $r->get('/raporlar/musteriler',     ['App\Controllers\Admin\ReportController', 'customers'],      [AdminMiddleware::class]);
    $r->get('/raporlar/stok',           ['App\Controllers\Admin\ReportController', 'stock'],          [AdminMiddleware::class]);
    $r->get('/raporlar/stok/csv',       ['App\Controllers\Admin\ReportController', 'exportStockCsv'], [AdminMiddleware::class]);
    $r->get('/raporlar/kuponlar',       ['App\Controllers\Admin\ReportController', 'coupons'],        [AdminMiddleware::class]);
    $r->get('/raporlar/terk-sepetler',  ['App\Controllers\Admin\ReportController', 'abandonedCarts'], [AdminMiddleware::class]);
    $r->get('/raporlar/indir',          ['App\Controllers\Admin\ReportController', 'download'],       [AdminMiddleware::class]);

    // ----------------------------------------
    // AYARLAR
    // ----------------------------------------
    $r->get('/ayarlar/genel',           ['App\Controllers\Admin\SettingController', 'general'],      [AdminMiddleware::class]);
    $r->post('/ayarlar/genel',          ['App\Controllers\Admin\SettingController', 'saveGeneral'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/iletisim',        ['App\Controllers\Admin\SettingController', 'contact'],      [AdminMiddleware::class]);
    $r->post('/ayarlar/iletisim',       ['App\Controllers\Admin\SettingController', 'saveContact'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/sosyal-medya',    ['App\Controllers\Admin\SettingController', 'social'],       [AdminMiddleware::class]);
    $r->post('/ayarlar/sosyal-medya',   ['App\Controllers\Admin\SettingController', 'saveSocial'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/odeme',           ['App\Controllers\Admin\SettingController', 'payment'],      [AdminMiddleware::class]);
    $r->post('/ayarlar/odeme',          ['App\Controllers\Admin\SettingController', 'savePayment'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/kargo',           ['App\Controllers\Admin\SettingController', 'shipping'],     [AdminMiddleware::class]);
    $r->post('/ayarlar/kargo',          ['App\Controllers\Admin\SettingController', 'saveShipping'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/whatsapp',        ['App\Controllers\Admin\SettingController', 'whatsapp'],     [AdminMiddleware::class]);
    $r->post('/ayarlar/whatsapp',       ['App\Controllers\Admin\SettingController', 'saveWhatsapp'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/bakim-modu',      ['App\Controllers\Admin\SettingController', 'maintenance'],  [AdminMiddleware::class]);
    $r->post('/ayarlar/bakim-modu',     ['App\Controllers\Admin\SettingController', 'saveMaintenance'],[AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/robots',          ['App\Controllers\Admin\SettingController', 'robots'],       [AdminMiddleware::class]);
    $r->post('/ayarlar/robots',         ['App\Controllers\Admin\SettingController', 'saveRobots'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/guvenlik',        ['App\Controllers\Admin\SettingController', 'security'],     [AdminMiddleware::class]);
    $r->post('/ayarlar/guvenlik',       ['App\Controllers\Admin\SettingController', 'saveSecurity'], [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->get('/ayarlar/2fa',             ['App\Controllers\Admin\SettingController', 'twoFactor'],    [AdminMiddleware::class]);
    $r->post('/ayarlar/2fa',            ['App\Controllers\Admin\SettingController', 'saveTwoFactor'],[AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/diller',              ['App\Controllers\Admin\LanguageController', 'index'],   [AdminMiddleware::class]);
    $r->post('/ayarlar/diller/ekle',        ['App\Controllers\Admin\LanguageController', 'store'],   [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/ayarlar/diller/:id/duzenle', ['App\Controllers\Admin\LanguageController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/ayarlar/diller/:id/sil',     ['App\Controllers\Admin\LanguageController', 'destroy'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/para-birimleri',          ['App\Controllers\Admin\CurrencyController', 'index'],   [AdminMiddleware::class]);
    $r->post('/ayarlar/para-birimleri/guncelle',['App\Controllers\Admin\CurrencyController', 'update'],  [AdminMiddleware::class, CsrfMiddleware::class]);
    $r->post('/ayarlar/para-birimleri/tcmb',    ['App\Controllers\Admin\CurrencyController', 'syncTCMB'],[AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/mail-sablonlari',              ['App\Controllers\Admin\MailTemplateController', 'index'],  [AdminMiddleware::class]);
    $r->get('/ayarlar/mail-sablonlari/:id/duzenle',  ['App\Controllers\Admin\MailTemplateController', 'edit'],   [AdminMiddleware::class]);
    $r->post('/ayarlar/mail-sablonlari/:id/duzenle', ['App\Controllers\Admin\MailTemplateController', 'update'], [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/google',          ['App\Controllers\Admin\SettingController', 'google'],       [AdminMiddleware::class]);
    $r->post('/ayarlar/google',         ['App\Controllers\Admin\SettingController', 'saveGoogle'],   [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/cache',           ['App\Controllers\Admin\CacheController', 'index'],   [AdminMiddleware::class]);
    $r->post('/ayarlar/cache/temizle',  ['App\Controllers\Admin\CacheController', 'flush'],   [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/loglar',          ['App\Controllers\Admin\LogController', 'index'],     [AdminMiddleware::class]);
    $r->get('/ayarlar/loglar/:file',    ['App\Controllers\Admin\LogController', 'show'],      [AdminMiddleware::class]);
    $r->post('/ayarlar/loglar/temizle', ['App\Controllers\Admin\LogController', 'flush'],     [AdminMiddleware::class, CsrfMiddleware::class]);

    $r->get('/ayarlar/aktiviteler',     ['App\Controllers\Admin\LogController', 'activity'],  [AdminMiddleware::class]);

}, []);
