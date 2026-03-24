<?php

use App\Models\Setting;

// ============================================
// SEO META ETIKETLERI
// ============================================

function seoMeta(array $data = []): string
{
    $siteName    = setting('site_name', 'Magazam');
    $title       = isset($data['title']) ? e($data['title']) . ' | ' . $siteName : $siteName;
    $description = isset($data['description']) ? e($data['description']) : '';
    $keywords    = isset($data['keywords']) ? e($data['keywords']) : '';
    $image       = $data['image'] ?? asset('images/og-default.jpg');
    $url         = $data['url'] ?? currentUrl();
    $type        = $data['type'] ?? 'website';
    $noindex     = $data['noindex'] ?? false;
    $canonical   = $data['canonical'] ?? $url;
    $lang        = session('lang', setting('lang_default', 'tr'));

    $html = '';

    // Temel meta
    $html .= '<title>' . $title . '</title>' . PHP_EOL;
    $html .= '<meta name="description" content="' . $description . '">' . PHP_EOL;

    if ($keywords) {
        $html .= '<meta name="keywords" content="' . $keywords . '">' . PHP_EOL;
    }

    // Robots
    $html .= '<meta name="robots" content="' . ($noindex ? 'noindex,nofollow' : 'index,follow') . '">' . PHP_EOL;

    // Canonical
    $html .= '<link rel="canonical" href="' . e($canonical) . '">' . PHP_EOL;

    // Open Graph
    $html .= '<meta property="og:type" content="' . $type . '">' . PHP_EOL;
    $html .= '<meta property="og:title" content="' . $title . '">' . PHP_EOL;
    $html .= '<meta property="og:description" content="' . $description . '">' . PHP_EOL;
    $html .= '<meta property="og:image" content="' . e($image) . '">' . PHP_EOL;
    $html .= '<meta property="og:url" content="' . e($url) . '">' . PHP_EOL;
    $html .= '<meta property="og:site_name" content="' . e($siteName) . '">' . PHP_EOL;
    $html .= '<meta property="og:locale" content="' . ($lang === 'tr' ? 'tr_TR' : 'en_US') . '">' . PHP_EOL;

    // Twitter Card
    $html .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
    $html .= '<meta name="twitter:title" content="' . $title . '">' . PHP_EOL;
    $html .= '<meta name="twitter:description" content="' . $description . '">' . PHP_EOL;
    $html .= '<meta name="twitter:image" content="' . e($image) . '">' . PHP_EOL;

    // Hreflang (coklu dil)
    $html .= hreflangTags($url);

    return $html;
}

function hreflangTags(string $currentUrl): string
{
    $html = '';
    $base = rtrim($_ENV['APP_URL'] ?? '', '/');

    // TR
    $html .= '<link rel="alternate" hreflang="tr" href="' . e($base . '/') . '">' . PHP_EOL;
    // EN
    $html .= '<link rel="alternate" hreflang="en" href="' . e($base . '/en/') . '">' . PHP_EOL;
    // x-default
    $html .= '<link rel="alternate" hreflang="x-default" href="' . e($base . '/') . '">' . PHP_EOL;

    return $html;
}

// ============================================
// BREADCRUMB
// ============================================

function breadcrumb(array $items): string
{
    $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';

    foreach ($items as $i => $item) {
        $isLast = $i === array_key_last($items);

        if ($isLast) {
            $html .= '<li class="breadcrumb-item active" aria-current="page">' . e($item['label']) . '</li>';
        } else {
            $html .= '<li class="breadcrumb-item"><a href="' . e($item['url']) . '">' . e($item['label']) . '</a></li>';
        }
    }

    $html .= '</ol></nav>';
    return $html;
}

// ============================================
// SEO GUCUNU HESAPLA (Yoast tarzı)
// ============================================

function seoScore(array $data): array
{
    $score    = 0;
    $warnings = [];
    $goods    = [];

    $title       = $data['meta_title'] ?? $data['name'] ?? '';
    $description = $data['meta_desc'] ?? '';
    $content     = strip_tags($data['long_desc'] ?? $data['content'] ?? '');
    $keyword     = $data['meta_keywords'] ?? '';

    // Baslik kontrolu
    if (empty($title)) {
        $warnings[] = 'Meta baslik eksik';
    } elseif (mb_strlen($title) < 30) {
        $warnings[] = 'Meta baslik cok kisa (min 30 karakter)';
    } elseif (mb_strlen($title) > 60) {
        $warnings[] = 'Meta baslik cok uzun (max 60 karakter)';
    } else {
        $score += 20;
        $goods[] = 'Meta baslik uygun uzunlukta';
    }

    // Aciklama kontrolu
    if (empty($description)) {
        $warnings[] = 'Meta aciklama eksik';
    } elseif (mb_strlen($description) < 120) {
        $warnings[] = 'Meta aciklama cok kisa (min 120 karakter)';
    } elseif (mb_strlen($description) > 160) {
        $warnings[] = 'Meta aciklama cok uzun (max 160 karakter)';
    } else {
        $score += 20;
        $goods[] = 'Meta aciklama uygun uzunlukta';
    }

    // Icerik kontrolu
    $wordCount = str_word_count($content);
    if ($wordCount < 50) {
        $warnings[] = 'Icerik cok kisa (min 50 kelime)';
    } elseif ($wordCount < 300) {
        $warnings[] = 'Icerik yetersiz (300+ kelime onerilen)';
        $score += 10;
    } else {
        $score += 20;
        $goods[] = 'Icerik yeterli uzunlukta (' . $wordCount . ' kelime)';
    }

    // Anahtar kelime kontrolu
    if (empty($keyword)) {
        $warnings[] = 'Anahtar kelime girilmemis';
    } else {
        $score += 10;
        $goods[] = 'Anahtar kelime mevcut';

        // Anahtar kelime baslikta geciyorsa
        if (stripos($title, $keyword) !== false) {
            $score += 15;
            $goods[] = 'Anahtar kelime baslikta geciyor';
        }

        // Anahtar kelime aciklamada geciyorsa
        if (stripos($description, $keyword) !== false) {
            $score += 15;
            $goods[] = 'Anahtar kelime aciklamada geciyor';
        }
    }

    // Durum belirleme
    $status = match(true) {
        $score >= 80 => 'good',
        $score >= 50 => 'ok',
        default      => 'poor',
    };

    return [
        'score'    => $score,
        'status'   => $status,
        'warnings' => $warnings,
        'goods'    => $goods,
    ];
}

// ============================================
// SITEMAP
// ============================================

function generateSitemapUrl(string $loc, string $lastmod = '', string $changefreq = 'weekly', float $priority = 0.8): string
{
    $xml  = '<url>' . PHP_EOL;
    $xml .= '  <loc>' . e($loc) . '</loc>' . PHP_EOL;

    if ($lastmod) {
        $xml .= '  <lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>' . PHP_EOL;
    }

    $xml .= '  <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
    $xml .= '  <priority>' . $priority . '</priority>' . PHP_EOL;
    $xml .= '</url>' . PHP_EOL;

    return $xml;
}

// ============================================
// YARDIMCI
// ============================================

function currentUrl(): string
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}
