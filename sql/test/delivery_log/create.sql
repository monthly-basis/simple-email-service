CREATE TABLE `delivery_log` (
  `delivery_log_id` int unsigned NOT NULL AUTO_INCREMENT,
  `email_address` varchar(255) NOT NULL,
  `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`delivery_log_id`),
  INDEX `email_address_created` (`email_address`, `created`)
) ENGINE=InnoDB CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
