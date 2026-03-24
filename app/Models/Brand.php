<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;

class Brand extends BaseModel
{
    protected static string $table = 'brands';

    // Dil ile getir
    public static function findWithLang(int $id, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT b.*, bt.name, bt.description, bt.meta_title, bt.meta_desc
             FROM brands b
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             WHERE b.id = ?",
            [$lang, $id]
        );
    }

    // Slug ile bul
    public static function findBySlug(string $slug, string $lang = 'tr'): ?array
    {
        return Database::row(
            "SELECT b.*, bt.name, bt.description
             FROM brands b
             LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
             WHERE b.slug = ?",
            [$lang, $slug]
        );
    }

    // Aktif markalar
    public static function activeList(string $lang = 'tr'): array
    {
        return Cache::remember("brands_active_{$lang}", 3600, function() use ($lang) {
            return Database::rows(
                "SELECT b.*, bt.name
                 FROM brands b
                 LEFT JOIN brand_translations bt ON bt.brand_id = b.id AND bt.lang = ?
                 WHERE b.is_active = 1
                 ORDER BY bt.name ASC",
                [$lang]
            );
        });
    }

    // Marka olustur
    public static function createWithTranslation(array $data, array $translations): int
    {
        Database::beginTransaction();
        try {
            $id = self::create([
                'slug'       => $data['slug'],
                'logo'       => $data['logo'] ?? null,
                'website'    => $data['website'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $data['is_active'] ?? 1,
            ]);

            foreach ($translations as $lang => $trans) {
                Database::query(
                    "INSERT INTO brand_translations (brand_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)",
                    [$id, $lang, $trans['name'], $trans['description'] ?? null, $trans['meta_title'] ?? null, $trans['meta_desc'] ?? null]
                );
            }

            Database::commit();
            Cache::delete('brands_active_tr');
            Cache::delete('brands_active_en');
            return $id;

        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    // Marka guncelle
    public static function updateWithTranslation(int $id, array $data, array $translations): void
    {
        Database::beginTransaction();
        try {
            self::update($id, [
                'slug'       => $data['slug'],
                'logo'       => $data['logo'] ?? null,
                'website'    => $data['website'] ?? null,
                'sort_order' => $data['sort_order'] ?? 0,
                'is_active'  => $data['is_active'] ?? 1,
            ]);

            foreach ($translations as $lang => $trans) {
                Database::query(
                    "INSERT INTO brand_translations (brand_id, lang, name, description, meta_title, meta_desc)
                     VALUES (?, ?, ?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE
                     name = VALUES(name), description = VALUES(description),
                     meta_title = VALUES(meta_title), meta_desc = VALUES(meta_desc)",
                    [$id, $lang, $trans['name'], $trans['description'] ?? null, $trans['meta_title'] ?? null, $trans['meta_desc'] ?? null]
                );
            }

            Database::commit();
            Cache::delete('brands_active_tr');
            Cache::delete('brands_active_en');

        } catch (\Throwable $e) {
            Database::rollback();
            throw $e;
        }
    }

    // Urun sayisi
    public static function productCount(int $id): int
    {
        return (int) Database::value(
            "SELECT COUNT(*) FROM products WHERE brand_id = ? AND is_active = 1",
            [$id]
        );
    }
}
