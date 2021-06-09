CREATE TABLE `bounce_log` (
  `bounce_log_id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) NOT NULL,
  `created` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`bounce_log_id`),
  INDEX `email_address_created` (`email_address`, `created`)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
