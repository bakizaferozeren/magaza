-- ============================================
-- E-Ticaret CMS - Veritabani Semasi
-- MariaDB 10.6+ / MySQL 8+
-- Karakter Seti: utf8mb4_unicode_ci
-- ============================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- ============================================
-- KULLANICILAR
-- ============================================

CREATE TABLE `users` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`            VARCHAR(100) NOT NULL,
  `surname`         VARCHAR(100) NOT NULL,
  `email`           VARCHAR(150) NOT NULL UNIQUE,
  `phone`           VARCHAR(20),
  `password`        VARCHAR(255),
  `role`            ENUM('admin','customer') NOT NULL DEFAULT 'customer',
  `gender`          ENUM('male','female','other') DEFAULT NULL,
  `birth_date`      DATE DEFAULT NULL,
  `avatar`          VARCHAR(255) DEFAULT NULL,
  `email_verified`  TINYINT(1) DEFAULT 0,
  `verify_token`    VARCHAR(100) DEFAULT NULL,
  `verify_expires`  DATETIME DEFAULT NULL,
  `reset_token`     VARCHAR(100) DEFAULT NULL,
  `reset_expires`   DATETIME DEFAULT NULL,
  `two_factor_secret` VARCHAR(255) DEFAULT NULL,
  `two_factor_enabled` TINYINT(1) DEFAULT 0,
  `login_attempts`  INT DEFAULT 0,
  `locked_until`    DATETIME DEFAULT NULL,
  `newsletter`      TINYINT(1) DEFAULT 0,
  `kvkk_accepted`   TINYINT(1) DEFAULT 0,
  `kvkk_date`       DATETIME DEFAULT NULL,
  `last_login`      DATETIME DEFAULT NULL,
  `last_ip`         VARCHAR(45) DEFAULT NULL,
  `is_active`       TINYINT(1) DEFAULT 1,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SOSYAL GIRIS
-- ============================================

CREATE TABLE `social_logins` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED NOT NULL,
  `provider`    ENUM('google','facebook','apple','yandex') NOT NULL,
  `provider_id` VARCHAR(255) NOT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_social` (`provider`, `provider_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- ADRESLER
-- ============================================

CREATE TABLE `addresses` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `title`      VARCHAR(80),
  `full_name`  VARCHAR(150) NOT NULL,
  `phone`      VARCHAR(20),
  `city`       VARCHAR(80) NOT NULL,
  `district`   VARCHAR(80),
  `neighborhood` VARCHAR(100),
  `address`    TEXT NOT NULL,
  `zip`        VARCHAR(10),
  `is_default` TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MESAJLAR
-- ============================================

CREATE TABLE `messages` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED NOT NULL,
  `thread_id`   INT UNSIGNED DEFAULT NULL,
  `type`        ENUM('order','question','system','support') DEFAULT 'system',
  `subject`     VARCHAR(255),
  `body`        TEXT NOT NULL,
  `sender`      ENUM('user','admin') DEFAULT 'admin',
  `is_read`     TINYINT(1) DEFAULT 0,
  `related_id`  INT UNSIGNED DEFAULT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- DILLER
-- ============================================

CREATE TABLE `languages` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`      VARCHAR(5) NOT NULL UNIQUE,
  `name`      VARCHAR(80) NOT NULL,
  `flag`      VARCHAR(10),
  `is_default` TINYINT(1) DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `languages` (`code`, `name`, `flag`, `is_default`, `is_active`, `sort_order`) VALUES
('tr', 'Turkce', 'TR', 1, 1, 1),
('en', 'English', 'EN', 0, 1, 2);

-- ============================================
-- PARA BIRIMLERI
-- ============================================

CREATE TABLE `currencies` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`       VARCHAR(5) NOT NULL UNIQUE,
  `name`       VARCHAR(80) NOT NULL,
  `symbol`     VARCHAR(10) NOT NULL,
  `rate`       DECIMAL(10,4) DEFAULT 1.0000,
  `is_default` TINYINT(1) DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `currencies` (`code`, `name`, `symbol`, `rate`, `is_default`, `is_active`) VALUES
('TRY', 'Turk Lirasi', '₺', 1.0000, 1, 1),
('USD', 'US Dollar', '$', 0.0000, 0, 1),
('EUR', 'Euro', '€', 0.0000, 0, 1);

-- ============================================
-- KATEGORILER
-- ============================================

CREATE TABLE `categories` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `parent_id`  INT UNSIGNED DEFAULT NULL,
  `slug`       VARCHAR(220) NOT NULL UNIQUE,
  `image`      VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`parent_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `category_translations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `lang`        VARCHAR(5) NOT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `description` TEXT,
  `meta_title`  VARCHAR(255),
  `meta_desc`   VARCHAR(500),
  UNIQUE KEY `unique_cat_lang` (`category_id`, `lang`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MARKALAR
-- ============================================

CREATE TABLE `brands` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`       VARCHAR(220) NOT NULL UNIQUE,
  `logo`       VARCHAR(255),
  `website`    VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `brand_translations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `brand_id`    INT UNSIGNED NOT NULL,
  `lang`        VARCHAR(5) NOT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `description` TEXT,
  `meta_title`  VARCHAR(255),
  `meta_desc`   VARCHAR(500),
  UNIQUE KEY `unique_brand_lang` (`brand_id`, `lang`),
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUNLER
-- ============================================

CREATE TABLE `products` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id`      INT UNSIGNED DEFAULT NULL,
  `brand_id`         INT UNSIGNED DEFAULT NULL,
  `slug`             VARCHAR(220) NOT NULL UNIQUE,
  `sku`              VARCHAR(100) UNIQUE,
  `barcode`          VARCHAR(100),
  `price`            DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `sale_price`       DECIMAL(10,2) DEFAULT NULL,
  `tax_rate`         TINYINT UNSIGNED DEFAULT 20,
  `stock`            INT NOT NULL DEFAULT 0,
  `stock_status`     ENUM('in_stock','out_of_stock','pre_order','coming_soon','backorder') DEFAULT 'in_stock',
  `stock_alert_qty`  INT DEFAULT NULL,
  `order_limit_per_product` INT DEFAULT NULL,
  `order_limit_per_customer` INT DEFAULT NULL,
  `has_variations`   TINYINT(1) DEFAULT 0,
  `shipping_type`    ENUM('domestic','international') DEFAULT 'domestic',
  `shipping_days_min` INT DEFAULT 1,
  `shipping_days_max` INT DEFAULT 2,
  `shipping_note`    VARCHAR(255) DEFAULT NULL,
  `is_featured`      TINYINT(1) DEFAULT 0,
  `is_best_seller`   TINYINT(1) DEFAULT 0,
  `is_most_clicked`  TINYINT(1) DEFAULT 0,
  `is_recommended`   TINYINT(1) DEFAULT 0,
  `click_count`      INT DEFAULT 0,
  `sale_count`       INT DEFAULT 0,
  `view_count`       INT DEFAULT 0,
  `video_url`        VARCHAR(500) DEFAULT NULL,
  `video_file`       VARCHAR(255) DEFAULT NULL,
  `warranty_period`  VARCHAR(100) DEFAULT NULL,
  `warranty_terms`   TEXT DEFAULT NULL,
  `compatible_with`  TEXT DEFAULT NULL,
  `is_active`        TINYINT(1) DEFAULT 1,
  `created_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_translations` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`      INT UNSIGNED NOT NULL,
  `lang`            VARCHAR(5) NOT NULL,
  `name`            VARCHAR(255) NOT NULL,
  `short_desc`      TEXT,
  `long_desc`       LONGTEXT,
  `meta_title`      VARCHAR(255),
  `meta_desc`       VARCHAR(500),
  `meta_keywords`   VARCHAR(500),
  `og_title`        VARCHAR(255),
  `og_desc`         VARCHAR(500),
  UNIQUE KEY `unique_prod_lang` (`product_id`, `lang`),
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUN GORSELLERI
-- ============================================

CREATE TABLE `product_images` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `webp_path`  VARCHAR(255),
  `alt`        VARCHAR(255),
  `sort_order` INT DEFAULT 0,
  `is_cover`   TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUN TEKNIK OZELLIKLERI
-- ============================================

CREATE TABLE `product_attributes` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT UNSIGNED NOT NULL,
  `attr_name`  VARCHAR(150) NOT NULL,
  `attr_value` VARCHAR(500) NOT NULL,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- VARYASYON NITELIKLERI (renk, beden vb.)
-- ============================================

CREATE TABLE `variation_types` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `slug`       VARCHAR(120) NOT NULL UNIQUE,
  `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `variation_types` (`name`, `slug`, `sort_order`) VALUES
('Renk', 'renk', 1);

CREATE TABLE `variation_options` (
  `id`               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `variation_type_id` INT UNSIGNED NOT NULL,
  `name`             VARCHAR(100) NOT NULL,
  `value`            VARCHAR(100),
  `sort_order`       INT DEFAULT 0,
  FOREIGN KEY (`variation_type_id`) REFERENCES `variation_types`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUN VARYASYONLARI
-- ============================================

CREATE TABLE `product_variations` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`   INT UNSIGNED NOT NULL,
  `sku`          VARCHAR(100),
  `price`        DECIMAL(10,2) DEFAULT NULL,
  `sale_price`   DECIMAL(10,2) DEFAULT NULL,
  `stock`        INT DEFAULT 0,
  `stock_status` ENUM('in_stock','out_of_stock','pre_order') DEFAULT 'in_stock',
  `image_id`     INT UNSIGNED DEFAULT NULL,
  `is_active`    TINYINT(1) DEFAULT 1,
  `sort_order`   INT DEFAULT 0,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_variation_options` (
  `id`                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_variation_id` INT UNSIGNED NOT NULL,
  `variation_type_id`   INT UNSIGNED NOT NULL,
  `variation_option_id` INT UNSIGNED NOT NULL,
  FOREIGN KEY (`product_variation_id`) REFERENCES `product_variations`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variation_type_id`)    REFERENCES `variation_types`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`variation_option_id`)  REFERENCES `variation_options`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BAGLANTILI URUNLER
-- ============================================

CREATE TABLE `product_relations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `related_id`  INT UNSIGNED NOT NULL,
  `type`        ENUM('cross_sell','similar','upsell') DEFAULT 'cross_sell',
  UNIQUE KEY `unique_relation` (`product_id`, `related_id`, `type`),
  FOREIGN KEY (`product_id`)  REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`related_id`)  REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUN DEGERLENDIRMELERI
-- ============================================

CREATE TABLE `reviews` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`    INT UNSIGNED NOT NULL,
  `user_id`       INT UNSIGNED DEFAULT NULL,
  `order_id`      INT UNSIGNED DEFAULT NULL,
  `author_name`   VARCHAR(100) NOT NULL,
  `rating`        TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `comment`       TEXT NOT NULL,
  `is_verified`   TINYINT(1) DEFAULT 0,
  `is_manual`     TINYINT(1) DEFAULT 0,
  `is_approved`   TINYINT(1) DEFAULT 0,
  `helpful_yes`   INT DEFAULT 0,
  `helpful_no`    INT DEFAULT 0,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `review_images` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `review_id` INT UNSIGNED NOT NULL,
  `path`      VARCHAR(255) NOT NULL,
  `webp_path` VARCHAR(255),
  FOREIGN KEY (`review_id`) REFERENCES `reviews`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- URUN SORULARI
-- ============================================

CREATE TABLE `questions` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `guest_email` VARCHAR(150) DEFAULT NULL,
  `author_name` VARCHAR(100) NOT NULL,
  `question`    TEXT NOT NULL,
  `answer`      TEXT DEFAULT NULL,
  `answered_at` DATETIME DEFAULT NULL,
  `helpful_yes` INT DEFAULT 0,
  `helpful_no`  INT DEFAULT 0,
  `is_approved` TINYINT(1) DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FAVORILER
-- ============================================

CREATE TABLE `wishlists` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_wishlist` (`user_id`, `product_id`),
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FIYAT ALARMLARI
-- ============================================

CREATE TABLE `price_alerts` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`   INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED DEFAULT NULL,
  `guest_email`  VARCHAR(150) DEFAULT NULL,
  `target_price` DECIMAL(10,2) DEFAULT NULL,
  `is_notified`  TINYINT(1) DEFAULT 0,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- STOK ALARMLARI
-- ============================================

CREATE TABLE `stock_alerts` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `guest_email` VARCHAR(150) DEFAULT NULL,
  `is_notified` TINYINT(1) DEFAULT 0,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SON INCELENEN URUNLER
-- ============================================

CREATE TABLE `recently_viewed` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT UNSIGNED DEFAULT NULL,
  `session_id` VARCHAR(100) DEFAULT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `viewed_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SEPET
-- ============================================

CREATE TABLE `carts` (
  `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`              INT UNSIGNED DEFAULT NULL,
  `session_id`           VARCHAR(100) DEFAULT NULL,
  `product_id`           INT UNSIGNED NOT NULL,
  `product_variation_id` INT UNSIGNED DEFAULT NULL,
  `quantity`             INT NOT NULL DEFAULT 1,
  `created_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- TERK EDILMIS SEPETLER
-- ============================================

CREATE TABLE `abandoned_carts` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED DEFAULT NULL,
  `email`        VARCHAR(150),
  `cart_data`    JSON,
  `total`        DECIMAL(10,2),
  `notified_at`  DATETIME DEFAULT NULL,
  `recovered`    TINYINT(1) DEFAULT 0,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- KUPONLAR
-- ============================================

CREATE TABLE `coupons` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`            VARCHAR(50) NOT NULL UNIQUE,
  `type`            ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value`           DECIMAL(10,2) NOT NULL,
  `min_order`       DECIMAL(10,2) DEFAULT 0.00,
  `max_discount`    DECIMAL(10,2) DEFAULT NULL,
  `usage_limit`     INT DEFAULT NULL,
  `usage_per_user`  INT DEFAULT NULL,
  `usage_count`     INT DEFAULT 0,
  `applies_to`      ENUM('all','products','categories') DEFAULT 'all',
  `applies_ids`     TEXT DEFAULT NULL,
  `expires_at`      DATE DEFAULT NULL,
  `is_active`       TINYINT(1) DEFAULT 1,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SIPARISLER
-- ============================================

CREATE TABLE `orders` (
  `id`                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_no`          VARCHAR(30) NOT NULL UNIQUE,
  `user_id`           INT UNSIGNED DEFAULT NULL,
  `guest_email`       VARCHAR(150) DEFAULT NULL,
  `guest_token`       VARCHAR(100) DEFAULT NULL,
  `status`            ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  `subtotal`          DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount`        DECIMAL(10,2) DEFAULT 0.00,
  `shipping_cost`     DECIMAL(10,2) DEFAULT 0.00,
  `discount`          DECIMAL(10,2) DEFAULT 0.00,
  `total`             DECIMAL(10,2) NOT NULL,
  `coupon_id`         INT UNSIGNED DEFAULT NULL,
  `coupon_code`       VARCHAR(50) DEFAULT NULL,
  `currency`          VARCHAR(5) DEFAULT 'TRY',
  `currency_rate`     DECIMAL(10,4) DEFAULT 1.0000,
  `payment_method`    VARCHAR(60) DEFAULT NULL,
  `payment_status`    ENUM('unpaid','paid','refunded') DEFAULT 'unpaid',
  `payment_ref`       VARCHAR(255) DEFAULT NULL,
  `installment`       INT DEFAULT 1,
  `shipping_name`     VARCHAR(150),
  `shipping_phone`    VARCHAR(20),
  `shipping_city`     VARCHAR(80),
  `shipping_district` VARCHAR(80),
  `shipping_address`  TEXT,
  `shipping_zip`      VARCHAR(10),
  `billing_same`      TINYINT(1) DEFAULT 1,
  `billing_name`      VARCHAR(150),
  `billing_address`   TEXT,
  `billing_tax_no`    VARCHAR(20),
  `billing_company`   VARCHAR(150),
  `cargo_company`     VARCHAR(100) DEFAULT NULL,
  `cargo_tracking`    VARCHAR(200) DEFAULT NULL,
  `cargo_url`         VARCHAR(500) DEFAULT NULL,
  `estimated_delivery` DATE DEFAULT NULL,
  `notes`             TEXT,
  `is_suspicious`     TINYINT(1) DEFAULT 0,
  `ip_address`        VARCHAR(45),
  `created_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`coupon_id`) REFERENCES `coupons`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
  `id`                   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`             INT UNSIGNED NOT NULL,
  `product_id`           INT UNSIGNED DEFAULT NULL,
  `product_variation_id` INT UNSIGNED DEFAULT NULL,
  `name`                 VARCHAR(255) NOT NULL,
  `sku`                  VARCHAR(100),
  `variation_info`       VARCHAR(255),
  `price`                DECIMAL(10,2) NOT NULL,
  `tax_rate`             TINYINT UNSIGNED DEFAULT 20,
  `quantity`             INT NOT NULL DEFAULT 1,
  `shipping_type`        ENUM('domestic','international') DEFAULT 'domestic',
  FOREIGN KEY (`order_id`)   REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_status_history` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT UNSIGNED NOT NULL,
  `status`     VARCHAR(60) NOT NULL,
  `note`       TEXT,
  `created_by` INT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- IADELER
-- ============================================

CREATE TABLE `returns` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`    INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `reason`      TEXT NOT NULL,
  `status`      ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
  `admin_note`  TEXT,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- FATURALAR
-- ============================================

CREATE TABLE `invoices` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`     INT UNSIGNED NOT NULL,
  `type`         ENUM('proforma','e_invoice','e_archive','return','cancel') NOT NULL,
  `invoice_no`   VARCHAR(100),
  `path`         VARCHAR(255),
  `issued_at`    DATETIME DEFAULT NULL,
  `sent_at`      DATETIME DEFAULT NULL,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MESAFELI SATIS SOZLESMESI
-- ============================================

CREATE TABLE `order_contracts` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`   INT UNSIGNED NOT NULL UNIQUE,
  `path`       VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BOLGE VERILERI (IL / ILCE / MAHALLE)
-- ============================================

CREATE TABLE `cities` (
  `id`   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(80) NOT NULL,
  `code` VARCHAR(5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `districts` (
  `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `city_id` INT UNSIGNED NOT NULL,
  `name`    VARCHAR(100) NOT NULL,
  FOREIGN KEY (`city_id`) REFERENCES `cities`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `neighborhoods` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `district_id` INT UNSIGNED NOT NULL,
  `name`        VARCHAR(150) NOT NULL,
  `zip`         VARCHAR(10),
  FOREIGN KEY (`district_id`) REFERENCES `districts`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SLIDERLAR
-- ============================================

CREATE TABLE `sliders` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `image`       VARCHAR(255) NOT NULL,
  `webp_image`  VARCHAR(255),
  `link`        VARCHAR(500),
  `sort_order`  INT DEFAULT 0,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `slider_translations` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slider_id` INT UNSIGNED NOT NULL,
  `lang`      VARCHAR(5) NOT NULL,
  `title`     VARCHAR(255),
  `subtitle`  VARCHAR(500),
  `btn_text`  VARCHAR(100),
  UNIQUE KEY `unique_slider_lang` (`slider_id`, `lang`),
  FOREIGN KEY (`slider_id`) REFERENCES `sliders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BANNERLAR
-- ============================================

CREATE TABLE `banners` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `position`   VARCHAR(60) NOT NULL,
  `image`      VARCHAR(255) NOT NULL,
  `webp_image` VARCHAR(255),
  `link`       VARCHAR(500),
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `banner_translations` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `banner_id` INT UNSIGNED NOT NULL,
  `lang`      VARCHAR(5) NOT NULL,
  `title`     VARCHAR(255),
  `subtitle`  VARCHAR(500),
  UNIQUE KEY `unique_banner_lang` (`banner_id`, `lang`),
  FOREIGN KEY (`banner_id`) REFERENCES `banners`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- POPUP
-- ============================================

CREATE TABLE `popups` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `image`       VARCHAR(255),
  `link`        VARCHAR(500),
  `delay`       INT DEFAULT 3,
  `show_once`   TINYINT(1) DEFAULT 1,
  `is_active`   TINYINT(1) DEFAULT 1,
  `starts_at`   DATETIME DEFAULT NULL,
  `ends_at`     DATETIME DEFAULT NULL,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `popup_translations` (
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `popup_id` INT UNSIGNED NOT NULL,
  `lang`     VARCHAR(5) NOT NULL,
  `title`    VARCHAR(255),
  `content`  TEXT,
  UNIQUE KEY `unique_popup_lang` (`popup_id`, `lang`),
  FOREIGN KEY (`popup_id`) REFERENCES `popups`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BILDIRIM BANDI
-- ============================================

CREATE TABLE `announcement_bars` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bg_color`   VARCHAR(20) DEFAULT '#000000',
  `text_color` VARCHAR(20) DEFAULT '#ffffff',
  `link`       VARCHAR(500),
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `announcement_bar_translations` (
  `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bar_id`  INT UNSIGNED NOT NULL,
  `lang`    VARCHAR(5) NOT NULL,
  `text`    VARCHAR(500) NOT NULL,
  UNIQUE KEY `unique_bar_lang` (`bar_id`, `lang`),
  FOREIGN KEY (`bar_id`) REFERENCES `announcement_bars`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- MENULER
-- ============================================

CREATE TABLE `menus` (
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `location` VARCHAR(60) NOT NULL UNIQUE,
  `name`     VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `menus` (`location`, `name`) VALUES
('header', 'Ust Menu'),
('footer', 'Alt Menu');

CREATE TABLE `menu_items` (
  `id`        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `menu_id`   INT UNSIGNED NOT NULL,
  `parent_id` INT UNSIGNED DEFAULT NULL,
  `type`      ENUM('page','category','url','product') DEFAULT 'url',
  `target_id` INT UNSIGNED DEFAULT NULL,
  `url`       VARCHAR(500),
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menu_item_translations` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `menu_item_id` INT UNSIGNED NOT NULL,
  `lang`         VARCHAR(5) NOT NULL,
  `label`        VARCHAR(150) NOT NULL,
  UNIQUE KEY `unique_menu_item_lang` (`menu_item_id`, `lang`),
  FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAYFALAR
-- ============================================

CREATE TABLE `pages` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`       VARCHAR(220) NOT NULL UNIQUE,
  `template`   VARCHAR(60) DEFAULT 'default',
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `page_translations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_id`     INT UNSIGNED NOT NULL,
  `lang`        VARCHAR(5) NOT NULL,
  `title`       VARCHAR(255) NOT NULL,
  `content`     LONGTEXT,
  `meta_title`  VARCHAR(255),
  `meta_desc`   VARCHAR(500),
  UNIQUE KEY `unique_page_lang` (`page_id`, `lang`),
  FOREIGN KEY (`page_id`) REFERENCES `pages`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayilan sayfalar
INSERT INTO `pages` (`slug`, `template`) VALUES
('hakkimizda', 'default'),
('iletisim', 'contact'),
('kvkk', 'policy'),
('gizlilik-politikasi', 'policy'),
('mesafeli-satis-sozlesmesi', 'contract'),
('iade-politikasi', 'policy');

-- ============================================
-- BLOG
-- ============================================

CREATE TABLE `blogs` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`         VARCHAR(220) NOT NULL UNIQUE,
  `image`        VARCHAR(255),
  `webp_image`   VARCHAR(255),
  `author_id`    INT UNSIGNED DEFAULT NULL,
  `is_active`    TINYINT(1) DEFAULT 1,
  `published_at` DATETIME DEFAULT NULL,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blog_translations` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `blog_id`      INT UNSIGNED NOT NULL,
  `lang`         VARCHAR(5) NOT NULL,
  `title`        VARCHAR(255) NOT NULL,
  `excerpt`      TEXT,
  `content`      LONGTEXT,
  `meta_title`   VARCHAR(255),
  `meta_desc`    VARCHAR(500),
  UNIQUE KEY `unique_blog_lang` (`blog_id`, `lang`),
  FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SSS
-- ============================================

CREATE TABLE `faqs` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sort_order` INT DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `faq_translations` (
  `id`       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `faq_id`   INT UNSIGNED NOT NULL,
  `lang`     VARCHAR(5) NOT NULL,
  `question` TEXT NOT NULL,
  `answer`   LONGTEXT NOT NULL,
  UNIQUE KEY `unique_faq_lang` (`faq_id`, `lang`),
  FOREIGN KEY (`faq_id`) REFERENCES `faqs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- WIDGET ALANLARI
-- ============================================

CREATE TABLE `widgets` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `location`   VARCHAR(60) NOT NULL,
  `type`       VARCHAR(60) NOT NULL,
  `data`       JSON,
  `sort_order` INT DEFAULT 0,
  `is_active`  TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- YONLENDIRMELER (301)
-- ============================================

CREATE TABLE `redirects` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `from_url`    VARCHAR(500) NOT NULL,
  `to_url`      VARCHAR(500) NOT NULL,
  `type`        SMALLINT DEFAULT 301,
  `hit_count`   INT DEFAULT 0,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- BULTEN ABONELERI
-- ============================================

CREATE TABLE `newsletters` (
  `id`            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`         VARCHAR(150) NOT NULL UNIQUE,
  `name`          VARCHAR(100),
  `is_active`     TINYINT(1) DEFAULT 1,
  `token`         VARCHAR(100),
  `subscribed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- KVKK TALEPLERI
-- ============================================

CREATE TABLE `gdpr_requests` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`      INT UNSIGNED DEFAULT NULL,
  `guest_email`  VARCHAR(150) DEFAULT NULL,
  `type`         ENUM('download','delete') NOT NULL,
  `status`       ENUM('pending','approved','rejected','completed') DEFAULT 'pending',
  `admin_note`   TEXT,
  `scheduled_at` DATETIME DEFAULT NULL,
  `completed_at` DATETIME DEFAULT NULL,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- CEREZ KAYITLARI
-- ============================================

CREATE TABLE `cookie_consents` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `session_id`  VARCHAR(100) NOT NULL,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `accepted`    TINYINT(1) DEFAULT 0,
  `ip`          VARCHAR(45),
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- AKTIVITE LOGLARI
-- ============================================

CREATE TABLE `activity_logs` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `action`      VARCHAR(100) NOT NULL,
  `model`       VARCHAR(60),
  `model_id`    INT UNSIGNED,
  `old_data`    JSON,
  `new_data`    JSON,
  `ip`          VARCHAR(45),
  `user_agent`  VARCHAR(500),
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- HATA LOGLARI
-- ============================================

CREATE TABLE `error_logs` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `level`       ENUM('error','warning','info') DEFAULT 'error',
  `message`     TEXT NOT NULL,
  `file`        VARCHAR(500),
  `line`        INT,
  `trace`       LONGTEXT,
  `url`         VARCHAR(500),
  `ip`          VARCHAR(45),
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- GUVENLIK LOGLARI
-- ============================================

CREATE TABLE `security_logs` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `event`       VARCHAR(100) NOT NULL,
  `ip`          VARCHAR(45),
  `user_agent`  VARCHAR(500),
  `location`    VARCHAR(200),
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- SAHTE SIPARIS KORUMASI
-- ============================================

CREATE TABLE `suspicious_ips` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip`           VARCHAR(45) NOT NULL,
  `reason`       VARCHAR(255),
  `attempt_count` INT DEFAULT 1,
  `blocked_until` DATETIME DEFAULT NULL,
  `is_permanent` TINYINT(1) DEFAULT 0,
  `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- GOOGLE ENTEGRASYONLARI
-- ============================================

CREATE TABLE `google_integrations` (
  `id`                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ga4_measurement_id`      VARCHAR(50),
  `search_console_code`     VARCHAR(500),
  `merchant_feed_url`       VARCHAR(500),
  `sitemap_last_updated`    DATETIME DEFAULT NULL,
  `updated_at`              TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `google_integrations` (`id`) VALUES (1);

-- ============================================
-- SITE AYARLARI
-- ============================================

CREATE TABLE `settings` (
  `key`   VARCHAR(150) PRIMARY KEY,
  `value` LONGTEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`key`, `value`) VALUES
('site_name', 'Magazam'),
('site_email', 'info@magazam.com'),
('site_phone', ''),
('site_address', ''),
('site_logo', ''),
('site_favicon', ''),
('currency_default', 'TRY'),
('lang_default', 'tr'),
('tax_included', '1'),
('shipping_free', '1'),
('shipping_cost', '0'),
('maintenance_mode', '0'),
('maintenance_allowed_ips', ''),
('whatsapp_number', ''),
('whatsapp_message', ''),
('facebook_url', ''),
('instagram_url', ''),
('twitter_url', ''),
('youtube_url', ''),
('tiktok_url', ''),
('admin_path', 'yonetim'),
('session_lifetime', '120'),
('max_login_attempts', '3'),
('lockout_minutes', '15'),
('order_suspicious_limit', '3'),
('order_suspicious_minutes', '30'),
('cache_enabled', '1'),
('cache_lifetime', '3600'),
('backup_frequency', 'weekly'),
('smtp_host', ''),
('smtp_port', '587'),
('smtp_user', ''),
('smtp_pass', ''),
('smtp_from', ''),
('smtp_from_name', ''),
('abandoned_cart_hours', '3'),
('tcmb_xml_url', 'https://www.tcmb.gov.tr/kurlar/today.xml'),
('currency_last_updated', '');

-- ============================================
-- MAIL SABLONLARI
-- ============================================

CREATE TABLE `mail_templates` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`        VARCHAR(60) NOT NULL UNIQUE,
  `is_active`   TINYINT(1) DEFAULT 1,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `mail_template_translations` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `template_id` INT UNSIGNED NOT NULL,
  `lang`        VARCHAR(5) NOT NULL,
  `subject`     VARCHAR(255) NOT NULL,
  `body`        LONGTEXT NOT NULL,
  UNIQUE KEY `unique_mail_lang` (`template_id`, `lang`),
  FOREIGN KEY (`template_id`) REFERENCES `mail_templates`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `mail_templates` (`code`) VALUES
('order_placed'),
('order_confirmed'),
('order_processing'),
('order_shipped'),
('order_delivered'),
('order_cancelled'),
('invoice_uploaded'),
('return_approved'),
('return_rejected'),
('abandoned_cart'),
('price_alert'),
('stock_alert'),
('newsletter_confirm'),
('email_verify'),
('password_reset'),
('new_device_login'),
('kvkk_request'),
('proforma_invoice');

-- ============================================
-- YEDEKLEME KAYITLARI
-- ============================================

CREATE TABLE `backups` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `filename`   VARCHAR(255) NOT NULL,
  `size`       BIGINT DEFAULT 0,
  `type`       ENUM('auto','manual') DEFAULT 'auto',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- INDEXLER (Performans)
-- ============================================

ALTER TABLE `products`        ADD INDEX `idx_category`    (`category_id`);
ALTER TABLE `products`        ADD INDEX `idx_brand`       (`brand_id`);
ALTER TABLE `products`        ADD INDEX `idx_active`      (`is_active`);
ALTER TABLE `products`        ADD INDEX `idx_stock`       (`stock_status`);
ALTER TABLE `products`        ADD INDEX `idx_price`       (`price`);
ALTER TABLE `product_translations` ADD INDEX `idx_lang`   (`lang`);
ALTER TABLE `orders`          ADD INDEX `idx_user`        (`user_id`);
ALTER TABLE `orders`          ADD INDEX `idx_status`      (`status`);
ALTER TABLE `orders`          ADD INDEX `idx_created`     (`created_at`);
ALTER TABLE `orders`          ADD INDEX `idx_order_no`    (`order_no`);
ALTER TABLE `order_items`     ADD INDEX `idx_order`       (`order_id`);
ALTER TABLE `reviews`         ADD INDEX `idx_product`     (`product_id`);
ALTER TABLE `reviews`         ADD INDEX `idx_approved`    (`is_approved`);
ALTER TABLE `carts`           ADD INDEX `idx_session`     (`session_id`);
ALTER TABLE `recently_viewed` ADD INDEX `idx_session`     (`session_id`);
ALTER TABLE `activity_logs`   ADD INDEX `idx_created`     (`created_at`);
ALTER TABLE `error_logs`      ADD INDEX `idx_created`     (`created_at`);

-- ============================================
-- DEMO ADMIN KULLANICI
-- Sifre: Admin1234!
-- ============================================

INSERT INTO `users` (`name`, `surname`, `email`, `password`, `role`, `email_verified`, `is_active`) VALUES
('Site', 'Yoneticisi', 'admin@magazam.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, 1);

SET FOREIGN_KEY_CHECKS = 1;
