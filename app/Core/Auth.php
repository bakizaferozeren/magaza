<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    // Giris yap
    public static function login(array $user): void
    {
        session_regenerate_id(true);

        Session::set('user_id',   $user['id']);
        Session::set('user_name', $user['name'] . ' ' . $user['surname']);
        Session::set('user_role', $user['role']);
        Session::set('user_email',$user['email']);

        // Son giris bilgilerini guncelle
        Database::query(
            "UPDATE users SET last_login = NOW(), last_ip = ?, login_attempts = 0 WHERE id = ?",
            [$_SERVER['REMOTE_ADDR'] ?? '', $user['id']]
        );
    }

    // Cikis yap
    public static function logout(): void
    {
        Session::remove('user_id');
        Session::remove('user_name');
        Session::remove('user_role');
        Session::remove('user_email');
        session_regenerate_id(true);
    }

    // Giris yapilmis mi?
    public static function check(): bool
    {
        return Session::has('user_id');
    }

    // Misafir mi?
    public static function guest(): bool
    {
        return !self::check();
    }

    // Admin mi?
    public static function isAdmin(): bool
    {
        return Session::get('user_role') === 'admin';
    }

    // Giris yapan kullanici
    public static function user(): ?array
    {
        if (!self::check()) return null;
        return User::find(Session::get('user_id'));
    }

    // Kullanici ID
    public static function id(): ?int
    {
        return Session::get('user_id');
    }

    // Sifre dogrula
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    // Sifre hashle
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    // Brute force - deneme sayisini artir
    public static function incrementAttempts(string $email): void
    {
        $maxAttempts = (int) ($_ENV['MAX_LOGIN_ATTEMPTS'] ?? 3);
        $lockMinutes = (int) ($_ENV['LOCKOUT_MINUTES'] ?? 15);

        $user = Database::row("SELECT id, login_attempts FROM users WHERE email = ?", [$email]);
        if (!$user) return;

        $attempts = $user['login_attempts'] + 1;

        if ($attempts >= $maxAttempts) {
            Database::query(
                "UPDATE users SET login_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL ? MINUTE) WHERE id = ?",
                [$attempts, $lockMinutes, $user['id']]
            );
        } else {
            Database::query(
                "UPDATE users SET login_attempts = ? WHERE id = ?",
                [$attempts, $user['id']]
            );
        }
    }

    // Hesap kilitli mi?
    public static function isLocked(array $user): bool
    {
        if (empty($user['locked_until'])) return false;
        return strtotime($user['locked_until']) > time();
    }

    // Kilit suresi kaldi mi?
    public static function lockRemainingMinutes(array $user): int
    {
        if (empty($user['locked_until'])) return 0;
        $remaining = strtotime($user['locked_until']) - time();
        return max(0, (int) ceil($remaining / 60));
    }
}
