<?php

namespace App\Models;

use App\Core\Database;
use App\Core\Cache;

class Currency extends BaseModel
{
    protected static string $table = 'currencies';

    // Kur getir
    public static function getRate(string $code): float
    {
        if ($code === 'TRY') return 1.0;

        $rate = Cache::remember("currency_rate_{$code}", 3600, function() use ($code) {
            return Database::value(
                "SELECT rate FROM currencies WHERE code = ? AND is_active = 1",
                [$code]
            );
        });

        return $rate ? (float) $rate : 1.0;
    }

    // Aktif para birimleri
    public static function active(string $orderBy = 'sort_order', string $direction = 'ASC'): array
    {
        return Cache::remember('currencies_active', 3600, function() {
            return Database::rows(
                "SELECT * FROM currencies WHERE is_active = 1 ORDER BY is_default DESC"
            );
        });
    }

    // TCMB XML'den kurlari guncelle
    public static function updateFromTCMB(): bool
    {
        $url = Setting::get('tcmb_xml_url', 'https://www.tcmb.gov.tr/kurlar/today.xml');

        $xml = @file_get_contents($url);
        if (!$xml) return false;

        try {
            $data = simplexml_load_string($xml);
            if (!$data) return false;

            foreach ($data->Currency as $currency) {
                $code      = (string) $currency['CurrencyCode'];
                $buyingRate = (float) str_replace(',', '.', (string) $currency->ForexBuying);
                $sellingRate= (float) str_replace(',', '.', (string) $currency->ForexSelling);

                if ($buyingRate <= 0) continue;

                // Ortalama kur
                $rate = ($buyingRate + $sellingRate) / 2;

                Database::query(
                    "UPDATE currencies SET rate = ? WHERE code = ?",
                    [round(1 / $rate, 6), $code]
                );
            }

            Setting::set('currency_last_updated', date('Y-m-d H:i:s'));
            Cache::flush();
            return true;

        } catch (\Throwable $e) {
            \App\Core\Logger::error('TCMB kur guncelleme hatasi: ' . $e->getMessage());
            return false;
        }
    }

    // Urun fiyatlarini guncelle
    public static function updateProductPrices(): void
    {
        // Bu metod daha sonra fiyat guncelleme ozelligi eklenince genisletilecek
    }
}
