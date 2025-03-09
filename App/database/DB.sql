-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-03-2025 a las 23:24:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bookify`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asesor_terminals`
--

CREATE TABLE `asesor_terminals` (
  `id_asesor` int(11) NOT NULL,
  `id_terminal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `custom_ticket_fields`
--

CREATE TABLE `custom_ticket_fields` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `custom_field` varchar(100) NOT NULL,
  `enum_type` enum('TEXT','NUMBER','DATE','BOOLEAN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `interactions`
--

CREATE TABLE `interactions` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `message` text NOT NULL,
  `interaction_date` datetime DEFAULT current_timestamp(),
  `type` enum('COMMENT','INTERN') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` text NOT NULL,
  `id_user` int(11) DEFAULT 1,
  `log_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id_rol` int(11) NOT NULL,
  `id_permission` int(11) NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_history`
--

CREATE TABLE `status_history` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `last_status` enum('NEW','IN_PROGRESS','CLOSED') NOT NULL,
  `new_status` enum('NEW','IN_PROGRESS','CLOSED') NOT NULL,
  `update_date` datetime DEFAULT current_timestamp(),
  `id_asesor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `terminals`
--

CREATE TABLE `terminals` (
  `id` int(11) NOT NULL,
  `terminal_ext` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `status` enum('NEW','IN_PROGRESS','CLOSED') NOT NULL DEFAULT 'NEW',
  `priority` enum('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
  `id_category` int(11) NOT NULL,
  `id_asesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ticket_custom_values`
--

CREATE TABLE `ticket_custom_values` (
  `id` int(11) NOT NULL,
  `custom_field_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `rol` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `worklog`
--

CREATE TABLE `worklog` (
  `id` int(11) NOT NULL,
  `id_ticket` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `work_description` text NOT NULL,
  `log_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asesor_terminals`
--
ALTER TABLE `asesor_terminals`
  ADD UNIQUE KEY `id_asesor` (`id_asesor`,`id_terminal`),
  ADD KEY `id_terminal` (`id_terminal`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `custom_ticket_fields`
--
ALTER TABLE `custom_ticket_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indices de la tabla `interactions`
--
ALTER TABLE `interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indices de la tabla `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id_rol`,`id_permission`),
  ADD KEY `permission_id` (`id_permission`);

--
-- Indices de la tabla `status_history`
--
ALTER TABLE `status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_asesor` (`id_asesor`);

--
-- Indices de la tabla `terminals`
--
ALTER TABLE `terminals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `terminal_ext` (`terminal_ext`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_category` (`id_category`),
  ADD KEY `id_asesor` (`id_asesor`);

--
-- Indices de la tabla `ticket_custom_values`
--
ALTER TABLE `ticket_custom_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `custom_field_id` (`custom_field_id`),
  ADD KEY `ticket_id` (`ticket_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `worklog`
--
ALTER TABLE `worklog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_ticket` (`id_ticket`),
  ADD KEY `id_user` (`id_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `custom_ticket_fields`
--
ALTER TABLE `custom_ticket_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `interactions`
--
ALTER TABLE `interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `terminals`
--
ALTER TABLE `terminals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ticket_custom_values`
--
ALTER TABLE `ticket_custom_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `worklog`
--
ALTER TABLE `worklog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asesor_terminals`
--
ALTER TABLE `asesor_terminals`
  ADD CONSTRAINT `asesor_terminals_ibfk_1` FOREIGN KEY (`id_asesor`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `asesor_terminals_ibfk_2` FOREIGN KEY (`id_terminal`) REFERENCES `terminals` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `custom_ticket_fields`
--
ALTER TABLE `custom_ticket_fields`
  ADD CONSTRAINT `custom_ticket_fields_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `interactions`
--
ALTER TABLE `interactions`
  ADD CONSTRAINT `interactions_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interactions_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`id_permission`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `status_history`
--
ALTER TABLE `status_history`
  ADD CONSTRAINT `status_history_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `status_history_ibfk_2` FOREIGN KEY (`id_asesor`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`id_category`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`id_asesor`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `ticket_custom_values`
--
ALTER TABLE `ticket_custom_values`
  ADD CONSTRAINT `ticket_custom_values_ibfk_1` FOREIGN KEY (`custom_field_id`) REFERENCES `custom_ticket_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ticket_custom_values_ibfk_2` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `worklog`
--
ALTER TABLE `worklog`
  ADD CONSTRAINT `worklog_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `worklog_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;


-- Insertar el rol de Administrador si no existe
INSERT INTO roles (name) VALUES ('Admin'),('Dummy')
ON DUPLICATE KEY UPDATE id=id;

-- Obtener el ID del rol Admin
SET @admin_role_id = (SELECT id FROM roles WHERE name='Admin' LIMIT 1);
SET @dummy_role_id = (SELECT id FROM roles WHERE name='Dummy' LIMIT 1);

-- Insertar el usuario administrador si no existe
INSERT INTO users (name, username, password, phone, rol, is_active) 
VALUES ('root', 'root', '$2a$12$cjBqcM4DP3CZWv9bMBnIpOwcJTsC058bIe/5zqVWZD6xqoqCMusf.', '0', @admin_role_id, 1)
ON DUPLICATE KEY UPDATE password=VALUES(password), phone=VALUES(phone), rol=VALUES(rol), is_active=VALUES(is_active);

-- Insertar el permiso ALL si no existe
INSERT INTO permissions (name, description) 
VALUES ('ALL', 'TOTAL CONTROL')
ON DUPLICATE KEY UPDATE description=VALUES(description);

-- Obtener el ID del permiso ALL
SET @all_permission_id = (SELECT id FROM permissions WHERE name='ALL' LIMIT 1);

-- Asignar el permiso ALL al rol Admin y Dummy
INSERT INTO role_permissions (id_rol, id_permission, is_allowed) 
VALUES (@admin_role_id, @all_permission_id, 1), 
       (@dummy_role_id, @all_permission_id, 0)
ON DUPLICATE KEY UPDATE is_allowed=1;



-- Permisos 
INSERT INTO permissions (name, description) VALUES
    -- CATEGORY
    ('CATEGORY.INDEX', 'VIEW CATEGORY LIST'),
    ('CATEGORY.SHOW', 'VIEW CATEGORY BY ID'),
    ('CATEGORY.STORE', 'CREATE CATEGORY'),
    ('CATEGORY.UPDATE', 'UPDATE CATEGORY'),
    ('CATEGORY.DESTROY', 'DELETE CATEGORY BY ID'),
    -- CUSTOM TICKET FIELD
    ('CTFIELDS.INDEX', 'VIEW CUSTOM TICKET FIELD LIST'),
    ('CTFIELDS.SHOW', 'VIEW CUSTOM TICKET FIELD BY ID'),
    ('CTFIELDS.STORE', 'CREATE CUSTOM TICKET FIELD'),
    ('CTFIELDS.UPDATE', 'UPDATE CUSTOM TICKET FIELD'),
    ('CTFIELDS.DESTROY', 'DELETE CUSTOM TICKET FIELD BY ID'),
    ('CTFIELDS.GETFIELDSBYCATEGORY', 'VIEW CUSTOM TICKET FIELDS BY CATEGORY LIST'),
    -- INTERACTION
    ('INTERACTION.INDEX', 'VIEW INTERACTION LIST'),
    ('INTERACTION.SHOW', 'VIEW INTERACTION BY ID'),
    ('INTERACTION.STORE', 'CREATE INTERACTION'),
    ('INTERACTION.UPDATE', 'UPDATE INTERACTION'),
    ('INTERACTION.DESTROY', 'DELETE INTERACTION BY ID'),
    ('INTERACTION.GETINTERACTIONSBYTICKET', 'VIEW INTERACTION BY TICKET LIST'),
    -- LOG
    ('LOG.INDEX', 'VIEW LOG LIST'),
    ('LOG.SHOW', 'VIEW LOG BY ID'),
    ('LOG.STORE', 'CREATE LOG'),
    ('LOG.CLEAR', 'CLEAR ALL LOGS'),
    -- ROLE
    ('ROL.INDEX', 'VIEW ROLE LIST'),
    ('ROL.SHOW', 'VIEW ROLE BY ID'),
    ('ROL.STORE', 'CREATE ROLE'),
    ('ROL.UPDATE', 'UPDATE ROLE'),
    ('ROL.DESTROY', 'DELETE ROLE BY ID'),
    -- PERMISSIONS
    ('PERMISSIONS.INDEX', 'VIEW PERMISSIONS LIST'),
    ('PERMISSIONS.SHOW', 'VIEW PERMISSIONS BY ID'),
    ('PERMISSIONS.STORE', 'CREATE PERMISSIONS'),
    ('PERMISSIONS.UPDATE', 'UPDATE PERMISSIONS'),
    ('PERMISSIONS.DESTROY', 'DELETE PERMISSIONS BY ID'),
    ('PERMISSIONS.GETASSIGNMENTS', 'VIEW PERMISSION ASSIGNMENTS'),
    ('PERMISSIONS.ASSIGN', 'ASSIGN PERMISSIONS TO A ROLE'),
    ('PERMISSIONS.UNASSIGN', 'UNASSIGN PERMISSIONS TO A ROLE'),
    -- STATUS TICKET HISTORY
    ('STHISTORY.INDEX', 'VIEW STATUS TICKET HISTORY LIST'),
    ('STHISTORY.SHOW', 'VIEW STATUS TICKET HISTORY BY ID'),
    ('STHISTORY.STORE', 'CREATE STATUS TICKET HISTORY'),
    ('STHISTORY.CLEAR', 'CLEAR ALL STATUS TICKET HISTORIES'),
    ('STHISTORY.GETBYTICKET', 'VIEW ALL STATUS TICKET HISTORY BY TICKET'),
    -- TERMINAL
    ('TERMINAL.INDEX', 'VIEW TERMINAL LIST'),
    ('TERMINAL.SHOW', 'VIEW TERMINAL BY ID'),
    ('TERMINAL.STORE', 'CREATE TERMINAL'),
    ('TERMINAL.UPDATE', 'UPDATE TERMINAL'),
    ('TERMINAL.DESTROY', 'DELETE TERMINAL BY ID'),
    ('TERMINAL.GETASSIGNMENTS', 'VIEW TERMINAL ASSIGNMENTS'),
    ('TERMINAL.ASSIGN', 'ASSIGN TERMINAL TO AN ADVISOR'),
    ('TERMINAL.UNASSIGN', 'UNASSIGN TERMINAL FROM AN ADVISOR'),
    -- TICKET
    ('TICKET.INDEX', 'VIEW TICKET LIST'),
    ('TICKET.SHOW', 'VIEW TICKET BY ID'),
    ('TICKET.STORE', 'CREATE TICKET'),
    ('TICKET.UPDATE', 'UPDATE TICKET'),
    ('TICKET.DESTROY', 'DELETE TICKET BY ID'),
    ('TICKET.GETBYCATEGORY', 'VIEW ALL TICKETS BY CATEGORY'),
    ('TICKET.GETBYSTATUS', 'VIEW ALL TICKETS BY STATUS'),
    ('TICKET.GETBYPRIORITY', 'VIEW ALL TICKETS BY PRIORITY'),
    ('TICKET.GETBYADVISOR', 'VIEW ALL TICKETS BY ADVISOR'),
    ('TICKET.GETBYCLIENT', 'VIEW ALL TICKETS BY CLIENT'),
    ('TICKET.ASSIGN', 'ASSIGN TICKET TO AN ADVISOR'),
    -- UPLOADS
    ('UPLOAD.FILES', 'UPLOAD FILES TO SERVER'),
    -- USER
    ('USER.INDEX', 'VIEW USER LIST'),
    ('USER.SHOW', 'VIEW USER BY ID'),
    ('USER.STORE', 'CREATE USER'),
    ('USER.UPDATE', 'UPDATE USER'),
    ('USER.DESTROY', 'DELETE USER BY ID'),
    ('USER.ACTIVE', 'ACTIVATE USER BY ID'),
    ('USER.INACTIVE', 'DEACTIVATE USER BY ID'),
    -- WORKLOG
    ('WORKLOG.INDEX', 'VIEW WORKLOG LIST'),
    ('WORKLOG.SHOW', 'VIEW WORKLOG BY ID'),
    ('WORKLOG.STORE', 'CREATE WORKLOG'),
    ('WORKLOG.UPDATE', 'UPDATE WORKLOG'),
    ('WORKLOG.DESTROY', 'DELETE WORKLOG BY ID'),
    ('WORKLOG.GETBYTICKET', 'VIEW ALL WORKLOG BY TICKET'),
    ('WORKLOG.GETBYUSER', 'VIEW ALL WORKLOG BY USER')
ON DUPLICATE KEY UPDATE description = VALUES(description);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
