# E-Ticaret CMS

## Kurulum

1. .env dosyasini ac ve veritabani bilgilerini doldur
2. Laragon terminalinde: composer install
3. database/schema.sql dosyasini veritabanina import et
4. http://localhost/magaza/public adresine git

## Admin Paneli

.env dosyasindaki APP_ADMIN_PATH degerini degistirerek gizli admin URL belirle.
Varsayilan: http://localhost/magaza/public/yonetim

## Gereksinimler

- PHP 8.3+
- MariaDB 10.6+
- Apache mod_rewrite
- Composer
