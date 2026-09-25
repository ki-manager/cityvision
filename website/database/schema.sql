-- =====================================================================
-- Schema: Kinderarztpraxis Königslutter am Elm
-- MySQL 5.7+ / MariaDB 10.3+ (goneo Shared Hosting kompatibel)
-- Zeichensatz: utf8mb4 / utf8mb4_unicode_ci
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- users: Admin-Benutzer
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`      VARCHAR(64)  NOT NULL,
    `email`         VARCHAR(190) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `name`          VARCHAR(128) NOT NULL DEFAULT '',
    `role`          ENUM('admin','editor') NOT NULL DEFAULT 'editor',
    `active`        TINYINT(1)   NOT NULL DEFAULT 1,
    `last_login_at` DATETIME     NULL DEFAULT NULL,
    `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_username` (`username`),
    UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- login_attempts: Brute-Force-Schutz
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(190) NOT NULL,
    `ip_hash`    CHAR(64)      NOT NULL,
    `success`    TINYINT(1)    NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ix_login_attempts_lookup` (`username`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- settings: zentrale Konfigurationswerte (Admin pflegbar)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `setting_key`   VARCHAR(128) NOT NULL,
    `setting_value` TEXT         NULL,
    `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- pages: editierbare Seitentexte
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pages` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(128) NOT NULL,
    `locale`      CHAR(5)      NOT NULL DEFAULT 'de',
    `title`       VARCHAR(190) NOT NULL,
    `meta_description` VARCHAR(320) NOT NULL DEFAULT '',
    `content`     MEDIUMTEXT   NULL,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_pages_slug_locale` (`slug`, `locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- translations: UI-Texte für Mehrsprachigkeit (de/en/ar, erweiterbar)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `translations` (
    `id`      INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `locale`  CHAR(5)      NOT NULL,
    `key`     VARCHAR(190) NOT NULL,
    `value`   TEXT         NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_translations_locale_key` (`locale`, `key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- team: Ärztinnen/Ärzte und Mitarbeitende
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `team` (
    `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`           VARCHAR(128) NOT NULL,
    `name`           VARCHAR(128) NOT NULL,
    `role`           VARCHAR(128) NOT NULL DEFAULT '',
    `role_type`      ENUM('arzt','mitarbeitende') NOT NULL DEFAULT 'mitarbeitende',
    `specialty`      VARCHAR(190) NOT NULL DEFAULT '',
    `qualifications` TEXT         NULL,
    `bio`            MEDIUMTEXT   NULL,
    `photo`          VARCHAR(255) NOT NULL DEFAULT '',
    `sort_order`     INT          NOT NULL DEFAULT 0,
    `active`         TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_team_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- services: Leistungen (Gruppen + Einzelleistungen)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `services` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `slug`        VARCHAR(128) NOT NULL,
    `group_key`   VARCHAR(64)  NOT NULL DEFAULT 'allgemein',
    `title`       VARCHAR(190) NOT NULL,
    `description` TEXT         NULL,
    `icon`        VARCHAR(64)  NOT NULL DEFAULT 'stethoscope',
    `homepage`     TINYINT(1)   NOT NULL DEFAULT 0,
    `sort_order`  INT          NOT NULL DEFAULT 0,
    `active`      TINYINT(1)   NOT NULL DEFAULT 1,
    `updated_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_services_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- opening_hours: Sprechzeiten
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `opening_hours` (
    `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `weekday`         TINYINT UNSIGNED NOT NULL, -- 1=Montag ... 7=Sonntag
    `morning_from`    TIME NULL DEFAULT NULL,
    `morning_to`      TIME NULL DEFAULT NULL,
    `afternoon_from`  TIME NULL DEFAULT NULL,
    `afternoon_to`    TIME NULL DEFAULT NULL,
    `closed`          TINYINT(1) NOT NULL DEFAULT 0,
    `note`            VARCHAR(190) NOT NULL DEFAULT '',
    `sort_order`      INT NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_opening_hours_weekday` (`weekday`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- news: Aktuelles aus der Praxis
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `news` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `title`        VARCHAR(190) NOT NULL,
    `slug`         VARCHAR(190) NOT NULL,
    `summary`      VARCHAR(500) NOT NULL DEFAULT '',
    `content`      MEDIUMTEXT   NULL,
    `publish_date` DATE         NOT NULL,
    `start_date`   DATE         NULL DEFAULT NULL,
    `end_date`     DATE         NULL DEFAULT NULL,
    `important`    TINYINT(1)   NOT NULL DEFAULT 0,
    `active`       TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_news_slug` (`slug`),
    KEY `ix_news_publish` (`active`, `publish_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- contact_requests: Kontaktformular-Anfragen
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `contact_requests` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `firstname`  VARCHAR(128) NOT NULL,
    `lastname`   VARCHAR(128) NOT NULL,
    `email`      VARCHAR(190) NOT NULL,
    `phone`      VARCHAR(64)  NOT NULL DEFAULT '',
    `subject`    VARCHAR(190) NOT NULL,
    `message`    TEXT         NOT NULL,
    `status`     ENUM('neu','in_bearbeitung','erledigt','geloescht') NOT NULL DEFAULT 'neu',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ix_contact_requests_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- media: hochgeladene Dateien (Admin-Uploads)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `media` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `filename`   VARCHAR(255) NOT NULL,
    `original_name` VARCHAR(255) NOT NULL DEFAULT '',
    `mime_type`  VARCHAR(128) NOT NULL,
    `size`       INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- audit_log: Admin-Aktionen
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `audit_log` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED NULL DEFAULT NULL,
    `action`     VARCHAR(128) NOT NULL,
    `details`    VARCHAR(500) NOT NULL DEFAULT '',
    `ip_hash`    CHAR(64)     NOT NULL DEFAULT '',
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `ix_audit_log_user` (`user_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
