CREATE TABLE `users` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) UNIQUE NOT NULL COMMENT 'Índice para búsqueda',
  `username` varchar(50) UNIQUE NOT NULL COMMENT 'Índice para login',
  `password` varchar(255) NOT NULL,
  `phone` varchar(20),
  `profile_image` text DEFAULT null,
  `totp_secret` varchar(255) DEFAULT null,
  `created_at` datetime DEFAULT (current_timestamp()),
  `updated_at` datetime,
  `deleted_at` datetime,
  `last_login_at` datetime,
  `last_ip` varchar(50),
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `roles` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255),
  `created_at` datetime DEFAULT (current_timestamp()),
  `created_by` int,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `user_roles` (
  `id_user` int NOT NULL,
  `id_role` int NOT NULL,
  PRIMARY KEY (`id_user`, `id_role`)
);
-- Permission Modules
CREATE TABLE `modules` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL
);

CREATE TABLE `permissions` (
  `id` INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `description` VARCHAR(512),
  `id_module` INT NOT NULL,
  `type` ENUM('VIEW', 'READ', 'CREATE', 'UPDATE', 'DELETE', 'EXPORT', 'IMPORT', 'MANAGE', 'EXECUTE') NOT NULL
);

CREATE TABLE `role_permissions` (
  `id_role` int NOT NULL,
  `id_permission` int NOT NULL,
  PRIMARY KEY (`id_role`, `id_permission`)
);

CREATE TABLE `logs` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `entity_type` varchar(50),
  `entity_id` int,
  `created_by` int,
  `ip_address` varchar(50),
  `user_agent` varchar(255),
  `log_date` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `log_catalog` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `code` varchar(50) UNIQUE NOT NULL,
  `description` varchar(255) NOT NULL
);

CREATE TABLE `user_sessions` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `token` varchar(255) UNIQUE NOT NULL,
  `device_info` varchar(255),
  `ip_address` varchar(50),
  `user_agent` varchar(255),
  `created_at` datetime DEFAULT (current_timestamp()),
  `expires_at` datetime
);

CREATE TABLE `categories` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT (current_timestamp()),
  `created_by` int,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `terminals` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT (current_timestamp()),
  `created_by` int,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `terminal_categories` (
  `id_terminal` int NOT NULL,
  `id_category` int NOT NULL,
  PRIMARY KEY (`id_terminal`, `id_category`)
);

CREATE TABLE `groups` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT (current_timestamp()),
  `created_by` int,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `group_users` (
  `id_user` int NOT NULL,
  `id_group` int NOT NULL,
  PRIMARY KEY (`id_user`, `id_group`)
);

CREATE TABLE `terminal_groups` (
  `id_terminal` int NOT NULL,
  `id_group` int NOT NULL,
  PRIMARY KEY (`id_terminal`, `id_group`)
);

CREATE TABLE `field_types` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL
);

CREATE TABLE `ticket_fields` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_category` int NOT NULL,
  `field` varchar(50) NOT NULL,
  `type` int,
  `created_at` datetime DEFAULT (current_timestamp()),
  `created_by` int,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `ticket_status` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `status` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `ticket_priority` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `priority` varchar(50) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `tickets` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `creation_date` datetime DEFAULT (current_timestamp()),
  `updated_at` datetime,
  `deleted_at` datetime,
  `id_status` int NOT NULL DEFAULT 1,
  `id_priority` int NOT NULL DEFAULT 1,
  `id_category` int NOT NULL,
  `id_assigned_user` int,
  `closed_at` datetime
);

CREATE TABLE `ticket_values` (
  `id_ticket` int NOT NULL,
  `id_ticket_field` int NOT NULL,
  `value` text,
  PRIMARY KEY (`id_ticket`, `id_ticket_field`)
);

CREATE TABLE `interactions` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_ticket` int NOT NULL,
  `id_user` int NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT (current_timestamp()),
  `updated_at` datetime,
  `is_internal` tinyint(1) DEFAULT 0
);

