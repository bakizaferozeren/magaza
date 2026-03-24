<?php
/**
 * Bu dosya: C:\laragon\www\magaza\public\index.php
 * Mevcut index.php dosyanızın EN ÜSTÜNE şu define satırlarını ekleyin.
 * Eğer zaten varsa, üzerine yazmayın — sadece eksik olanları ekleyin.
 */

// ─── YOL SABİTLERİ ─────────────────────────────────────────
// Bu satırları mevcut index.php'nizin en başına ekleyin:

define('ROOT_PATH', dirname(__DIR__));          // C:\laragon\www\magaza
define('APP_PATH',  ROOT_PATH . '/app');        // C:\laragon\www\magaza\app
define('PUB_PATH',  ROOT_PATH . '/public');     // C:\laragon\www\magaza\public
define('STR_PATH',  ROOT_PATH . '/storage');    // C:\laragon\www\magaza\storage

// ─── MEVCUT index.php İÇERİĞİNİZ BUNUN ALTINDA DEVAM EDER ──
// Örnek:
// require ROOT_PATH . '/vendor/autoload.php';
// ...vs.
