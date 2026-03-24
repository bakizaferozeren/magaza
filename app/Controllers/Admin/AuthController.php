<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Logger;
use App\Core\Validator;
use App\Core\Database;
use App\Models\User;

class AuthController
{
    // Giris formu
    public function loginForm(): void
    {
        if (Auth::check() && Auth::isAdmin()) {
            Response::redirect(adminUrl());
        }

        $data = [
            'title'     => 'Admin Girisi',
            'siteName'  => setting('site_name', 'Magazam'),
            'siteLogo'  => setting('site_logo'),
        ];

        Response::view('Admin.auth.login', $data);
    }

    // Giris islemi
    public function login(): void
    {
        $email    = Request::post('email');
        $password = Request::post('password');
        $remember = Request::post('remember');

        // Validasyon
        $validator = Validator::make(
            ['email' => $email, 'password' => $password],
            ['email' => 'required|email', 'password' => 'required']
        );

        if ($validator->fails()) {
            Session::flash('error', 'E-posta ve sifre giriniz.');
            Session::flash('old_email', $email);
            Response::back();
        }

        // IP bazli brute force kontrolu
        $suspiciousIp = Database::row(
            "SELECT * FROM suspicious_ips WHERE ip = ? AND (blocked_until > NOW() OR is_permanent = 1)",
            [Request::ip()]
        );

        if ($suspiciousIp) {
            Logger::security('blocked_ip_login_attempt');
            Session::flash('error', 'Cok fazla basarisiz giris denemesi. Lutfen daha sonra tekrar deneyin.');
            Response::back();
        }

        // Kullanici bul
        $user = User::findByEmail($email);

        if (!$user) {
            $this->handleFailedLogin($email);
            Session::flash('error', 'E-posta veya sifre hatali.');
            Session::flash('old_email', $email);
            Response::back();
        }

        // Admin mi?
        if ($user['role'] !== 'admin') {
            Logger::security('non_admin_login_attempt', $user['id']);
            Session::flash('error', 'Bu alana erisim yetkiniz yok.');
            Response::back();
        }

        // Hesap kilitli mi?
        if (Auth::isLocked($user)) {
            $remaining = Auth::lockRemainingMinutes($user);
            Session::flash('error', "Hesabiniz kilitlendi. {$remaining} dakika sonra tekrar deneyin.");
            Response::back();
        }

        // Sifre kontrol
        if (!Auth::verifyPassword($password, $user['password'])) {
            Auth::incrementAttempts($email);
            $this->handleFailedLogin($email);
            Session::flash('error', 'E-posta veya sifre hatali.');
            Session::flash('old_email', $email);
            Response::back();
        }

        // 2FA aktif mi?
        if ($user['two_factor_enabled']) {
            Session::set('2fa_user_id', $user['id']);
            Response::redirect(adminUrl('2fa'));
        }

        // Farkli cihaz/konum kontrolu
        $this->checkNewDevice($user);

        // Giris yap
        Auth::login($user);
        Logger::security('admin_login_success', $user['id']);

        Session::flash('success', 'Hosgeldiniz, ' . $user['name'] . '!');
        Response::redirect(adminUrl());
    }

    // 2FA formu
    public function twoFactorForm(): void
    {
        if (!Session::has('2fa_user_id')) {
            Response::redirect(adminUrl('giris'));
        }

        Response::view('Admin.auth.two_factor', [
            'title' => 'Iki Adimli Dogrulama',
        ]);
    }

    // 2FA dogrulama
    public function twoFactor(): void
    {
        $userId = Session::get('2fa_user_id');
        if (!$userId) {
            Response::redirect(adminUrl('giris'));
        }

        $code = Request::post('code');
        $user = User::find($userId);

        if (!$user) {
            Response::redirect(adminUrl('giris'));
        }

        // TOTP dogrula
        if (!$this->verifyTOTP($user['two_factor_secret'], $code)) {
            Session::flash('error', 'Gecersiz dogrulama kodu. Tekrar deneyin.');
            Response::back();
        }

        Session::remove('2fa_user_id');

        $this->checkNewDevice($user);
        Auth::login($user);
        Logger::security('admin_2fa_success', $user['id']);

        Session::flash('success', 'Hosgeldiniz, ' . $user['name'] . '!');
        Response::redirect(adminUrl());
    }

    // Cikis
    public function logout(): void
    {
        $userId = Auth::id();
        Auth::logout();
        Logger::security('admin_logout', $userId);
        Session::flash('success', 'Basariyla cikis yapildi.');
        Response::redirect(adminUrl('giris'));
    }

    // Basarisiz giris - IP takibi
    private function handleFailedLogin(string $email): void
    {
        $ip = Request::ip();

        $existing = Database::row(
            "SELECT * FROM suspicious_ips WHERE ip = ?",
            [$ip]
        );

        if ($existing) {
            $count = $existing['attempt_count'] + 1;
            $blockedUntil = $count >= 10
                ? date('Y-m-d H:i:s', strtotime('+1 hour'))
                : null;

            Database::query(
                "UPDATE suspicious_ips SET attempt_count = ?, blocked_until = ?, updated_at = NOW() WHERE ip = ?",
                [$count, $blockedUntil, $ip]
            );
        } else {
            Database::query(
                "INSERT INTO suspicious_ips (ip, reason, attempt_count) VALUES (?, ?, 1)",
                [$ip, 'Failed login attempt: ' . $email]
            );
        }

        Logger::security('failed_login_attempt: ' . $email);
    }

    // Yeni cihaz/konum kontrolu
    private function checkNewDevice(array $user): void
    {
        $currentIp = Request::ip();
        $lastIp    = $user['last_ip'] ?? '';

        if ($lastIp && $lastIp !== $currentIp) {
            Logger::security('new_device_login', $user['id']);
            // Mail gonder - ilerleyen adimda Mail sinifi yazildiginda aktif edilecek
        }
    }

    // TOTP dogrulama (Google Authenticator)
    private function verifyTOTP(string $secret, string $code): bool
    {
        // Base32 decode
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $binaryString = '';

        for ($i = 0; $i < strlen($secret); $i++) {
            $val = strpos($base32chars, $secret[$i]);
            $binaryString .= sprintf('%05b', $val);
        }

        $secretBytes = '';
        for ($i = 0; $i + 8 <= strlen($binaryString); $i += 8) {
            $secretBytes .= chr(bindec(substr($binaryString, $i, 8)));
        }

        $timeSlice = floor(time() / 30);

        // 1 onceki ve sonraki window da gecerli
        for ($i = -1; $i <= 1; $i++) {
            $time = pack('N*', 0) . pack('N*', $timeSlice + $i);
            $hm   = hash_hmac('sha1', $time, $secretBytes, true);
            $offset = ord($hm[19]) & 0xf;
            $hashPart = substr($hm, $offset, 4);
            $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
            $totp  = str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);

            if (hash_equals($totp, $code)) {
                return true;
            }
        }

        return false;
    }
}
