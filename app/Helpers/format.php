<?php

use App\Core\Session;
use App\Models\Setting;
use App\Models\Currency;

// ============================================
// PARA FORMATLAMA
// ============================================

function formatPrice(float $price, bool $showSymbol = true): string
{
    $currencyCode = Session::get('currency', Setting::get('currency_default', 'TRY'));
    $rate         = Currency::getRate($currencyCode);
    $converted    = $price * $rate;

    $symbols = ['TRY' => '₺', 'USD' => '$', 'EUR' => '€'];
    $symbol  = $symbols[$currencyCode] ?? '₺';

    $formatted = number_format($converted, 2, ',', '.');

    if (!$showSymbol) return $formatted;

    return match($currencyCode) {
        'USD', 'EUR' => $symbol . $formatted,
        default      => $formatted . ' ' . $symbol,
    };
}

function formatPriceTRY(float $price): string
{
    return number_format($price, 2, ',', '.') . ' ₺';
}

function calculateTax(float $price, int $taxRate): float
{
    // KDV dahil fiyattan KDV tutarini hesapla
    return round($price - ($price / (1 + $taxRate / 100)), 2);
}

function priceWithoutTax(float $price, int $taxRate): float
{
    return round($price / (1 + $taxRate / 100), 2);
}

function discountPercent(float $originalPrice, float $salePrice): int
{
    if ($originalPrice <= 0) return 0;
    return (int) round((($originalPrice - $salePrice) / $originalPrice) * 100);
}

// ============================================
// STOK DURUMU
// ============================================

function stockStatusLabel(string $status): string
{
    return match($status) {
        'in_stock'     => 'Stokta Var',
        'out_of_stock' => 'Stokta Yok',
        'pre_order'    => 'On Siparis',
        'coming_soon'  => 'Yakinda Gelecek',
        'backorder'    => 'Siparis Uzerine',
        default        => 'Bilinmiyor',
    };
}

function stockStatusColor(string $status): string
{
    return match($status) {
        'in_stock'     => 'success',
        'out_of_stock' => 'danger',
        'pre_order'    => 'info',
        'coming_soon'  => 'warning',
        'backorder'    => 'warning',
        default        => 'secondary',
    };
}

// ============================================
// SIPARIS DURUMU
// ============================================

function orderStatusLabel(string $status): string
{
    return match($status) {
        'pending'    => 'Beklemede',
        'confirmed'  => 'Onaylandi',
        'processing' => 'Hazirlaniyor',
        'shipped'    => 'Kargoya Verildi',
        'delivered'  => 'Teslim Edildi',
        'cancelled'  => 'Iptal Edildi',
        'refunded'   => 'Iade Edildi',
        default      => 'Bilinmiyor',
    };
}

function orderStatusColor(string $status): string
{
    return match($status) {
        'pending'    => 'warning',
        'confirmed'  => 'info',
        'processing' => 'primary',
        'shipped'    => 'info',
        'delivered'  => 'success',
        'cancelled'  => 'danger',
        'refunded'   => 'secondary',
        default      => 'secondary',
    };
}

function orderStatusStep(string $status): int
{
    return match($status) {
        'pending'    => 1,
        'confirmed'  => 1,
        'processing' => 2,
        'shipped'    => 3,
        'delivered'  => 4,
        default      => 0,
    };
}

// ============================================
// FATURA TIPI
// ============================================

function invoiceTypeLabel(string $type): string
{
    return match($type) {
        'proforma'   => 'Proforma Fatura',
        'e_invoice'  => 'E-Fatura',
        'e_archive'  => 'E-Arsiv Fatura',
        'return'     => 'Iade Faturasi',
        'cancel'     => 'Iptal Faturasi',
        default      => 'Fatura',
    };
}

// ============================================
// DOSYA & GORSEL
// ============================================

function formatFileSize(int $bytes): string
{
    if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
    if ($bytes >= 1048576)    return number_format($bytes / 1048576, 2) . ' MB';
    if ($bytes >= 1024)       return number_format($bytes / 1024, 2) . ' KB';
    return $bytes . ' B';
}

function productImage(string $image = '', string $size = 'medium'): string
{
    if (empty($image)) {
        return asset('images/no-image.png');
    }
    return uploadUrl('products/' . $image);
}

function categoryImage(string $image = ''): string
{
    if (empty($image)) return asset('images/no-category.png');
    return uploadUrl('categories/' . $image);
}

function brandLogo(string $logo = ''): string
{
    if (empty($logo)) return asset('images/no-brand.png');
    return uploadUrl('brands/' . $logo);
}

// ============================================
// METIN
// ============================================

function truncate(string $text, int $length = 100, string $suffix = '...'): string
{
    $text = strip_tags($text);
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . $suffix;
}

function slugify(string $text): string
{
    $tr = ['ş'=>'s','Ş'=>'S','ı'=>'i','İ'=>'I','ğ'=>'g','Ğ'=>'G','ü'=>'u','Ü'=>'U','ö'=>'o','Ö'=>'O','ç'=>'c','Ç'=>'C'];
    $text = strtr($text, $tr);
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9\-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

function excerpt(string $html, int $length = 160): string
{
    return truncate(strip_tags($html), $length);
}

// ============================================
// TELEFON
// ============================================

function formatPhone(string $phone): string
{
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10) {
        return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 2) . ' ' . substr($phone, 8, 2);
    }
    return $phone;
}
