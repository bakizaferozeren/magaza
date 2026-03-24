<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;

class Category extends BaseModel
{
    protected static string $table = 'categories';

    // Dil ile kategori getir
    public static function findWithLang(int $id, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT c.*, ct.name, ct.description, ct.meta_title, ct.meta_desc
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.id = ?",
            [$lang, $id]
        );
    }

    // Slug ile bul
    public static function findBySlug(string $slug, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT c.*, ct.name, ct.description, ct.meta_title, ct.meta_desc
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             WHERE c.slug = ?",
            [$lang, $slug]
        );
    }

    // Tum aktif kategoriler (agac yapisi)
    public static function tree(string $lang = 'tr'): array
    {
        return Cache::remember("categories_tree_{$lang}", 3600, function() use ($lang) {
            $rows = Database::rows(
                "SELECT c.*, ct.name
                 FROM categories c
                 LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
                 WHERE c.is_active = 1
                 ORDER BY c.sort_order ASC",
                [$lang]
            );

            return self::buildTree($rows);
        });
    }

    // Duz liste (admin icin)
    public static function flatList(string $lang = 'tr'): array
    {
        return Database::rows(
            "SELECT c.*, ct.name, p.id as parent_real_id
             FROM categories c
             LEFT JOIN category_translations ct ON ct.category_id = c.id AND ct.lang = ?
             LEFT JOIN categories p ON p.id = c.parent_id
             ORDER BY c.sort_order ASC",
            [$lang]
        );
    }

    // Agac yap
    private static function buildTree(array $rows, int $parentId = null): array
    {
        $tree = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] == $parentId) {
                $row['children'] = self::buildTree($rows, $row['id']);
                $tree[] = $row;
            }
        }
        return $tree;
    }

    // Kategori olustur
    public static function createWithTranslation(array $data, array $translations): int
    {
        Database::beginTransaction();
        try {
            $id = self::create([
                'parent_id'  => $data['parent_id'] ?? null,
                'slug'       => $data['slug'],
                'image'      => $data['image'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $data['is_active'] ?? 1,
            ]);

            foreach ($translations as $lang => $trans) {
                Database::query(
                    "INSERT INTO category_translations (category_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$id, $lang, $trans['name'], $trans['description'] ?? null, $trans['meta_title'] ?? null, $trans['meta_desc'] ?? null]
                );
            }

            Database::commit();
            Cache::delete('categories_tree_tr');
            Cache::delete('categories_tree_en');
            return $id;

        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    // Kategori guncelle
    public static function updateWithTranslation(int $id, array $data, array $translations): void
    {
        Database::beginTransaction();
        try {
            self::update($id, [
                'parent_id'  => $data['parent_id'] ?? null,
                'slug'       => $data['slug'],
                'image'      => $data['image'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $data['is_active'] ?? 1,
            ]);

            foreach ($translations as $lang => $trans) {
                Database::query(
                    "INSERT INTO category_translations (category_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     name = VALUES(name), description = VALUES(description),
                     meta_title = VALUES(meta_title), meta_desc = VALUES(meta_desc)",
                    [$id, $lang, $trans['name'], $trans['description'] ?? null, $trans['meta_title'] ?? null, $trans['meta_desc'] ?? null]
                );
            }

            Database::commit();
            Cache::delete('categories_tree_tr');
            Cache::delete('categories_tree_en');

        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    // Urun sayisi
    public static function productCount(int $id): int
    {
        return (int) Database::value(
            "SELECT COUNT(*) FROM products WHERE category_id = ? AND is_active = 1",
            [$id]
        );
    }
}