CREATE TABLE `ticket_history` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_ticket` int NOT NULL,
  `event` varchar(512) NOT NULL,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `attachments` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_ticket` int,
  `id_interaction` int,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50),
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `ticket_reports` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `report_name` varchar(100) NOT NULL,
  `filters` text,
  `generated_by` int,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `ticket_metrics` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_ticket` int NOT NULL,
  `response_time` int,
  `resolution_time` int,
  `reopens` int,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `sla_policies` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(255),
  `response_time_limit` int,
  `resolution_time_limit` int,
  `created_at` datetime DEFAULT (current_timestamp()),
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `notifications` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `url` varchar(255),
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT (current_timestamp())
);

CREATE TABLE `settings` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `key` varchar(100) UNIQUE NOT NULL,
  `value` text,
  `description` varchar(255),
  `updated_at` datetime
);

CREATE TABLE `api_keys` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `api_key` varchar(255) UNIQUE NOT NULL,
  `created_by` int,
  `created_at` datetime DEFAULT (current_timestamp()),
  `expires_at` datetime,
  `is_active` tinyint(1) DEFAULT 1
);

CREATE TABLE `webhooks` (
  `id` int PRIMARY KEY NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `event` varchar(100) NOT NULL,
  `secret` varchar(255),
  `created_by` int,
  `created_at` datetime DEFAULT (current_timestamp()),
  `is_active` tinyint(1) DEFAULT 1
);

CREATE INDEX `users_index_0` ON `users` (`email`);

CREATE INDEX `users_index_1` ON `users` (`username`);

CREATE INDEX `tickets_index_2` ON `tickets` (`id_status`);

CREATE INDEX `tickets_index_3` ON `tickets` (`id_priority`);

CREATE INDEX `tickets_index_4` ON `tickets` (`id_category`);

CREATE INDEX `tickets_index_5` ON `tickets` (`id_assigned_user`);

CREATE INDEX `tickets_index_6` ON `tickets` (`creation_date`);

ALTER TABLE `categories` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `roles` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `logs` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `terminals` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `ticket_fields` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`id_client`) REFERENCES `users` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`id_assigned_user`) REFERENCES `users` (`id`);

ALTER TABLE `notifications` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `api_keys` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `webhooks` ADD FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

ALTER TABLE `user_sessions` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `ticket_reports` ADD FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`);

ALTER TABLE `user_roles` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `user_roles` ADD FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`);

ALTER TABLE `permissions` ADD FOREIGN KEY (`id_module`) REFERENCES `modules` (`id`);

ALTER TABLE `role_permissions` ADD FOREIGN KEY (`id_role`) REFERENCES `roles` (`id`);

ALTER TABLE `role_permissions` ADD FOREIGN KEY (`id_permission`) REFERENCES `permissions` (`id`);

ALTER TABLE `terminal_categories` ADD FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`);

ALTER TABLE `terminal_categories` ADD FOREIGN KEY (`id_terminal`) REFERENCES `terminals` (`id`);

ALTER TABLE `group_users` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `group_users` ADD FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`);

ALTER TABLE `terminal_groups` ADD FOREIGN KEY (`id_terminal`) REFERENCES `terminals` (`id`);

ALTER TABLE `terminal_groups` ADD FOREIGN KEY (`id_group`) REFERENCES `groups` (`id`);

ALTER TABLE `ticket_fields` ADD FOREIGN KEY (`type`) REFERENCES `field_types` (`id`);

ALTER TABLE `ticket_fields` ADD FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`id_status`) REFERENCES `ticket_status` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`id_priority`) REFERENCES `ticket_priority` (`id`);

ALTER TABLE `tickets` ADD FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`);

ALTER TABLE `ticket_values` ADD FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`);

ALTER TABLE `ticket_values` ADD FOREIGN KEY (`id_ticket_field`) REFERENCES `ticket_fields` (`id`);

ALTER TABLE `interactions` ADD FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`);

ALTER TABLE `interactions` ADD FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

ALTER TABLE `ticket_history` ADD FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`);

ALTER TABLE `attachments` ADD FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`);

ALTER TABLE `attachments` ADD FOREIGN KEY (`id_interaction`) REFERENCES `interactions` (`id`);

ALTER TABLE `ticket_metrics` ADD FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`);
