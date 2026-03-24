<?php

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?PDO $instance = null;

    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host    = $_ENV['DB_HOST']    ?? 'localhost';
            $name    = $_ENV['DB_NAME']    ?? '';
            $user    = $_ENV['DB_USER']    ?? 'root';
            $pass    = $_ENV['DB_PASS']    ?? '';
            $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE utf8mb4_unicode_ci",
                ]);
            } catch (PDOException $e) {
                Logger::error('DB Baglanti Hatasi: ' . $e->getMessage());

                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    die('Veritabani Hatasi: ' . $e->getMessage());
                }

                die('Sistem hatasi olustu. Lutfen daha sonra tekrar deneyin.');
            }
        }

        return self::$instance;
    }

    // Sorgu calistir
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    // Tek satir getir
    public static function row(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    // Tum satirlari getir
    public static function rows(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    // Tek deger getir
    public static function value(string $sql, array $params = []): mixed
    {
        $result = self::query($sql, $params)->fetchColumn();
        return $result !== false ? $result : null;
    }

    // Son eklenen ID
    public static function lastId(): string
    {
        return self::connect()->lastInsertId();
    }

    // Transaction baslat
    public static function beginTransaction(): void
    {
        self::connect()->beginTransaction();
    }

    // Transaction onayla
    public static function commit(): void
    {
        self::connect()->commit();
    }

    // Transaction geri al
    public static function rollback(): void
    {
        self::connect()->rollBack();
    }

    private function __construct() {}
    private function __clone() {}
}
