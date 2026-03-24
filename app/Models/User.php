<?php

namespace App\Models;

use App\Core\Database;

class User extends BaseModel
{
    protected static string $table = 'users';

    // Email ile bul
    public static function findByEmail(string $email): ?array
    {
        return Database::row("SELECT * FROM users WHERE email = ?", [$email]);
    }

    // Token ile bul
    public static function findByToken(string $type, string $token): ?array
    {
        $col = $type === 'verify' ? 'verify_token' : 'reset_token';
        return Database::row("SELECT * FROM users WHERE {$col} = ?", [$token]);
    }

    // Kullanici olustur
    public static function register(array $data): int
    {
        return self::create([
            'name'         => $data['name'],
            'surname'      => $data['surname'],
            'email'        => $data['email'],
            'phone'        => $data['phone'] ?? null,
            'password'     => \App\Core\Auth::hashPassword($data['password']),
            'gender'       => $data['gender'] ?? null,
            'birth_date'   => $data['birth_date'] ?? null,
            'newsletter'   => $data['newsletter'] ?? 0,
            'kvkk_accepted'=> 1,
            'kvkk_date'    => date('Y-m-d H:i:s'),
            'verify_token' => generateToken(32),
            'verify_expires'=> date('Y-m-d H:i:s', strtotime('+24 hours')),
        ]);
    }

    // Email dogrula
    public static function verifyEmail(int $id): void
    {
        Database::query(
            "UPDATE users SET email_verified = 1, verify_token = NULL, verify_expires = NULL WHERE id = ?",
            [$id]
        );
    }

    // Sifre sifirla token olustur
    public static function createResetToken(int $id): string
    {
        $token = generateToken(32);
        Database::query(
            "UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?",
            [$token, date('Y-m-d H:i:s', strtotime('+1 hour')), $id]
        );
        return $token;
    }

    // Sifre guncelle
    public static function updatePassword(int $id, string $password): void
    {
        Database::query(
            "UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?",
            [\App\Core\Auth::hashPassword($password), $id]
        );
    }

    // Sosyal giris
    public static function findBySocial(string $provider, string $providerId): ?array
    {
        return Database::row(
            "SELECT u.* FROM users u
             JOIN social_logins s ON s.user_id = u.id
             WHERE s.provider = ? AND s.provider_id = ?",
            [$provider, $providerId]
        );
    }

    // Sosyal giris bagla
    public static function attachSocial(int $userId, string $provider, string $providerId): void
    {
        Database::query(
            "INSERT IGNORE INTO social_logins (user_id, provider, provider_id) VALUES (?, ?, ?)",
            [$userId, $provider, $providerId]
        );
    }

    // Kullanici listesi (admin)
    public static function list(int $page = 1, int $perPage = 20, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where  = "WHERE role = 'customer'";

        if ($search) {
            $where .= " AND (name LIKE ? OR surname LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $s = "%{$search}%";
            $params = [$s, $s, $s, $s];
        }

        $total = (int) Database::value("SELECT COUNT(*) FROM users {$where}", $params);

        $items = Database::rows(
            "SELECT id, name, surname, email, phone, email_verified, is_active, created_at
             FROM users {$where} ORDER BY id DESC LIMIT ? OFFSET ?",
            array_merge($params, [$perPage, $offset])
        );

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
        ];
    }

    // KVKK - Anonimize et
    public static function anonymize(int $id): void
    {
        Database::query(
            "UPDATE users SET
                name = 'Anonim',
                surname = 'Kullanici',
                email = CONCAT('deleted_', id, '@deleted.com'),
                phone = NULL,
                password = '',
                gender = NULL,
                birth_date = NULL,
                avatar = NULL,
                is_active = 0
             WHERE id = ?",
            [$id]
        );

        // Adresleri sil
        Database::query("DELETE FROM addresses WHERE user_id = ?", [$id]);

        // Sosyal girisleri sil
        Database::query("DELETE FROM social_logins WHERE user_id = ?", [$id]);
    }

    // Siparis sayisi
    public static function orderCount(int $userId): int
    {
        return (int) Database::value(
            "SELECT COUNT(*) FROM orders WHERE user_id = ? AND status != 'cancelled'",
            [$userId]
        );
    }

    // Toplam harcama
    public static function totalSpent(int $userId): float
    {
        return (float) Database::value(
            "SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = ? AND payment_status = 'paid'",
            [$userId]
        );
    }
}
