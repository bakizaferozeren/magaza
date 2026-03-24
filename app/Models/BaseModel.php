<?php

namespace App\Models;

use App\Core\Database;

abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    // Tek kayit getir
    public static function find(int $id): ?array
    {
        return Database::row(
            "SELECT * FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?",
            [$id]
        );
    }

    // Kosulla tek kayit getir
    public static function findBy(string $column, mixed $value): ?array
    {
        return Database::row(
            "SELECT * FROM `" . static::$table . "` WHERE `{$column}` = ? LIMIT 1",
            [$value]
        );
    }

    // Tum kayitlari getir
    public static function all(string $orderBy = 'id', string $direction = 'ASC'): array
    {
        return Database::rows(
            "SELECT * FROM `" . static::$table . "` ORDER BY `{$orderBy}` {$direction}"
        );
    }

    // Aktif kayitlari getir
    public static function active(string $orderBy = 'sort_order', string $direction = 'ASC'): array
    {
        return Database::rows(
            "SELECT * FROM `" . static::$table . "` WHERE is_active = 1 ORDER BY `{$orderBy}` {$direction}"
        );
    }

    // Kayit olustur
    public static function create(array $data): int
    {
        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        Database::query(
            "INSERT INTO `" . static::$table . "` (`{$columns}`) VALUES ({$placeholders})",
            array_values($data)
        );

        return (int) Database::lastId();
    }

    // Kayit guncelle
    public static function update(int $id, array $data): bool
    {
        $sets = implode(' = ?, ', array_keys($data)) . ' = ?';
        $values = array_values($data);
        $values[] = $id;

        $stmt = Database::query(
            "UPDATE `" . static::$table . "` SET {$sets} WHERE `" . static::$primaryKey . "` = ?",
            $values
        );

        return $stmt->rowCount() > 0;
    }

    // Kayit sil
    public static function delete(int $id): bool
    {
        $stmt = Database::query(
            "DELETE FROM `" . static::$table . "` WHERE `" . static::$primaryKey . "` = ?",
            [$id]
        );

        return $stmt->rowCount() > 0;
    }

    // Kayit var mi?
    public static function exists(string $column, mixed $value, int $exceptId = 0): bool
    {
        $sql = "SELECT COUNT(*) FROM `" . static::$table . "` WHERE `{$column}` = ?";
        $params = [$value];

        if ($exceptId > 0) {
            $sql .= " AND `" . static::$primaryKey . "` != ?";
            $params[] = $exceptId;
        }

        return (int) Database::value($sql, $params) > 0;
    }

    // Sayim
    public static function count(array $where = []): int
    {
        $sql = "SELECT COUNT(*) FROM `" . static::$table . "`";
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "`{$col}` = ?";
                $params[] = $val;
            }
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        return (int) Database::value($sql, $params);
    }

    // Sayfalama
    public static function paginate(int $page = 1, int $perPage = 20, array $where = [], string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $whereClause = '';

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $col => $val) {
                $conditions[] = "`{$col}` = ?";
                $params[] = $val;
            }
            $whereClause = " WHERE " . implode(' AND ', $conditions);
        }

        $total = (int) Database::value(
            "SELECT COUNT(*) FROM `" . static::$table . "`" . $whereClause,
            $params
        );

        $paginatedParams = array_merge($params, [$perPage, $offset]);
        $items = Database::rows(
            "SELECT * FROM `" . static::$table . "`{$whereClause} ORDER BY `{$orderBy}` {$direction} LIMIT ? OFFSET ?",
            $paginatedParams
        );

        return [
            'items'        => $items,
            'total'        => $total,
            'per_page'     => $perPage,
            'current_page' => $page,
            'last_page'    => (int) ceil($total / $perPage),
            'from'         => $offset + 1,
            'to'           => min($offset + $perPage, $total),
        ];
    }
}
