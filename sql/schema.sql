-- Database schema + admin seed.

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS `discover_saudi`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `discover_saudi`;

DROP TABLE IF EXISTS `places`;
CREATE TABLE `places` (
  `id`              INT AUTO_INCREMENT PRIMARY KEY,
  `name_ar`         VARCHAR(120) NOT NULL,
  `region_type`     ENUM(
                      'المنطقة الوسطى',
                      'المنطقة الغربية',
                      'المنطقة الشرقية',
                      'المنطقة الشمالية',
                      'المنطقة الجنوبية'
                    ) NOT NULL,
  `tagline`         VARCHAR(255) DEFAULT NULL,
  `description`     TEXT NOT NULL,
  `location`        VARCHAR(255) DEFAULT NULL,
  `area_km2`        VARCHAR(50)  DEFAULT NULL,
  `founded`         VARCHAR(120) DEFAULT NULL,
  `features`        TEXT,
  `activities`      TEXT,
  `cities`          VARCHAR(255) DEFAULT NULL,
  `landmarks`       TEXT,
  `hero_image`      VARCHAR(255) NOT NULL,
  `gallery_image_1` VARCHAR(255) DEFAULT NULL,
  `gallery_image_2` VARCHAR(255) DEFAULT NULL,
  `gallery_image_3` VARCHAR(255) DEFAULT NULL,
  `created_at`      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(50)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin: admin / admin123
INSERT INTO `admins` (`username`, `password_hash`) VALUES
('admin', '$2b$10$NKfpzPM.6lTxJag43mmukuq3cIe13yhHjeK0NaSfneihL.kMwZwYq');
