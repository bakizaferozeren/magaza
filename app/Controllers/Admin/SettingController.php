<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Database;
use App\Core\Logger;
use App\Models\Setting;

class SettingController
{
    // Genel Ayarlar
    public function general(): void
    {
        Response::view('Admin.settings.general', [
            'title'    => 'Genel Ayarlar',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'settings' => Setting::all(),
        ]);
    }

    public function saveGeneral(): void
    {
        $data = Request::all();

        $keys = [
            'site_name', 'site_email', 'site_phone', 'site_address',
            'site_description', 'site_keywords',
            'tax_included', 'currency_default', 'lang_default',
            'shipping_free_over', 'shipping_default_cost',
            'order_min_amount', 'whatsapp_number',
            'facebook_url', 'instagram_url', 'twitter_url',
            'youtube_url', 'tiktok_url', 'linkedin_url',
            'maintenance_mode', 'maintenance_message',
            'meta_title_suffix', 'google_site_verification',
        ];

        foreach ($keys as $key) {
            if (isset($data[$key])) {
                Setting::set($key, $data[$key]);
            }
        }

        // Logo yükleme
        if (!empty($_FILES['site_logo']['name'])) {
            $logo = $this->uploadImage($_FILES['site_logo'], 'logo');
            if ($logo) Setting::set('site_logo', $logo);
        }

        // Favicon yükleme
        if (!empty($_FILES['site_favicon']['name'])) {
            $favicon = $this->uploadImage($_FILES['site_favicon'], 'favicon');
            if ($favicon) Setting::set('site_favicon', $favicon);
        }

        Logger::activity('settings_updated', 'Setting');
        Session::flash('success', 'Ayarlar kaydedildi.');
        Response::redirect(adminUrl('ayarlar/genel'));
    }

    // Google Entegrasyonları
    public function google(): void
    {
        $google = Database::row("SELECT * FROM google_integrations WHERE id = 1") ?? [];

        Response::view('Admin.settings.google', [
            'title'    => 'Google Entegrasyonları',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'google'   => $google,
            'settings' => Setting::all(),
        ]);
    }

    public function saveGoogle(): void
    {
        $data = Request::all();

        Database::query(
            "INSERT INTO google_integrations
                (id, ga4_measurement_id, search_console_code, merchant_feed_url)
             VALUES (1, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                ga4_measurement_id   = VALUES(ga4_measurement_id),
                search_console_code  = VALUES(search_console_code),
                merchant_feed_url    = VALUES(merchant_feed_url)",
            [
                $data['ga4_measurement_id'] ?? null,
                $data['search_console_code'] ?? null,
                $data['merchant_feed_url'] ?? null,
            ]
        );

        // Ayrıca settings tablosuna da yaz
        $settingKeys = ['gtm_id', 'pixel_id', 'tiktok_pixel', 'clarity_id'];
        foreach ($settingKeys as $key) {
            if (isset($data[$key])) Setting::set($key, $data[$key]);
        }

        Logger::activity('google_settings_updated', 'Setting');
        Session::flash('success', 'Google entegrasyonları kaydedildi.');
        Response::redirect(adminUrl('ayarlar/google'));
    }

    // Güvenlik Ayarları
    public function security(): void
    {
        Response::view('Admin.settings.security', [
            'title'    => 'Güvenlik Ayarları',
            'siteName' => Setting::get('site_name'),
            'siteLogo' => Setting::get('site_logo'),
            'user'     => Auth::user(),
            'settings' => Setting::all(),
            'currentUser' => Auth::user(),
        ]);
    }

    public function saveSecurity(): void
    {
        $data   = Request::all();
        $action = $data['_action'] ?? 'password';

        // Profil güncelleme
        if ($action === 'profile') {
            $user = Auth::user();

            Database::query(
                "UPDATE users SET name = ?, surname = ?, email = ?, phone = ? WHERE id = ?",
                [
                    $data['name']    ?? $user['name'],
                    $data['surname'] ?? $user['surname'],
                    $data['email']   ?? $user['email'],
                    $data['phone']   ?? '',
                    $user['id'],
                ]
            );

            // Avatar
            if (!empty($_FILES['avatar']['name'])) {
                $avatar = $this->uploadImage($_FILES['avatar'], 'avatars');
                if ($avatar) {
                    Database::query("UPDATE users SET avatar = ? WHERE id = ?", [$avatar, $user['id']]);
                }
            }

            Logger::activity('profile_updated', 'User', $user['id']);
            Session::flash('success', 'Profil güncellendi.');
            Response::redirect(adminUrl('ayarlar/guvenlik'));
        }

        // Şifre değiştirme
        if (!empty($data['new_password'])) {
            if ($data['new_password'] !== ($data['new_password_confirm'] ?? '')) {
                Session::flash('error', 'Yeni şifreler eşleşmiyor.');
                Response::redirect(adminUrl('ayarlar/guvenlik'));
            }

            $user = Auth::user();
            if (!password_verify($data['current_password'] ?? '', $user['password'])) {
                Session::flash('error', 'Mevcut şifre yanlış.');
                Response::redirect(adminUrl('ayarlar/guvenlik'));
            }

            if (strlen($data['new_password']) < 8) {
                Session::flash('error', 'Şifre en az 8 karakter olmalıdır.');
                Response::redirect(adminUrl('ayarlar/guvenlik'));
            }

            Database::query(
                "UPDATE users SET password = ? WHERE id = ?",
                [password_hash($data['new_password'], PASSWORD_DEFAULT), $user['id']]
            );

            Logger::activity('password_changed', 'User', $user['id']);
            Session::flash('success', 'Şifre başarıyla değiştirildi.');
            Response::redirect(adminUrl('ayarlar/guvenlik'));
        }

        Session::flash('success', 'Ayarlar kaydedildi.');
        Response::redirect(adminUrl('ayarlar/guvenlik'));
    }

    private function uploadImage(array $file, string $folder): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp','svg','ico'];
        if (!in_array($ext, $allowed)) return null;

        $dir = PUB_PATH . '/uploads/' . $folder . '/';
        if (!is_dir($dir)) mkdir($dir, 0755, true);

        $filename = uniqid($folder . '_') . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            return $folder . '/' . $filename;
        }
        return null;
    }
}
