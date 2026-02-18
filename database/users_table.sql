-- ═══════════════════════════════════════════════════════════
-- 👤 جدول کاربران سایت (members) - کتاب نت
-- این جدول جداست از جدول users که برای ادمین‌هاست
-- ═══════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS `members` (
    `id`         INT(11)      NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `phone`      VARCHAR(20)  DEFAULT NULL,
    `avatar`     VARCHAR(255) DEFAULT NULL,
    `status`     TINYINT(1)   DEFAULT 1,
    `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_email`  (`email`),
    INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;