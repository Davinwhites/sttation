-- security_additions.sql
-- Run this once against the `stationery` database. Adds brute-force
-- protection for the login endpoint (required to satisfy basic mobile
-- app security scans used by Play Store / App Store review tooling).

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `identifier` VARCHAR(150) NOT NULL COMMENT 'email + ip combined',
  `attempted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `identifier_idx` (`identifier`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
