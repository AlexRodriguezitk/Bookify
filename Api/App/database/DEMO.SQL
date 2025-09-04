

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

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Soporte Técnico'),
(2, 'Solicitudes de Características'),
(3, 'Problemas de Facturación'),
(4, 'Preguntas Generales'),
(5, 'Reporte de Bug');

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

--
-- Volcado de datos para la tabla `custom_ticket_fields`
--

INSERT INTO `custom_ticket_fields` (`id`, `category_id`, `custom_field`, `enum_type`) VALUES
(1, 1, 'Tipo de problema', 'TEXT'),
(2, 1, 'Sistema operativo', 'TEXT'),
(3, 1, 'Versión del software', 'TEXT'),
(4, 1, '¿Afecta a un solo usuario? ', 'BOOLEAN'),
(5, 1, 'ID del usuario', 'NUMBER'),
(6, 1, 'Fecha de ocurrencia', 'DATE'),
(7, 2, 'Impacto esperado', 'TEXT'),
(8, 2, '¿Es una característica relacionada con la seguridad?', 'BOOLEAN'),
(9, 2, 'Fecha de solicitud', 'DATE'),
(10, 3, 'Número de factura', 'NUMBER'),
(11, 3, 'Tipo de problema', 'TEXT'),
(12, 3, 'Fecha del cobro', 'DATE'),
(13, 3, 'Monto del cobro', 'NUMBER'),
(14, 3, 'ID de la transacción', 'NUMBER'),
(15, 4, 'Tema de la consulta', 'TEXT'),
(16, 4, 'Departamento', 'TEXT'),
(17, 4, '¿Requiere una respuesta urgente?', 'BOOLEAN'),
(18, 4, 'Fecha de la consulta', 'DATE'),
(19, 5, 'Pasos para reproducir', 'TEXT'),
(20, 5, 'Comportamiento esperado', 'TEXT'),
(21, 5, 'Comportamiento actual', 'TEXT'),
(22, 5, 'Fecha en que se encontró el bug', 'DATE'),
(23, 5, 'Navegador', 'TEXT'),
(24, 5, 'Versión del sistema operativo', 'TEXT'),
(25, 5, '¿Es un bug crítico?', 'BOOLEAN');

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

--
-- Volcado de datos para la tabla `interactions`
--

INSERT INTO `interactions` (`id`, `id_ticket`, `id_user`, `message`, `interaction_date`, `type`) VALUES
(1, 1, 21, 'No puedo iniciar sesión, me da error de credenciales.', '2025-09-04 14:43:12', 'COMMENT'),
(2, 1, 10, 'Hola Ana 👋, ¿puedes confirmar si ya intentaste restablecer la contraseña?', '2025-09-04 14:43:12', 'COMMENT'),
(3, 1, 21, 'Sí, pero al ingresar la nueva clave sigue diciendo que es incorrecta.', '2025-09-04 14:43:12', 'COMMENT'),
(4, 1, 10, 'Entendido, reviso en el sistema si tu cuenta aparece bloqueada.', '2025-09-04 14:43:12', 'COMMENT'),
(5, 1, 21, 'Perfecto, quedo pendiente.', '2025-09-04 14:43:12', 'COMMENT'),
(6, 1, 10, 'Tu cuenta está activa, pero veo intentos fallidos recientes. Procedo a desbloquearla.', '2025-09-04 14:43:12', 'COMMENT'),
(7, 1, 21, 'Gracias, ¿me envías un enlace para probar de nuevo?', '2025-09-04 14:43:12', 'COMMENT'),
(8, 1, 10, 'Listo 🚀, te envié un enlace temporal válido por 24h.', '2025-09-04 14:43:12', 'COMMENT'),
(9, 1, 21, 'Ahora sí pude ingresar correctamente.', '2025-09-04 14:43:12', 'COMMENT'),
(10, 1, 10, 'Excelente, cerramos las sesiones previas para mayor seguridad ✔️.', '2025-09-04 14:43:12', 'COMMENT'),
(11, 1, 10, 'El cliente no puede iniciar sesión, la cuenta aparece con bloqueos.', '2025-09-04 14:43:12', 'INTERN'),
(12, 1, 5, 'Procede a desbloquear y genera enlace temporal. Confirma al cliente.', '2025-09-04 14:43:12', 'INTERN'),
(13, 1, 10, 'Hecho, envié enlace temporal y el cliente ya accedió.', '2025-09-04 14:43:12', 'INTERN'),
(14, 1, 5, 'Perfecto, documenta el cierre y asegura que no haya más bloqueos.', '2025-09-04 14:43:12', 'INTERN'),
(15, 1, 10, 'Confirmado, cierro el ticket como resuelto.', '2025-09-04 14:43:12', 'INTERN'),
(16, 2, 22, 'Recibí una factura con un valor que no corresponde.', '2025-09-04 14:43:12', 'COMMENT'),
(17, 2, 11, 'Hola, ¿puedes indicarnos el número de factura?', '2025-09-04 14:43:12', 'COMMENT'),
(18, 2, 22, 'Claro, es la F12345.', '2025-09-04 14:43:12', 'COMMENT'),
(19, 2, 11, 'Revisando… parece que hay un cobro duplicado.', '2025-09-04 14:43:12', 'COMMENT'),
(20, 2, 22, 'Eso mismo noté.', '2025-09-04 14:43:12', 'COMMENT'),
(21, 2, 11, 'Escalo al área contable para confirmación.', '2025-09-04 14:43:12', 'INTERN'),
(22, 2, 6, 'Confirma que eliminen el cobro duplicado y genera nota crédito.', '2025-09-04 14:43:12', 'INTERN'),
(23, 2, 11, 'Ya gestioné la nota crédito, en 24h tendrás la factura corregida.', '2025-09-04 14:43:12', 'COMMENT'),
(24, 2, 22, 'Perfecto, muchas gracias.', '2025-09-04 14:43:12', 'COMMENT'),
(25, 2, 11, 'Un placer ayudarte, quedo atento si necesitas algo más.', '2025-09-04 14:43:12', 'COMMENT'),
(26, 2, 11, 'Cliente afectado por error de facturación, ya corregido.', '2025-09-04 14:43:12', 'INTERN'),
(27, 2, 6, 'Bien, recuerda registrar el caso como incidencia contable.', '2025-09-04 14:43:12', 'INTERN'),
(28, 2, 11, 'Anotado en el sistema.', '2025-09-04 14:43:12', 'INTERN'),
(29, 2, 6, 'Correcto, se puede cerrar.', '2025-09-04 14:43:12', 'INTERN'),
(30, 2, 11, 'Ticket cerrado como resuelto.', '2025-09-04 14:43:12', 'INTERN'),
(31, 3, 23, 'Intento generar un reporte y aparece pantalla en blanco.', '2025-09-04 14:43:12', 'COMMENT'),
(32, 3, 12, 'Hola 👋, ¿qué tipo de reporte intentas sacar?', '2025-09-04 14:43:12', 'COMMENT'),
(33, 3, 23, 'Reporte de ventas mensual.', '2025-09-04 14:43:12', 'COMMENT'),
(34, 3, 12, 'Verifico en logs del sistema.', '2025-09-04 14:43:12', 'COMMENT'),
(35, 3, 23, 'Gracias.', '2025-09-04 14:43:12', 'COMMENT'),
(36, 3, 12, 'El error es por exceso de datos, sugiero filtrar por rango.', '2025-09-04 14:43:12', 'COMMENT'),
(37, 3, 23, 'Lo pruebo ahora.', '2025-09-04 14:43:12', 'COMMENT'),
(38, 3, 23, 'Sí, así funcionó con rango menor.', '2025-09-04 14:43:12', 'COMMENT'),
(39, 3, 12, 'Genial, vamos a mejorar el rendimiento.', '2025-09-04 14:43:12', 'COMMENT'),
(40, 3, 23, 'Perfecto, gracias.', '2025-09-04 14:43:12', 'COMMENT'),
(41, 3, 12, 'El reporte se bloquea por exceso de datos, ¿autorizamos optimización?', '2025-09-04 14:43:12', 'INTERN'),
(42, 3, 7, 'Sí, aplica paginación de registros y avisa al cliente.', '2025-09-04 14:43:12', 'INTERN'),
(43, 3, 12, 'Implementado en ambiente de prueba.', '2025-09-04 14:43:12', 'INTERN'),
(44, 3, 7, 'Confirma en producción y cierra.', '2025-09-04 14:43:12', 'INTERN'),
(45, 3, 12, 'Cerrado con mejoras aplicadas.', '2025-09-04 14:43:12', 'INTERN'),
(46, 4, 24, 'No recibo el correo de confirmación al registrarme.', '2025-09-04 14:43:12', 'COMMENT'),
(47, 4, 13, 'Hola, revisa tu carpeta de spam.', '2025-09-04 14:43:12', 'COMMENT'),
(48, 4, 24, 'Ya revisé y no aparece.', '2025-09-04 14:43:12', 'COMMENT'),
(49, 4, 13, 'Verifico si tu correo quedó mal registrado.', '2025-09-04 14:43:12', 'COMMENT'),
(50, 4, 24, 'El correo es cliente24@mail.com.', '2025-09-04 14:43:12', 'COMMENT'),
(51, 4, 13, 'Lo encontré con un error tipográfico, ya lo corregí.', '2025-09-04 14:43:12', 'COMMENT'),
(52, 4, 24, 'Ahora sí me llegó, gracias.', '2025-09-04 14:43:12', 'COMMENT'),
(53, 4, 13, 'Perfecto, tu cuenta ya está activa.', '2025-09-04 14:43:12', 'COMMENT'),
(54, 4, 24, 'Genial, todo bien.', '2025-09-04 14:43:12', 'COMMENT'),
(55, 4, 13, 'Un gusto ayudarte.', '2025-09-04 14:43:12', 'COMMENT'),
(56, 4, 13, 'Correo mal ingresado, corregido.', '2025-09-04 14:43:12', 'INTERN'),
(57, 4, 8, 'Correcto, registra el caso como error de usuario.', '2025-09-04 14:43:12', 'INTERN'),
(58, 4, 13, 'Ya lo documenté.', '2025-09-04 14:43:12', 'INTERN'),
(59, 4, 8, 'Ok, puedes cerrar.', '2025-09-04 14:43:12', 'INTERN'),
(60, 4, 13, 'Ticket cerrado.', '2025-09-04 14:43:12', 'INTERN'),
(76, 21, 11, '<b>¡Hola!</b><br>Espero que te encuentres muy bien. 🙌<br>¿En que te puedo ayudar el día de hoy?', '2025-09-04 15:07:55', 'COMMENT'),
(77, 21, 21, 'Hola, si...<br><br>Como comente en la descripción del caso, al momento de querer cambiar mi foto de perfil y darle al botón de guardar me salta un error.', '2025-09-04 15:09:05', 'COMMENT'),
(78, 21, 11, '¿Podría compartirme el código de error por favor?', '2025-09-04 15:09:33', 'COMMENT'),
(79, 21, 21, 'Claro! mira es este: <i><font color=\"#ff0000\">#E:00x01239E9</font></i>', '2025-09-04 15:09:57', 'COMMENT'),
(80, 21, 11, 'Ya veo, dame un momento verifico en el sistema.', '2025-09-04 15:10:10', 'COMMENT'),
(81, 21, 21, 'Dale! 👌👌', '2025-09-04 15:10:32', 'COMMENT'),
(82, 71, 11, 'Hola, Isabel.<br><br>¿Cómo estas? ¿En que te puedo ayudar el día de hoy?', '2025-09-04 15:11:02', 'COMMENT'),
(83, 46, 11, 'Hola Javier!,<br>¿En que te puedo ayudar el día de hoy?', '2025-09-04 15:11:36', 'COMMENT'),
(84, 2, 6, 'Perfecto, ya lo marco como resuelto. !Buen trabajo!', '2025-09-04 15:13:09', 'INTERN'),
(85, 21, 11, 'Hola, ya revise tu caso.&nbsp;<br>He aplicado las respectivas correcciones, ¿podrías verificar si en tu perfil ya puedes actualizar la información?', '2025-09-04 15:14:36', 'COMMENT'),
(86, 21, 21, 'Perfecto, muchas gracias.<br>Si, ya funciona. ❤️😊<br>¡Son unos cracks!', '2025-09-04 15:15:50', 'COMMENT'),
(87, 21, 11, 'Hola SUP,<br>¿Podrías ayudarme a cerrar este caso?', '2025-09-04 15:16:15', 'INTERN'),
(88, 11, 11, 'Hola Laura,<br>¿En que te puedo colaborar?', '2025-09-04 15:16:43', 'COMMENT'),
(89, 27, 11, 'Hola Isabel,<br><br>¿En que te puedo ayudar?', '2025-09-04 15:17:10', 'COMMENT'),
(90, 21, 9, 'Hola, Claro!<br>Listo para el cierre!', '2025-09-04 15:18:00', 'INTERN'),
(91, 18, 16, 'Hola Héctor,&nbsp;<br>¿En que te puedo colaborar el día de hoy?', '2025-09-04 15:19:20', 'COMMENT'),
(92, 18, 38, 'Hola!<br>Si mira, quería preguntar si Bookify es integrable con mi sistema POS.<br><br>Para poder realizar pedidos a través de la aplicación.', '2025-09-04 15:20:38', 'COMMENT'),
(93, 18, 16, 'Si, mira. Debes contactarte a través del siguiente numero. 3775775771<br><br>Allí podrás solicitar el servicio de soporte para la integración del sistema con tu sistema POS.<br>Debes contar con una mínima documentación.<br><br>Veo que también estas interesado en integración con un CRM. ¿También necesitas ayuda con eso?', '2025-09-04 15:22:14', 'COMMENT'),
(94, 18, 38, 'Hola, si.<br>¿Mas o menos que costo podría llegar a tener esta implementación?', '2025-09-04 15:22:40', 'COMMENT'),
(95, 18, 16, 'Eso depende.<br>Pero manejamos planes muy accesibles y varia según la dificultad de la integración.', '2025-09-04 15:23:10', 'COMMENT'),
(96, 18, 7, 'Puedes encontrar nuestros planes a través del siguiente url:&nbsp;<a href=\"https://www.bookifyCX.com\" target=\"_blank\" rel=\"noopener noreferrer\">Bookify Planes</a>', '2025-09-04 15:24:46', 'COMMENT'),
(97, 18, 7, 'Hola Jimmy,<br><br>Recuerda siempre redirigir al usuario al sistema de compras integrado en lugar de al equipo de ventas directamente.<br>Ahora mismo no tienen capacidad para atender a nuevos usuarios, hay mucha demanda de Bookify en el mercado.', '2025-09-04 15:25:58', 'INTERN'),
(98, 18, 38, 'Ohh, perfecto.<br>Muchas gracias. Eso seria todo.<br>Muchas gracias!', '2025-09-04 15:26:33', 'COMMENT'),
(99, 18, 16, 'A ti por preferirnos.<br>Dejo cerrado el caso.<br><br>Hasta pronto! 🤓', '2025-09-04 15:27:20', 'COMMENT'),
(100, 18, 16, 'Listo SUP,', '2025-09-04 15:27:32', 'INTERN'),
(101, 18, 16, 'Muchas gracias!<br>¿Podría ayudarme con el cierre del caso?', '2025-09-04 15:27:47', 'INTERN'),
(102, 50, 16, 'Hola. SUP, mira que aquí tenemos otro caso de permisos en el perfil.<br>¿Le mando el manual de requerimientos directamente?', '2025-09-04 15:28:31', 'INTERN'),
(103, 18, 7, 'Claro.', '2025-09-04 15:28:37', 'INTERN'),
(104, 50, 7, 'No!<br>Jimmy, recuerda siempre que debes hacer el debido proceso con cada cliente.<br><br>Sin importar que sepamos que es el mismo caso. Se que puede ser un reproceso pero esto nos ayuda a que si es un problema nuevo, no genere tanto ruido e impacto.', '2025-09-04 15:29:45', 'INTERN'),
(105, 50, 7, 'Mejor asegurarnos que dar una solución que no sirva y nos reabran el caso.', '2025-09-04 15:29:59', 'INTERN'),
(106, 50, 16, 'Vale mi SUP.<br>Entonces ya hago el proceso.', '2025-09-04 15:30:10', 'INTERN'),
(107, 50, 16, 'Hola Mario,<br>¿En que puedo ayudarte el día de hoy¡', '2025-09-04 15:30:23', 'COMMENT'),
(108, 9, 6, 'Hola, Saúl.<br><br>Veo que tienes la cola de tickets llena. Voy a transferirle este ticket, a María.', '2025-09-04 15:36:48', 'INTERN'),
(109, 9, 13, 'Dale, dale UP!', '2025-09-04 15:37:44', 'INTERN');

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

--
-- Volcado de datos para la tabla `logs`
--

INSERT INTO `logs` (`id`, `action`, `details`, `id_user`, `log_date`) VALUES
(1, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: root', 1, '2025-09-04 13:33:27'),
(2, 'ROL_CREATED', 'ROL WAS CREATED SUCCESSFULLY: Supervisor', 1, '2025-09-04 13:33:49'),
(3, 'ROL_CREATED', 'ROL WAS CREATED SUCCESSFULLY: Agent', 1, '2025-09-04 13:33:55'),
(4, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CATEGORY.READ} ROL{Client}', 1, '2025-09-04 13:34:00'),
(5, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CTFIELDS.READ} ROL{Client}', 1, '2025-09-04 13:34:04'),
(6, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.READ} ROL{Client}', 1, '2025-09-04 13:34:08'),
(7, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.CREATE} ROL{Client}', 1, '2025-09-04 13:34:09'),
(8, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{ROLE.READ} ROL{Client}', 1, '2025-09-04 13:34:19'),
(9, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PERMISSION.READ} ROL{Client}', 1, '2025-09-04 13:34:21'),
(10, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TERMINAL.READ} ROL{Client}', 1, '2025-09-04 13:34:27'),
(11, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.READ} ROL{Client}', 1, '2025-09-04 13:34:29'),
(12, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.CREATE} ROL{Client}', 1, '2025-09-04 13:34:30'),
(13, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.UPLOAD} ROL{Client}', 1, '2025-09-04 13:34:40'),
(14, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.READ} ROL{Client}', 1, '2025-09-04 13:34:41'),
(15, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.READ} ROL{Client}', 1, '2025-09-04 13:34:43'),
(16, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.2FA} ROL{Client}', 1, '2025-09-04 13:34:45'),
(17, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.READ} ROL{Client}', 1, '2025-09-04 13:34:49'),
(18, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{WORKLOG.CREATE} ROL{Client}', 1, '2025-09-04 13:34:50'),
(19, 'PERMISSION_UNASSIGNED', 'PERMISSION WAS UNASSIGNED SUCCESSFULLY: PERMISSION{WORKLOG.CREATE} ROL{Client}', 1, '2025-09-04 13:34:50'),
(20, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.UPDATE} ROL{Client}', 1, '2025-09-04 13:34:51'),
(21, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{DASHBOARD.VIEW} ROL{Client}', 1, '2025-09-04 13:35:07'),
(22, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CATEGORY.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:16'),
(23, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CTFIELDS.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:19'),
(24, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:22'),
(25, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.CREATE} ROL{Supervisor}', 1, '2025-09-04 13:35:24'),
(26, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{ROLE.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:31'),
(27, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PERMISSION.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:34'),
(28, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TERMINAL.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:40'),
(29, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.CREATE} ROL{Supervisor}', 1, '2025-09-04 13:35:48'),
(30, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.READ} ROL{Supervisor}', 1, '2025-09-04 13:35:48'),
(31, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.UPDATE} ROL{Supervisor}', 1, '2025-09-04 13:35:50'),
(32, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.INTERN} ROL{Supervisor}', 1, '2025-09-04 13:35:56'),
(33, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.TRANSFER} ROL{Supervisor}', 1, '2025-09-04 13:36:05'),
(34, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.RESOLVE} ROL{Supervisor}', 1, '2025-09-04 13:36:08'),
(35, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.WORKLOG} ROL{Supervisor}', 1, '2025-09-04 13:36:09'),
(36, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.UPLOAD} ROL{Supervisor}', 1, '2025-09-04 13:36:11'),
(37, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.READ} ROL{Supervisor}', 1, '2025-09-04 13:36:12'),
(38, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.READ} ROL{Supervisor}', 1, '2025-09-04 13:36:14'),
(39, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.2FA} ROL{Supervisor}', 1, '2025-09-04 13:36:19'),
(40, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.READ} ROL{Supervisor}', 1, '2025-09-04 13:36:21'),
(41, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.UPDATE} ROL{Supervisor}', 1, '2025-09-04 13:36:21'),
(42, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKETS.VIEW} ROL{Supervisor}', 1, '2025-09-04 13:36:25'),
(43, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{DASHBOARD.VIEW} ROL{Supervisor}', 1, '2025-09-04 13:36:26'),
(44, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USERS.VIEW} ROL{Supervisor}', 1, '2025-09-04 13:36:36'),
(45, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CATEGORY.READ} ROL{Agent}', 1, '2025-09-04 13:37:04'),
(46, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{CTFIELDS.READ} ROL{Agent}', 1, '2025-09-04 13:37:06'),
(47, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.READ} ROL{Agent}', 1, '2025-09-04 13:37:09'),
(48, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{INTERACTION.CREATE} ROL{Agent}', 1, '2025-09-04 13:37:10'),
(49, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{ROLE.READ} ROL{Agent}', 1, '2025-09-04 13:37:14'),
(50, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PERMISSION.READ} ROL{Agent}', 1, '2025-09-04 13:37:17'),
(51, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{STATUS_HISTORY.READ} ROL{Agent}', 1, '2025-09-04 13:37:21'),
(52, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TERMINAL.READ} ROL{Agent}', 1, '2025-09-04 13:37:23'),
(53, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.READ} ROL{Agent}', 1, '2025-09-04 13:37:25'),
(54, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.CREATE} ROL{Agent}', 1, '2025-09-04 13:37:25'),
(55, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.UPDATE} ROL{Agent}', 1, '2025-09-04 13:37:26'),
(56, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKET.INTERN} ROL{Agent}', 1, '2025-09-04 13:37:31'),
(57, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.UPLOAD} ROL{Agent}', 1, '2025-09-04 13:37:37'),
(58, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{UPLOADS.READ} ROL{Agent}', 1, '2025-09-04 13:37:38'),
(59, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.READ} ROL{Agent}', 1, '2025-09-04 13:37:40'),
(60, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{USER.2FA} ROL{Agent}', 1, '2025-09-04 13:37:50'),
(61, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.READ} ROL{Agent}', 1, '2025-09-04 13:37:52'),
(62, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.UPDATE} ROL{Agent}', 1, '2025-09-04 13:37:52'),
(63, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{TICKETS.VIEW} ROL{Agent}', 1, '2025-09-04 13:37:58'),
(64, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{DASHBOARD.VIEW} ROL{Agent}', 1, '2025-09-04 13:38:03'),
(65, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Administrador 1', 1, '2025-09-04 13:38:38'),
(66, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Administrador 2', 1, '2025-09-04 13:38:56'),
(67, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Administrador 3', 1, '2025-09-04 13:39:09'),
(68, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Supervisor 1', 1, '2025-09-04 13:39:25'),
(69, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Supervisor 2', 1, '2025-09-04 13:39:39'),
(70, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Supervisor 3', 1, '2025-09-04 13:39:53'),
(71, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Supervisor 4', 1, '2025-09-04 13:40:13'),
(72, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Supervisor 5', 1, '2025-09-04 13:40:35'),
(73, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 1', 1, '2025-09-04 13:40:55'),
(74, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 2', 1, '2025-09-04 13:41:07'),
(75, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 3', 1, '2025-09-04 13:41:19'),
(76, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 4', 1, '2025-09-04 13:41:37'),
(77, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 5', 1, '2025-09-04 13:42:02'),
(78, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 6', 1, '2025-09-04 13:42:26'),
(79, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 7', 1, '2025-09-04 13:42:47'),
(80, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 8', 1, '2025-09-04 13:43:03'),
(81, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 9', 1, '2025-09-04 13:43:16'),
(82, 'USER_CREATED', 'USER WAS CREATED SUCCESSFULLY: Agente 10', 1, '2025-09-04 13:43:28'),
(83, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: Administrador 2', 1, '2025-09-04 13:43:47'),
(84, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: Supervisor 4', 1, '2025-09-04 13:43:50'),
(85, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: Agente 5', 1, '2025-09-04 13:43:54'),
(86, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: Agente 9', 1, '2025-09-04 13:43:57'),
(87, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: Soporte Técnico', 1, '2025-09-04 13:47:52'),
(88, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Tipo de problema', 1, '2025-09-04 13:48:06'),
(89, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Sistema operativo', 1, '2025-09-04 13:48:13'),
(90, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Versión del software', 1, '2025-09-04 13:48:23'),
(91, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ¿Afecta a un solo usuario? ', 1, '2025-09-04 13:49:37'),
(92, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ID del usuario', 1, '2025-09-04 13:49:46'),
(93, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Fecha de ocurrencia', 1, '2025-09-04 13:49:53'),
(94, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: Solicitudes de Características', 1, '2025-09-04 13:51:17'),
(95, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Impacto esperado', 1, '2025-09-04 13:51:47'),
(96, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ¿Es una característica relacionada con la seguridad?', 1, '2025-09-04 13:51:57'),
(97, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Fecha de solicitud', 1, '2025-09-04 13:52:05'),
(98, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: Problemas de Facturación', 1, '2025-09-04 13:52:25'),
(99, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Número de factura', 1, '2025-09-04 13:52:34'),
(100, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Tipo de problema', 1, '2025-09-04 13:52:42'),
(101, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Fecha del cobro', 1, '2025-09-04 13:52:49'),
(102, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Monto del cobro', 1, '2025-09-04 13:52:57'),
(103, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ID de la transacción', 1, '2025-09-04 13:53:05'),
(104, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: Preguntas Generales', 1, '2025-09-04 13:53:28'),
(105, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Tema de la consulta', 1, '2025-09-04 13:53:36'),
(106, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Departamento', 1, '2025-09-04 13:53:43'),
(107, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ¿Requiere una respuesta urgente?', 1, '2025-09-04 13:53:53'),
(108, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Fecha de la consulta', 1, '2025-09-04 13:54:00'),
(109, 'CATEGORY_CREATED', 'CATEGORY WAS CREATED SUCCESSFULLY: Reporte de Bug', 1, '2025-09-04 13:54:14'),
(110, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Pasos para reproducir', 1, '2025-09-04 13:54:23'),
(111, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Comportamiento esperado', 1, '2025-09-04 13:54:36'),
(112, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Comportamiento actual', 1, '2025-09-04 13:54:42'),
(113, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Fecha en que se encontró el bug', 1, '2025-09-04 13:54:49'),
(114, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Navegador', 1, '2025-09-04 13:54:57'),
(115, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: Versión del sistema operativo', 1, '2025-09-04 13:55:06'),
(116, 'FIELD_CREATED', 'FIELD WAS CREATED SUCCESSFULLY: ¿Es un bug crítico?', 1, '2025-09-04 13:55:12'),
(117, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Administrador 1', 1, '2025-09-04 14:03:57'),
(118, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Administrador 2', 1, '2025-09-04 14:04:09'),
(119, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Administrador 3', 1, '2025-09-04 14:04:18'),
(120, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Supervisor 1', 1, '2025-09-04 14:04:31'),
(121, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Supervisor 2', 1, '2025-09-04 14:04:39'),
(122, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Supervisor 3', 1, '2025-09-04 14:04:47'),
(123, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Supervisor 4', 1, '2025-09-04 14:04:55'),
(124, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Supervisor 5', 1, '2025-09-04 14:05:03'),
(125, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 1', 1, '2025-09-04 14:05:11'),
(126, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 2', 1, '2025-09-04 14:05:21'),
(127, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 3', 1, '2025-09-04 14:05:31'),
(128, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 4', 1, '2025-09-04 14:05:46'),
(129, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 5', 1, '2025-09-04 14:06:04'),
(130, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 6', 1, '2025-09-04 14:06:15'),
(131, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 7', 1, '2025-09-04 14:06:23'),
(132, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 8', 1, '2025-09-04 14:06:32'),
(133, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 9', 1, '2025-09-04 14:06:38'),
(134, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 10', 1, '2025-09-04 14:06:46'),
(135, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Ana Pérez', 1, '2025-09-04 14:07:30'),
(136, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Luis García', 1, '2025-09-04 14:07:36'),
(137, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Sofía Ramírez', 1, '2025-09-04 14:07:43'),
(138, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Carlos Castro', 1, '2025-09-04 14:07:50'),
(139, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Elena Torres', 1, '2025-09-04 14:07:58'),
(140, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Javier Morales', 1, '2025-09-04 14:08:06'),
(141, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Isabel Ruiz', 1, '2025-09-04 14:08:14'),
(142, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Diego López', 1, '2025-09-04 14:08:20'),
(143, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Paola Gómez', 1, '2025-09-04 14:08:28'),
(144, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Mario Díaz', 1, '2025-09-04 14:08:36'),
(145, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Laura Hernández', 1, '2025-09-04 14:08:44'),
(146, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Fernando Vargas', 1, '2025-09-04 14:08:51'),
(147, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Gabriela Ortiz', 1, '2025-09-04 14:08:59'),
(148, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Alejandro Salazar', 1, '2025-09-04 14:09:06'),
(149, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Daniela Mendoza', 1, '2025-09-04 14:09:13'),
(150, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Ricardo Reyes', 1, '2025-09-04 14:09:19'),
(151, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Valeria Navarro', 1, '2025-09-04 14:09:26'),
(152, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Héctor Jiménez', 1, '2025-09-04 14:09:32'),
(153, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Andrea Aguilar', 1, '2025-09-04 14:09:41'),
(154, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Juan Orozco', 1, '2025-09-04 14:09:49'),
(155, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Sofía Ramírez', 23, '2025-09-04 14:10:08'),
(156, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: root', 1, '2025-09-04 14:16:09'),
(157, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.VIEW} ROL{Client}', 1, '2025-09-04 14:16:20'),
(158, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.VIEW} ROL{Supervisor}', 1, '2025-09-04 14:16:34'),
(159, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{PROFILE.VIEW} ROL{Agent}', 1, '2025-09-04 14:16:40'),
(160, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: root', 1, '2025-09-04 14:22:47'),
(161, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: root', 1, '2025-09-04 14:47:16'),
(162, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 1', 10, '2025-09-04 14:47:26'),
(163, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: root', 1, '2025-09-04 14:47:39'),
(164, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Ana Pérez', 21, '2025-09-04 14:47:58'),
(165, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 2', 11, '2025-09-04 15:06:49'),
(166, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: <b>¡Hola!</b><br>Espero que te encuentres muy bien. 🙌<br>¿En que te puedo ayudar el día de hoy?', 11, '2025-09-04 15:07:55'),
(167, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, si...<br><br>Como comente en la descripción del caso, al momento de querer cambiar mi foto de perfil y darle al botón de guardar me salta un error.', 21, '2025-09-04 15:09:05'),
(168, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: ¿Podría compartirme el código de error por favor?', 11, '2025-09-04 15:09:33'),
(169, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Claro! mira es este: <i><font color=\"#ff0000\">#E:00x01239E9</font></i>', 21, '2025-09-04 15:09:57'),
(170, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Ya veo, dame un momento verifico en el sistema.', 11, '2025-09-04 15:10:10'),
(171, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Dale! 👌👌', 21, '2025-09-04 15:10:32'),
(172, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, Isabel.<br><br>¿Cómo estas? ¿En que te puedo ayudar el día de hoy?', 11, '2025-09-04 15:11:03'),
(173, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El botón de \"Guardar\" no funciona en los perfiles', 11, '2025-09-04 15:11:15'),
(174, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Javier!,<br>¿En que te puedo ayudar el día de hoy?', 11, '2025-09-04 15:11:36'),
(175, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Error de formato en las fechas', 11, '2025-09-04 15:11:39'),
(176, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: La impresión de reportes no funciona', 11, '2025-09-04 15:12:15'),
(177, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Supervisor 2', 6, '2025-09-04 15:12:37'),
(178, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Perfecto, ya lo marco como resuelto. !Buen trabajo!', 6, '2025-09-04 15:13:09'),
(179, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: La impresión de reportes no funciona', 6, '2025-09-04 15:13:11'),
(180, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, ya revise tu caso.&nbsp;<br>He aplicado las respectivas correcciones, ¿podrías verificar si en tu perfil ya puedes actualizar la información?', 11, '2025-09-04 15:14:36'),
(181, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Ana Pérez', 21, '2025-09-04 15:14:50'),
(182, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Perfecto, muchas gracias.<br>Si, ya funciona. ❤️😊<br>¡Son unos cracks!', 21, '2025-09-04 15:15:50'),
(183, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola SUP,<br>¿Podrías ayudarme a cerrar este caso?', 11, '2025-09-04 15:16:15'),
(184, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Laura,<br>¿En que te puedo colaborar?', 11, '2025-09-04 15:16:43'),
(185, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Cargo duplicado en mi tarjeta de crédito', 11, '2025-09-04 15:16:50'),
(186, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Isabel,<br><br>¿En que te puedo ayudar?', 11, '2025-09-04 15:17:10'),
(187, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El buscador no encuentra mis documentos', 11, '2025-09-04 15:17:11'),
(188, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Imágenes adjuntas se rotan automáticamente', 11, '2025-09-04 15:17:26'),
(189, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Supervisor 5', 9, '2025-09-04 15:17:38'),
(190, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, Claro!<br>Listo para el cierre!', 9, '2025-09-04 15:18:00'),
(191, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El botón de \"Guardar\" no funciona en los perfiles', 9, '2025-09-04 15:18:02'),
(192, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El botón de \"Guardar\" no funciona en los perfiles', 9, '2025-09-04 15:18:08'),
(193, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El botón de \"Guardar\" no funciona en los perfiles', 9, '2025-09-04 15:18:09'),
(194, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: El botón de \"Guardar\" no funciona en los perfiles', 9, '2025-09-04 15:18:12'),
(195, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 6', 16, '2025-09-04 15:19:00'),
(196, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Héctor,&nbsp;<br>¿En que te puedo colaborar el día de hoy?', 16, '2025-09-04 15:19:20'),
(197, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Héctor Jiménez', 38, '2025-09-04 15:19:39'),
(198, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola!<br>Si mira, quería preguntar si Bookify es integrable con mi sistema POS.<br><br>Para poder realizar pedidos a través de la aplicación.', 38, '2025-09-04 15:20:38'),
(199, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Si, mira. Debes contactarte a través del siguiente numero. 3775775771<br><br>Allí podrás solicitar el servicio de soporte para la integración del sistema con tu sistema POS.<br>Debes contar con una mínima documentación.<br><br>Veo que también estas interesado en integración con un CRM. ¿También necesitas ayuda con eso?', 16, '2025-09-04 15:22:14'),
(200, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, si.<br>¿Mas o menos que costo podría llegar a tener esta implementación?', 38, '2025-09-04 15:22:40'),
(201, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Eso depende.<br>Pero manejamos planes muy accesibles y varia según la dificultad de la integración.', 16, '2025-09-04 15:23:10'),
(202, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Supervisor 3', 7, '2025-09-04 15:24:02'),
(203, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Puedes encontrar nuestros planes a través del siguiente url:&nbsp;<a href=\"https://www.bookifyCX.com\" target=\"_blank\" rel=\"noopener noreferrer\">Bookify Planes</a>', 7, '2025-09-04 15:24:46'),
(204, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Jimmy,<br><br>Recuerda siempre redirigir al usuario al sistema de compras integrado en lugar de al equipo de ventas directamente.<br>Ahora mismo no tienen capacidad para atender a nuevos usuarios, hay mucha demanda de Bookify en el mercado.', 7, '2025-09-04 15:25:58'),
(205, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Ohh, perfecto.<br>Muchas gracias. Eso seria todo.<br>Muchas gracias!', 38, '2025-09-04 15:26:33'),
(206, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 6', 16, '2025-09-04 15:26:44'),
(207, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: A ti por preferirnos.<br>Dejo cerrado el caso.<br><br>Hasta pronto! 🤓', 16, '2025-09-04 15:27:20'),
(208, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Listo SUP,', 16, '2025-09-04 15:27:32'),
(209, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Muchas gracias!<br>¿Podría ayudarme con el cierre del caso?', 16, '2025-09-04 15:27:47'),
(210, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Integración con otras plataformas', 16, '2025-09-04 15:27:56'),
(211, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola. SUP, mira que aquí tenemos otro caso de permisos en el perfil.<br>¿Le mando el manual de requerimientos directamente?', 16, '2025-09-04 15:28:31'),
(212, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Claro.', 7, '2025-09-04 15:28:37'),
(213, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Integración con otras plataformas', 7, '2025-09-04 15:28:38'),
(214, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: No!<br>Jimmy, recuerda siempre que debes hacer el debido proceso con cada cliente.<br><br>Sin importar que sepamos que es el mismo caso. Se que puede ser un reproceso pero esto nos ayuda a que si es un problema nuevo, no genere tanto ruido e impacto.', 7, '2025-09-04 15:29:45'),
(215, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Mejor asegurarnos que dar una solución que no sirva y nos reabran el caso.', 7, '2025-09-04 15:29:59'),
(216, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Vale mi SUP.<br>Entonces ya hago el proceso.', 16, '2025-09-04 15:30:10'),
(217, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola Mario,<br>¿En que puedo ayudarte el día de hoy¡', 16, '2025-09-04 15:30:23'),
(218, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 7', 17, '2025-09-04 15:31:12'),
(219, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Administrador 1', 2, '2025-09-04 15:31:48'),
(220, 'USER_UPDATED', 'USER WAS UPDATED SUCCESSFULLY: Agente 7', 2, '2025-09-04 15:32:02'),
(221, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Ricardo Reyes', 36, '2025-09-04 15:32:51'),
(222, 'USER_INACTIVE', 'USER WAS INACTIVATED SUCCESSFULLY: root', 2, '2025-09-04 15:34:32'),
(223, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Supervisor 2', 6, '2025-09-04 15:35:16'),
(224, 'PERMISSION_ASSIGNED', 'PERMISSION WAS ASSIGNED SUCCESSFULLY: PERMISSION{LOG.READ} ROL{Supervisor}', 2, '2025-09-04 15:35:58'),
(225, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Hola, Saúl.<br><br>Veo que tienes la cola de tickets llena. Voy a transferirle este ticket, a María.', 6, '2025-09-04 15:36:48'),
(226, 'USER_LOGIN', 'USER WAS LOGGED IN SUCCESSFULLY: Agente 4', 13, '2025-09-04 15:37:33'),
(227, 'INTERACTION_CREATED', 'INTERACTION WAS CREATED SUCCESSFULLY: Dale, dale UP!', 13, '2025-09-04 15:37:44'),
(228, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Exportación de datos a PDF', 6, '2025-09-04 15:37:57'),
(229, 'TICKET_UPDATED', 'TICKET WAS UPDATED SUCCESSFULLY: Información sobre el SLA', 6, '2025-09-04 15:38:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `description`) VALUES
(1, 'ALL', 'TOTAL CONTROL'),
(2, 'CATEGORY.CREATE', 'CREATE CATEGORY'),
(3, 'CATEGORY.READ', 'READ CATEGORIES'),
(4, 'CATEGORY.UPDATE', 'UPDATE CATEGORY'),
(5, 'CATEGORY.DELETE', 'DELETE CATEGORY'),
(6, 'CTFIELDS.CREATE', 'CREATE CUSTOM TICKET FIELD'),
(7, 'CTFIELDS.READ', 'READ CUSTOM TICKET FIELDS'),
(8, 'CTFIELDS.UPDATE', 'UPDATE CUSTOM TICKET FIELD'),
(9, 'CTFIELDS.DELETE', 'DELETE CUSTOM TICKET FIELD'),
(10, 'CTFIELDS.REORDER', 'REORDER CUSTOM TICKET FIELDS'),
(11, 'INTERACTION.CREATE', 'CREATE INTERACTION'),
(12, 'INTERACTION.READ', 'READ INTERACTIONS'),
(13, 'INTERACTION.UPDATE', 'UPDATE INTERACTION'),
(14, 'INTERACTION.DELETE', 'DELETE INTERACTION'),
(15, 'INTERACTION.REPLY', 'REPLY TO INTERACTION'),
(16, 'INTERACTION.ATTACH_FILE', 'ATTACH FILE TO INTERACTION'),
(17, 'LOG.READ', 'VIEW SYSTEM LOGS'),
(18, 'LOG.CLEAR', 'CLEAR SYSTEM LOGS'),
(19, 'LOG.EXPORT', 'EXPORT SYSTEM LOGS'),
(20, 'ROLE.CREATE', 'CREATE ROLE'),
(21, 'ROLE.READ', 'READ ROLES'),
(22, 'ROLE.UPDATE', 'UPDATE ROLE'),
(23, 'ROLE.DELETE', 'DELETE ROLE'),
(24, 'ROLE.ASSIGN', 'ASSIGN ROLES TO USERS'),
(25, 'PERMISSION.CREATE', 'CREATE PERMISSIONS'),
(26, 'PERMISSION.READ', 'READ PERMISSIONS'),
(27, 'PERMISSION.UPDATE', 'UPDATE PERMISSIONS'),
(28, 'PERMISSION.DELETE', 'DELETE PERMISSIONS'),
(29, 'PERMISSION.ASSIGN', 'ASSIGN PERMISSIONS TO ROLES'),
(30, 'STATUS_HISTORY.CREATE', 'CREATE TICKET STATUS HISTORY'),
(31, 'STATUS_HISTORY.READ', 'READ TICKET STATUS HISTORY'),
(32, 'STATUS_HISTORY.UPDATE', 'UPDATE TICKET STATUS HISTORY'),
(33, 'STATUS_HISTORY.CLEAR', 'CLEAR TICKET STATUS HISTORY'),
(34, 'TERMINAL.CREATE', 'CREATE TERMINAL'),
(35, 'TERMINAL.READ', 'READ TERMINALS'),
(36, 'TERMINAL.UPDATE', 'UPDATE TERMINAL'),
(37, 'TERMINAL.DELETE', 'DELETE TERMINAL'),
(38, 'TICKET.CREATE', 'CREATE TICKET'),
(39, 'TICKET.READ', 'READ TICKETS'),
(40, 'TICKET.UPDATE', 'UPDATE TICKET'),
(41, 'TICKET.DELETE', 'DELETE TICKET'),
(42, 'TICKET.ASSIGN', 'ASSIGN TICKET TO USER'),
(43, 'TICKET.CHANGE_STATUS', 'CHANGE TICKET STATUS'),
(44, 'TICKET.MERGE', 'MERGE TICKETS'),
(45, 'TICKET.ESCALATE', 'ESCALATE TICKET'),
(46, 'TICKET.INTERN', 'SWITCH TICKET FOR INTERN'),
(47, 'TICKET.TRANSFER', 'TRANSFER TICKET'),
(48, 'TICKET.WORKLOG', 'ADD WORKLOG TO TICKET'),
(49, 'TICKET.RESOLVE', 'RESOLVE TICKET'),
(50, 'UPLOADS.UPLOAD', 'UPLOAD FILES'),
(51, 'UPLOADS.READ', 'VIEW UPLOADED FILES'),
(52, 'UPLOADS.DELETE', 'DELETE UPLOADED FILES'),
(53, 'USER.CREATE', 'CREATE USER'),
(54, 'USER.READ', 'READ USERS'),
(55, 'USER.UPDATE', 'UPDATE USER'),
(56, 'USER.DELETE', 'DELETE USER'),
(57, 'USER.ACTIVATE', 'ACTIVATE OR DEACTIVATE USER'),
(58, 'USER.ASSIGN_ROLE', 'ASSIGN ROLE TO USER'),
(59, 'USER.2FA', 'ENABLE 2FA'),
(60, 'PROFILE.READ', 'READ USER PROFILE'),
(61, 'PROFILE.UPDATE', 'UPDATE USER PROFILE'),
(62, 'WORKLOG.CREATE', 'CREATE WORKLOG'),
(63, 'WORKLOG.READ', 'READ WORKLOGS'),
(64, 'WORKLOG.UPDATE', 'UPDATE WORKLOG'),
(65, 'WORKLOG.DELETE', 'DELETE WORKLOG'),
(66, 'WORKLOG.EXPORT', 'EXPORT WORKLOGS'),
(67, 'READONLY', 'READ-ONLY ACCESS TO ALL RESOURCES'),
(68, 'TICKETS.VIEW', 'VIEW TICKETS'),
(69, 'DASHBOARD.VIEW', 'VIEW DASHBOARD'),
(70, 'USERS.VIEW', 'VIEW USERS'),
(71, 'PROFILE.VIEW', 'VIEW PROFILE'),
(72, 'ACTIVITY.VIEW', 'VIEW ACTIVITY'),
(73, 'SETTINGS.VIEW', 'VIEW SETTINGS');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'Admin'),
(2, 'Client'),
(3, 'Supervisor'),
(4, 'Agent');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id_rol` int(11) NOT NULL,
  `id_permission` int(11) NOT NULL,
  `is_allowed` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `role_permissions`
--

INSERT INTO `role_permissions` (`id_rol`, `id_permission`, `is_allowed`) VALUES
(1, 1, 1),
(2, 1, 0),
(2, 3, 1),
(2, 7, 1),
(2, 11, 1),
(2, 12, 1),
(2, 21, 1),
(2, 26, 1),
(2, 35, 1),
(2, 38, 1),
(2, 39, 1),
(2, 50, 1),
(2, 51, 1),
(2, 54, 1),
(2, 59, 1),
(2, 60, 1),
(2, 61, 1),
(2, 69, 1),
(2, 71, 1),
(3, 3, 1),
(3, 7, 1),
(3, 11, 1),
(3, 12, 1),
(3, 17, 1),
(3, 21, 1),
(3, 26, 1),
(3, 35, 1),
(3, 38, 1),
(3, 39, 1),
(3, 40, 1),
(3, 46, 1),
(3, 47, 1),
(3, 48, 1),
(3, 49, 1),
(3, 50, 1),
(3, 51, 1),
(3, 54, 1),
(3, 59, 1),
(3, 60, 1),
(3, 61, 1),
(3, 68, 1),
(3, 69, 1),
(3, 70, 1),
(3, 71, 1),
(4, 3, 1),
(4, 7, 1),
(4, 11, 1),
(4, 12, 1),
(4, 21, 1),
(4, 26, 1),
(4, 31, 1),
(4, 35, 1),
(4, 38, 1),
(4, 39, 1),
(4, 40, 1),
(4, 46, 1),
(4, 50, 1),
(4, 51, 1),
(4, 54, 1),
(4, 59, 1),
(4, 60, 1),
(4, 61, 1),
(4, 68, 1),
(4, 69, 1),
(4, 71, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
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

--
-- Volcado de datos para la tabla `status_history`
--

INSERT INTO `status_history` (`id`, `id_ticket`, `last_status`, `new_status`, `update_date`, `id_asesor`) VALUES
(1, 21, 'NEW', 'IN_PROGRESS', '2025-09-04 15:11:15', 11),
(2, 46, 'NEW', 'IN_PROGRESS', '2025-09-04 15:11:39', 11),
(3, 2, 'NEW', 'IN_PROGRESS', '2025-09-04 15:12:15', 11),
(4, 2, 'IN_PROGRESS', 'CLOSED', '2025-09-04 15:13:11', 6),
(5, 11, 'NEW', 'IN_PROGRESS', '2025-09-04 15:16:50', 11),
(6, 27, 'NEW', 'IN_PROGRESS', '2025-09-04 15:17:11', 11),
(7, 71, 'NEW', 'IN_PROGRESS', '2025-09-04 15:17:26', 11),
(8, 21, 'IN_PROGRESS', 'CLOSED', '2025-09-04 15:18:02', 9),
(9, 21, 'CLOSED', 'IN_PROGRESS', '2025-09-04 15:18:08', 9),
(10, 21, 'IN_PROGRESS', 'CLOSED', '2025-09-04 15:18:09', 9),
(11, 18, 'NEW', 'IN_PROGRESS', '2025-09-04 15:27:56', 16),
(12, 18, 'IN_PROGRESS', 'CLOSED', '2025-09-04 15:28:38', 7);

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
  `id_client` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `status` enum('NEW','IN_PROGRESS','CLOSED') NOT NULL DEFAULT 'NEW',
  `priority` enum('LOW','MEDIUM','HIGH') NOT NULL DEFAULT 'MEDIUM',
  `id_category` int(11) NOT NULL,
  `id_asesor` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tickets`
--

INSERT INTO `tickets` (`id`, `id_client`, `title`, `description`, `creation_date`, `status`, `priority`, `id_category`, `id_asesor`) VALUES
(1, 21, 'No puedo iniciar sesión en la aplicación', 'La aplicación me da un error de credenciales incorrectas. He intentado resetear la contraseña varias veces sin éxito.', '2025-09-04 14:22:42', 'NEW', 'HIGH', 1, 10),
(2, 22, 'La impresión de reportes no funciona', 'Cuando intento imprimir cualquier reporte, el sistema se congela y no responde. Esto está afectando a todo el departamento.', '2025-09-04 14:22:42', 'CLOSED', 'HIGH', 1, 11),
(3, 23, 'Falla en la sincronización de datos', 'Los datos del usuario no se sincronizan correctamente entre la versión web y la móvil. La información mostrada es inconsistente.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 1, 12),
(4, 24, 'Problema con la barra de herramientas', 'La barra de herramientas principal ha desaparecido de la interfaz de usuario. No puedo acceder a las funciones básicas del programa.', '2025-09-04 14:22:42', 'NEW', 'LOW', 1, 13),
(5, 25, 'Error al cargar un archivo', 'Recibo un mensaje de \"Error 404\" al intentar cargar un archivo en mi perfil. He verificado que el archivo no está corrupto.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 1, 16),
(6, 26, 'Integración con Slack', 'Sería muy útil tener una integración con Slack para recibir notificaciones de nuevos tickets y comentarios directamente en un canal.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 2, 18),
(7, 27, 'Opción de autenticación de 2 factores', 'Solicito la implementación de la autenticación de dos factores (2FA) para mejorar la seguridad de mi cuenta.', '2025-09-04 14:22:42', 'NEW', 'HIGH', 2, 17),
(8, 28, 'Personalización de la vista del tablero', 'Me gustaría poder personalizar las columnas del tablero de tickets para adaptarlas a mis necesidades de flujo de trabajo.', '2025-09-04 14:22:42', 'NEW', 'LOW', 2, 16),
(9, 29, 'Exportación de datos a PDF', 'Necesito una opción para exportar los tickets y sus interacciones a un archivo PDF para poder archivarlos externamente.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 2, 15),
(10, 30, 'Función de búsqueda avanzada', 'La función de búsqueda actual es muy básica. Sería de gran ayuda una búsqueda avanzada que permita filtrar por múltiples campos y fechas.', '2025-09-04 14:22:42', 'NEW', 'HIGH', 2, 12),
(11, 31, 'Cargo duplicado en mi tarjeta de crédito', 'Aparece un cargo duplicado por el monto de mi suscripción mensual en el extracto de mi tarjeta de crédito. Adjunto el comprobante del banco.', '2025-09-04 14:22:42', 'IN_PROGRESS', 'HIGH', 3, 11),
(12, 32, 'No recibí mi última factura', 'No he recibido la factura correspondiente al mes de agosto. Necesito la copia para los registros de mi empresa.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 3, 10),
(13, 33, 'Error en el monto de la factura', 'El monto de mi última factura es incorrecto. Se me ha cobrado más de lo estipulado en mi plan de servicio. Por favor, revisen el cargo.', '2025-09-04 14:22:42', 'NEW', 'HIGH', 3, 12),
(14, 34, 'Problemas con el método de pago', 'No puedo actualizar mi método de pago. La plataforma no acepta mi nueva tarjeta de crédito y me da un error genérico.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 3, 13),
(15, 35, 'Solicitud de reembolso', 'Solicito el reembolso del último cargo, ya que el servicio no cumplió con las expectativas acordadas. He cancelado mi suscripción.', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 3, 16),
(16, 36, '¿Qué planes de suscripción ofrecen?', 'Quisiera saber más sobre los diferentes planes de suscripción disponibles y las características que incluyen. Estoy interesado en el plan premium.', '2025-09-04 14:22:42', 'NEW', 'LOW', 4, 18),
(17, 37, '¿Cómo puedo cambiar mi nombre de usuario?', 'Me gustaría cambiar mi nombre de usuario. No encuentro la opción en la configuración de mi perfil. ¿Pueden ayudarme con esto?', '2025-09-04 14:22:42', 'NEW', 'LOW', 4, 17),
(18, 38, 'Integración con otras plataformas', '¿Existe una API o alguna otra forma de integrar la plataforma con un CRM externo como Salesforce?', '2025-09-04 14:22:42', 'CLOSED', 'MEDIUM', 4, 16),
(19, 39, 'Soporte técnico 24/7', 'Necesito confirmar si su equipo de soporte técnico está disponible 24/7 o si tienen un horario específico para la atención de tickets.', '2025-09-04 14:22:42', 'NEW', 'LOW', 4, 13),
(20, 40, 'Dudas sobre las políticas de privacidad', 'Tengo algunas preguntas sobre cómo manejan los datos de los usuarios y las políticas de privacidad de la empresa. ¿Dónde puedo encontrar más información?', '2025-09-04 14:22:42', 'NEW', 'MEDIUM', 4, 12),
(21, 21, 'El botón de \"Guardar\" no funciona en los perfiles', 'Al intentar guardar los cambios en mi perfil, el botón no reacciona al clic. He intentado varias veces sin éxito.', '2025-09-04 14:22:43', 'CLOSED', 'MEDIUM', 5, 11),
(22, 22, 'Error de visualización en la tabla de tickets', 'La tabla de tickets se muestra desordenada y con valores incorrectos cuando se filtra por fecha. Las columnas se superponen.', '2025-09-04 14:22:43', 'NEW', 'HIGH', 5, 10),
(23, 23, 'Fallo de redirección al cerrar sesión', 'Después de cerrar la sesión, la página no me redirige a la pantalla de inicio de sesión. Me muestra una página en blanco.', '2025-09-04 14:22:43', 'NEW', 'MEDIUM', 5, 12),
(24, 24, 'Problemas de rendimiento en el dashboard', 'El dashboard tarda mucho en cargar y la interfaz es lenta, especialmente cuando hay muchos tickets asignados. Se vuelve casi inutilizable.', '2025-09-04 14:22:43', 'NEW', 'HIGH', 5, 13),
(25, 25, 'Notificaciones de correo electrónico duplicadas', 'Estoy recibiendo notificaciones por correo electrónico duplicadas por cada nuevo comentario en un ticket. Es molesto y genera spam.', '2025-09-04 14:22:43', 'NEW', 'LOW', 5, 16),
(26, 26, 'No puedo subir archivos grandes', 'Cuando intento cargar archivos de más de 50MB, la carga falla y me sale un mensaje de error genérico. He probado con varios tipos de archivos.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 1, 10),
(27, 27, 'El buscador no encuentra mis documentos', 'La función de búsqueda no devuelve resultados para documentos que sé que existen en mi cuenta. He verificado el nombre y sigue sin aparecer.', '2025-09-04 14:27:18', 'IN_PROGRESS', 'MEDIUM', 1, 11),
(28, 28, 'Error al exportar datos a CSV', 'Recibo un \"timeout\" del servidor cada vez que intento exportar grandes cantidades de datos a un archivo CSV. La exportación nunca se completa.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 1, 12),
(29, 29, 'La app móvil se cierra inesperadamente', 'La aplicación para Android se cierra sin previo aviso al intentar abrir la sección de notificaciones. Ocurre con frecuencia.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 1, 13),
(30, 30, 'Problemas con el inicio de sesión único (SSO)', 'Mi cuenta de SSO no se conecta correctamente a la plataforma. Me da un error de autenticación. No he cambiado nada en la configuración.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 1, 16),
(31, 31, 'Notificaciones personalizadas para equipos', 'Me gustaría poder configurar notificaciones por separado para los diferentes equipos dentro de mi organización, en lugar de recibir todas en un solo canal.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 2, 18),
(32, 32, 'Integración con Microsoft Teams', 'Sería muy útil tener una integración nativa con Microsoft Teams para colaborar en los tickets de soporte sin salir de la aplicación.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 2, 17),
(33, 33, 'Panel de control para administradores', 'Necesitamos un panel de control centralizado para que los administradores puedan ver el estado de todos los tickets y la carga de trabajo de los asesores.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 2, 16),
(34, 34, 'Soporte para múltiples idiomas', 'Solicito soporte para múltiples idiomas en la interfaz de la plataforma. Mi equipo trabaja en diferentes países y la barrera del idioma es un problema.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 2, 13),
(35, 35, 'Comentarios privados en tickets', 'Me gustaría tener la opción de añadir comentarios privados en los tickets que solo sean visibles para los asesores, pero no para el cliente.', '2025-09-04 14:27:18', 'NEW', 'LOW', 2, 12),
(36, 36, 'No puedo ver mi historial de pagos', 'La sección de \"Historial de Pagos\" está vacía en mi cuenta. No puedo verificar los cargos anteriores.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 3, 11),
(37, 37, 'Cargo no autorizado', 'Detecté un cargo en mi tarjeta de crédito que no autoricé. El monto y la fecha no coinciden con ninguna de mis suscripciones activas.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 3, 10),
(38, 38, 'Error en la información de mi empresa en la factura', 'La factura que recibí tiene un error en el nombre de mi empresa. Necesito una corrección para poder procesar el pago.', '2025-09-04 14:27:18', 'NEW', 'LOW', 3, 12),
(39, 39, 'No puedo cambiar mi dirección de facturación', 'Intento actualizar mi dirección de facturación, pero el sistema no guarda los cambios. Necesito que se actualice para la próxima factura.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 3, 13),
(40, 40, 'Recibí una factura con IVA incorrecto', 'El IVA aplicado en la factura no es el correcto para mi región. Necesito una factura con el impuesto adecuado.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 3, 16),
(41, 21, '¿Se pueden fusionar cuentas?', 'Tengo dos cuentas de usuario y me gustaría fusionar la información de ambas en una sola. ¿Es posible?', '2025-09-04 14:27:18', 'NEW', 'LOW', 4, 18),
(42, 22, '¿Cuáles son los requisitos del sistema?', 'Necesito saber los requisitos mínimos de hardware y software para poder usar la aplicación sin problemas en mi computadora de trabajo.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 4, 17),
(43, 23, '¿Ofrecen descuentos para estudiantes?', 'Quiero saber si tienen algún tipo de descuento o promoción especial para estudiantes o instituciones educativas.', '2025-09-04 14:27:18', 'NEW', 'LOW', 4, 16),
(44, 24, '¿Cómo puedo obtener un recibo de mi compra?', 'Compré una suscripción anual pero no he recibido un recibo o comprobante de la transacción. ¿Dónde puedo obtenerlo?', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 4, 13),
(45, 25, 'Política de uso justo', 'Quiero conocer los detalles de su política de uso justo para el almacenamiento en la nube y el uso de la API.', '2025-09-04 14:27:18', 'NEW', 'LOW', 4, 12),
(46, 26, 'Error de formato en las fechas', 'Las fechas en el dashboard se muestran en un formato incorrecto (YYYY-MM-DD en lugar de DD/MM/YYYY). Afecta a la legibilidad.', '2025-09-04 14:27:18', 'IN_PROGRESS', 'LOW', 5, 11),
(47, 27, 'Imágenes no cargan en los comentarios', 'Al adjuntar imágenes en los comentarios de los tickets, no se visualizan correctamente. Aparece un icono de \"imagen rota\".', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 5, 10),
(48, 28, 'Filtro de categoría no funciona', 'El filtro para buscar tickets por categoría no funciona. Al seleccionar una categoría, la lista de tickets se queda en blanco.', '2025-09-04 14:27:18', 'NEW', 'HIGH', 5, 12),
(49, 29, 'Botón de \"Cerrar ticket\" no visible en móviles', 'En la versión móvil de la plataforma, el botón para cerrar un ticket no es visible en la parte inferior de la pantalla. Es imposible cerrarlos desde el teléfono.', '2025-09-04 14:27:18', 'NEW', 'MEDIUM', 5, 13),
(50, 30, 'Recibí un error 500 al guardar el perfil', 'Al intentar guardar los cambios en mi perfil (ej. cambiar el nombre), el servidor devuelve un error 500 (Internal Server Error).', '2025-09-04 14:27:18', 'NEW', 'HIGH', 5, 16),
(51, 27, 'Problemas de rendimiento general', 'La aplicación de escritorio se ha vuelto muy lenta al cargar. Las operaciones básicas tardan varios segundos en completarse, afectando mi productividad.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 1, 10),
(52, 28, 'Configuración de notificaciones por correo', 'No logro configurar las notificaciones por correo electrónico. A pesar de habilitarlas en mi perfil, no recibo ningún mensaje.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 1, 11),
(53, 29, 'Error al conectar con la base de datos', 'Recibo un mensaje de error \"Conexión a la base de datos fallida\" al iniciar el programa. No puedo acceder a mis datos.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 1, 12),
(54, 30, 'Función de \"Reenviar contraseña\" no funciona', 'He olvidado mi contraseña y cuando uso la opción para reenviarla, no me llega el correo electrónico. He revisado mi carpeta de spam.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 1, 13),
(55, 31, 'Problema con la vista de calendario', 'El calendario no carga los eventos correctamente. Muestra un \"cargando...\" infinito o la pantalla se queda en blanco. He probado en diferentes navegadores.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 1, 16),
(56, 32, 'Historial de cambios de tickets', 'Me gustaría que se pudiera ver un historial detallado de los cambios hechos en cada ticket, incluyendo quién lo modificó y cuándo.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 2, 18),
(57, 33, 'Recordatorios de tickets inactivos', 'Solicito una característica que envíe recordatorios automáticos a los asesores si un ticket no ha tenido actividad por más de 48 horas.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 2, 17),
(58, 34, 'Etiquetas personalizables para tickets', 'Actualmente las etiquetas son fijas, pero me gustaría poder crear etiquetas personalizadas para organizar mis tickets de manera más eficiente.', '2025-09-04 14:28:28', 'NEW', 'LOW', 2, 16),
(59, 35, 'Vista de tickets por Asesor', 'Necesito una vista en el dashboard que muestre el listado de tickets asignados a cada asesor, para poder distribuir la carga de trabajo de forma equitativa.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 2, 13),
(60, 36, 'Compatibilidad con otros navegadores', 'Pido que la plataforma sea compatible con el navegador Brave, ya que actualmente experimenta fallos de formato y rendimiento.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 2, 12),
(61, 37, 'Factura sin detalles de la compra', 'Mi última factura solo muestra el monto total sin especificar los productos o servicios que se cobraron. Necesito un desglose detallado.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 3, 11),
(62, 38, 'Problema con la moneda de facturación', 'Se me ha cobrado en dólares en lugar de euros, que es la moneda de mi región. Esto me está generando un cargo adicional por conversión.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 3, 10),
(63, 39, 'No se aplicó un descuento en mi factura', 'Se me prometió un descuento del 15% al renovar mi suscripción, pero no se ha reflejado en la última factura. Por favor, corríjanlo.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 3, 12),
(64, 40, 'Recibí una factura de un servicio cancelado', 'Cancelé mi suscripción el mes pasado, pero acabo de recibir una factura por un servicio que ya no estoy usando. Solicito la anulación del cargo.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 3, 13),
(65, 21, 'Error en los datos fiscales de mi empresa', 'La dirección fiscal de mi empresa en la factura es incorrecta. No coincide con el registro oficial y necesito una factura actualizada.', '2025-09-04 14:28:28', 'NEW', 'LOW', 3, 16),
(66, 22, '¿Se pueden transferir los tickets?', 'Como supervisor, quiero saber si es posible transferir tickets entre diferentes asesores para equilibrar la carga de trabajo.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 4, 18),
(67, 23, '¿Ofrecen una versión de prueba gratuita?', 'Estoy interesado en probar su servicio antes de contratarlo. ¿Tienen una versión de prueba gratuita o un periodo de demostración?', '2025-09-04 14:28:28', 'NEW', 'LOW', 4, 17),
(68, 24, 'Pregunta sobre la política de privacidad de datos', 'Tengo dudas sobre cómo gestionan los datos personales de mis clientes que se suben a la plataforma. ¿Están protegidos de acuerdo con el GDPR?', '2025-09-04 14:28:28', 'NEW', 'HIGH', 4, 16),
(69, 25, '¿Cómo puedo integrar la plataforma con mi sitio web?', 'Quiero integrar su sistema de tickets en mi propio sitio web para que mis clientes no tengan que acceder a una página externa. ¿Es posible?', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 4, 13),
(70, 26, 'Información sobre el SLA', 'Necesito información sobre los acuerdos de nivel de servicio (SLA) que ofrecen. ¿Cuál es el tiempo de respuesta garantizado para los tickets?', '2025-09-04 14:28:28', 'NEW', 'HIGH', 4, NULL),
(71, 27, 'Imágenes adjuntas se rotan automáticamente', 'Al subir una imagen en los comentarios, el sistema la rota 90 grados a la izquierda automáticamente. Sucede con todas las imágenes.', '2025-09-04 14:28:28', 'IN_PROGRESS', 'MEDIUM', 5, 11),
(72, 28, 'Buscador de usuarios con fallo', 'El buscador de usuarios del dashboard no funciona. No devuelve ningún resultado, incluso si escribo el nombre de un usuario existente.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 5, 10),
(73, 29, 'Botón de \"Guardar\" inactivo en el editor de roles', 'En la página de gestión de roles, el botón de \"Guardar cambios\" se muestra inactivo, sin permitirme guardar los permisos que he modificado.', '2025-09-04 14:28:28', 'NEW', 'HIGH', 5, 12),
(74, 30, 'Error en el contador de tickets', 'El contador de tickets en el dashboard muestra una cantidad incorrecta. El total de tickets es de 100, pero el contador solo marca 85.', '2025-09-04 14:28:28', 'NEW', 'LOW', 5, 13),
(75, 31, 'Problema con la vista de reportes', 'Los reportes generados no se muestran correctamente. Las tablas están desalineadas y las gráficas se ven cortadas.', '2025-09-04 14:28:28', 'NEW', 'MEDIUM', 5, 16);

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

--
-- Volcado de datos para la tabla `ticket_custom_values`
--

INSERT INTO `ticket_custom_values` (`id`, `custom_field_id`, `ticket_id`, `value`) VALUES
(1, 1, 1, 'Problema de acceso'),
(2, 2, 1, 'Windows 10'),
(3, 3, 1, '2.5.1'),
(4, 4, 1, '1'),
(5, 5, 1, '1001'),
(6, 6, 1, '2025-09-03'),
(7, 1, 2, 'Fallo de impresión'),
(8, 2, 2, 'Windows Server 2022'),
(9, 3, 2, '4.0.0'),
(10, 4, 2, '0'),
(11, 5, 2, '1002'),
(12, 6, 2, '2025-09-02'),
(13, 1, 3, 'Sincronización'),
(14, 2, 3, 'iOS 15'),
(15, 3, 3, '3.1.2'),
(16, 4, 3, '1'),
(17, 5, 3, '1003'),
(18, 6, 3, '2025-09-04'),
(19, 1, 4, 'Interfaz de usuario'),
(20, 2, 4, 'macOS Monterey'),
(21, 3, 4, '2.5.1'),
(22, 4, 4, '1'),
(23, 5, 4, '1004'),
(24, 6, 4, '2025-09-04'),
(25, 1, 5, 'Carga de archivos'),
(26, 2, 5, 'Android 12'),
(27, 3, 5, '3.2.0'),
(28, 4, 5, '1'),
(29, 5, 5, '1005'),
(30, 6, 5, '2025-09-03'),
(31, 7, 6, 'Aumentar la eficiencia en la gestión de tickets'),
(32, 8, 6, '0'),
(33, 9, 6, '2025-09-03'),
(34, 7, 7, 'Mejorar la seguridad del sistema'),
(35, 8, 7, '1'),
(36, 9, 7, '2025-09-04'),
(37, 7, 8, 'Adaptar la interfaz a los flujos de trabajo individuales'),
(38, 8, 8, '0'),
(39, 9, 8, '2025-09-02'),
(40, 7, 9, 'Facilitar el archivo y la auditoría de tickets'),
(41, 8, 9, '0'),
(42, 9, 9, '2025-09-01'),
(43, 7, 10, 'Reducir el tiempo de búsqueda de información'),
(44, 8, 10, '0'),
(45, 9, 10, '2025-09-03'),
(46, 10, 11, 'INV-2025-08-0123'),
(47, 11, 11, 'Cargo duplicado'),
(48, 12, 11, '2025-08-25'),
(49, 13, 11, '49.99'),
(50, 14, 11, '123456789'),
(51, 10, 12, 'INV-2025-08-0456'),
(52, 11, 12, 'Factura no recibida'),
(53, 12, 12, '2025-08-30'),
(54, 13, 12, '29.99'),
(55, 14, 12, '987654321'),
(56, 10, 13, 'INV-2025-09-0789'),
(57, 11, 13, 'Monto incorrecto'),
(58, 12, 13, '2025-09-01'),
(59, 13, 13, '59.99'),
(60, 14, 13, '554433221'),
(61, 10, 14, 'N/A'),
(62, 11, 14, 'Error al actualizar método de pago'),
(63, 12, 14, '2025-09-03'),
(64, 13, 14, '0'),
(65, 14, 14, '0'),
(66, 10, 15, 'INV-2025-08-0123'),
(67, 11, 15, 'Reembolso'),
(68, 12, 15, '2025-08-26'),
(69, 13, 15, '49.99'),
(70, 14, 15, '123456789'),
(71, 15, 16, 'Planes y precios'),
(72, 16, 16, 'Ventas'),
(73, 17, 16, '0'),
(74, 18, 16, '2025-09-04'),
(75, 15, 17, 'Cambio de usuario'),
(76, 16, 17, 'Soporte'),
(77, 17, 17, '0'),
(78, 18, 17, '2025-09-03'),
(79, 15, 18, 'Integración de API'),
(80, 16, 18, 'Producto'),
(81, 17, 18, '0'),
(82, 18, 18, '2025-09-02'),
(83, 15, 19, 'Horario de soporte'),
(84, 16, 19, 'Soporte'),
(85, 17, 19, '0'),
(86, 18, 19, '2025-09-04'),
(87, 15, 20, 'Políticas de privacidad'),
(88, 16, 20, 'Legal'),
(89, 17, 20, '0'),
(90, 18, 20, '2025-09-04'),
(91, 19, 21, '1. Navegar a la página de perfil. 2. Cambiar un campo (ej. teléfono). 3. Clic en \"Guardar\"'),
(92, 20, 21, 'Que los cambios se guarden y el perfil se actualice.'),
(93, 21, 21, 'El botón no reacciona.'),
(94, 22, 21, '2025-09-03'),
(95, 23, 21, 'Chrome'),
(96, 24, 21, 'Windows 10'),
(97, 25, 21, '0'),
(98, 19, 22, '1. Navegar a la vista de tickets. 2. Aplicar filtro por \"fecha de creación\".'),
(99, 20, 22, 'Que la tabla muestre los tickets ordenados por fecha de forma correcta.'),
(100, 21, 22, 'La tabla muestra datos desordenados y mal formateados.'),
(101, 22, 22, '2025-09-02'),
(102, 23, 22, 'Firefox'),
(103, 24, 22, 'Ubuntu 20.04'),
(104, 25, 22, '1'),
(105, 19, 23, '1. Clic en el botón \"Cerrar sesión\".'),
(106, 20, 23, 'Redirección a la página de inicio de sesión.'),
(107, 21, 23, 'La página se queda en blanco.'),
(108, 22, 23, '2025-09-04'),
(109, 23, 23, 'Safari'),
(110, 24, 23, 'macOS Sonoma'),
(111, 25, 23, '0'),
(112, 19, 24, '1. Abrir el dashboard.'),
(113, 20, 24, 'El dashboard carga rápidamente.'),
(114, 21, 24, 'El dashboard tarda más de 10 segundos en cargar.'),
(115, 22, 24, '2025-09-03'),
(116, 23, 24, 'Chrome'),
(117, 24, 24, 'Windows 11'),
(118, 25, 24, '1'),
(119, 19, 25, '1. Comentar en cualquier ticket. 2. Esperar la notificación por correo.'),
(120, 20, 25, 'Recibir una sola notificación por comentario.'),
(121, 21, 25, 'Recibo dos o más correos por el mismo comentario.'),
(122, 22, 25, '2025-09-04'),
(123, 23, 25, 'N/A (problema del sistema)'),
(124, 24, 25, 'N/A (problema del sistema)'),
(125, 25, 25, '0'),
(126, 1, 26, 'Límites de subida'),
(127, 2, 26, 'Windows 11'),
(128, 3, 26, '4.2.0'),
(129, 4, 26, '1'),
(130, 5, 26, '1006'),
(131, 6, 26, '2025-09-04'),
(132, 1, 27, 'Problema de búsqueda'),
(133, 2, 27, 'macOS Ventura'),
(134, 3, 27, '3.1.2'),
(135, 4, 27, '1'),
(136, 5, 27, '1007'),
(137, 6, 27, '2025-09-03'),
(138, 1, 28, 'Fallo de exportación'),
(139, 2, 28, 'Linux (Debian)'),
(140, 3, 28, '4.2.1'),
(141, 4, 28, '1'),
(142, 5, 28, '1008'),
(143, 6, 28, '2025-09-02'),
(144, 1, 29, 'Cierre inesperado'),
(145, 2, 29, 'Android 13'),
(146, 3, 29, '3.3.0'),
(147, 4, 29, '1'),
(148, 5, 29, '1009'),
(149, 6, 29, '2025-09-04'),
(150, 1, 30, 'Fallo de autenticación'),
(151, 2, 30, 'Windows 10'),
(152, 3, 30, '4.2.0'),
(153, 4, 30, '0'),
(154, 5, 30, '1010'),
(155, 6, 30, '2025-09-04'),
(156, 7, 31, 'Mejorar la comunicación y colaboración interna'),
(157, 8, 31, '0'),
(158, 9, 31, '2025-09-03'),
(159, 7, 32, 'Integración con herramientas empresariales existentes'),
(160, 8, 32, '0'),
(161, 9, 32, '2025-09-04'),
(162, 7, 33, 'Optimizar la gestión y supervisión del equipo de soporte'),
(163, 8, 33, '0'),
(164, 9, 33, '2025-09-04'),
(165, 7, 34, 'Aumentar la accesibilidad para usuarios internacionales'),
(166, 8, 34, '0'),
(167, 9, 34, '2025-09-01'),
(168, 7, 35, 'Mejorar la privacidad de la información interna'),
(169, 8, 35, '0'),
(170, 9, 35, '2025-09-02'),
(171, 10, 36, 'N/A'),
(172, 11, 36, 'Historial de pagos inaccesible'),
(173, 12, 36, '2025-09-04'),
(174, 13, 36, '0'),
(175, 14, 36, '0'),
(176, 10, 37, 'N/A'),
(177, 11, 37, 'Cargo no reconocido'),
(178, 12, 37, '2025-09-04'),
(179, 13, 37, '9.99'),
(180, 14, 37, '123456790'),
(181, 10, 38, 'INV-2025-09-0790'),
(182, 11, 38, 'Información incorrecta'),
(183, 12, 38, '2025-09-02'),
(184, 13, 38, '89.99'),
(185, 14, 38, '234567890'),
(186, 10, 39, 'N/A'),
(187, 11, 39, 'Error al actualizar dirección'),
(188, 12, 39, '2025-09-03'),
(189, 13, 39, '0'),
(190, 14, 39, '0'),
(191, 10, 40, 'INV-2025-09-0791'),
(192, 11, 40, 'Impuesto incorrecto'),
(193, 12, 40, '2025-09-04'),
(194, 13, 40, '110.50'),
(195, 14, 40, '345678901'),
(196, 15, 41, 'Fusión de cuentas'),
(197, 16, 41, 'Cuentas'),
(198, 17, 41, '0'),
(199, 18, 41, '2025-09-04'),
(200, 15, 42, 'Requisitos técnicos'),
(201, 16, 42, 'Soporte Técnico'),
(202, 17, 42, '0'),
(203, 18, 42, '2025-09-03'),
(204, 15, 43, 'Descuentos y promociones'),
(205, 16, 43, 'Ventas'),
(206, 17, 43, '0'),
(207, 18, 43, '2025-09-02'),
(208, 15, 44, 'Recibo de compra'),
(209, 16, 44, 'Facturación'),
(210, 17, 44, '0'),
(211, 18, 44, '2025-09-04'),
(212, 15, 45, 'Política de uso'),
(213, 16, 45, 'Legal'),
(214, 17, 45, '0'),
(215, 18, 45, '2025-09-04'),
(216, 19, 46, '1. Navegar al dashboard. 2. Observar las fechas de los tickets.'),
(217, 20, 46, 'Que las fechas se muestren en el formato correcto (DD/MM/YYYY).'),
(218, 21, 46, 'Las fechas se muestran en formato YYYY-MM-DD.'),
(219, 22, 46, '2025-09-03'),
(220, 23, 46, 'Firefox'),
(221, 24, 46, 'Windows 10'),
(222, 25, 46, '0'),
(223, 19, 47, '1. Abrir un ticket. 2. Adjuntar una imagen. 3. Guardar el comentario.'),
(224, 20, 47, 'Que la imagen se muestre correctamente.'),
(225, 21, 47, 'La imagen aparece rota.'),
(226, 22, 47, '2025-09-04'),
(227, 23, 47, 'Safari'),
(228, 24, 47, 'iOS 15'),
(229, 25, 47, '0'),
(230, 19, 48, '1. Clic en el filtro de categorías. 2. Seleccionar \"Soporte Técnico\".'),
(231, 20, 48, 'Que se muestren todos los tickets de \"Soporte Técnico\".'),
(232, 21, 48, 'La lista se queda en blanco.'),
(233, 22, 48, '2025-09-04'),
(234, 23, 48, 'Chrome'),
(235, 24, 48, 'Windows 11'),
(236, 25, 48, '1'),
(237, 19, 49, '1. Acceder a la plataforma desde el móvil. 2. Abrir un ticket. 3. Scroll hacia abajo.'),
(238, 20, 49, 'Que el botón \"Cerrar ticket\" sea visible.'),
(239, 21, 49, 'El botón no aparece en la pantalla.'),
(240, 22, 49, '2025-09-03'),
(241, 23, 49, 'Chrome Móvil'),
(242, 24, 49, 'Android 12'),
(243, 25, 49, '0'),
(244, 19, 50, '1. Navegar a la página de perfil. 2. Modificar el nombre. 3. Clic en \"Guardar\".'),
(245, 20, 50, 'Que se guarden los cambios correctamente.'),
(246, 21, 50, 'Recibo un error 500 del servidor.'),
(247, 22, 50, '2025-09-04'),
(248, 23, 50, 'Firefox'),
(249, 24, 50, 'macOS Monterey'),
(250, 25, 50, '1'),
(251, 1, 51, 'Lentitud de la aplicación'),
(252, 2, 51, 'Windows 10'),
(253, 3, 51, '4.2.1'),
(254, 4, 51, '1'),
(255, 5, 51, '1011'),
(256, 6, 51, '2025-09-04'),
(257, 1, 52, 'Problemas de notificaciones'),
(258, 2, 52, 'N/A'),
(259, 3, 52, '5.0.0'),
(260, 4, 52, '1'),
(261, 5, 52, '1012'),
(262, 6, 52, '2025-09-04'),
(263, 1, 53, 'Fallo de conexión'),
(264, 2, 53, 'Linux (CentOS)'),
(265, 3, 53, '4.1.0'),
(266, 4, 53, '0'),
(267, 5, 53, '1013'),
(268, 6, 53, '2025-09-03'),
(269, 1, 54, 'Recuperación de contraseña'),
(270, 2, 54, 'N/A'),
(271, 3, 54, '5.0.1'),
(272, 4, 54, '1'),
(273, 5, 54, '1014'),
(274, 6, 54, '2025-09-04'),
(275, 1, 55, 'Problema con el calendario'),
(276, 2, 55, 'macOS Monterey'),
(277, 3, 55, '5.0.0'),
(278, 4, 55, '0'),
(279, 5, 55, '1015'),
(280, 6, 55, '2025-09-04'),
(281, 7, 56, 'Trazabilidad de la gestión de tickets'),
(282, 8, 56, '0'),
(283, 9, 56, '2025-09-04'),
(284, 7, 57, 'Optimización de la atención y tiempos de respuesta'),
(285, 8, 57, '0'),
(286, 9, 57, '2025-09-03'),
(287, 7, 58, 'Organización y filtrado de tickets'),
(288, 8, 58, '0'),
(289, 9, 58, '2025-09-04'),
(290, 7, 59, 'Supervisión y gestión de equipos'),
(291, 8, 59, '0'),
(292, 9, 59, '2025-09-02'),
(293, 7, 60, 'Ampliar el soporte a más navegadores'),
(294, 8, 60, '0'),
(295, 9, 60, '2025-09-04'),
(296, 10, 61, 'INV-2025-09-0792'),
(297, 11, 61, 'Falta de detalles'),
(298, 12, 61, '2025-09-04'),
(299, 13, 61, '199.99'),
(300, 14, 61, '456789012'),
(301, 10, 62, 'INV-2025-09-0793'),
(302, 11, 62, 'Moneda incorrecta'),
(303, 12, 62, '2025-09-03'),
(304, 13, 62, '80.00'),
(305, 14, 62, '567890123'),
(306, 10, 63, 'INV-2025-09-0794'),
(307, 11, 63, 'Descuento no aplicado'),
(308, 12, 63, '2025-09-02'),
(309, 13, 63, '150.00'),
(310, 14, 63, '678901234'),
(311, 10, 64, 'INV-2025-08-0795'),
(312, 11, 64, 'Cargo por servicio cancelado'),
(313, 12, 64, '2025-09-01'),
(314, 13, 64, '49.99'),
(315, 14, 64, '789012345'),
(316, 10, 65, 'INV-2025-09-0796'),
(317, 11, 65, 'Datos fiscales incorrectos'),
(318, 12, 65, '2025-09-04'),
(319, 13, 65, '99.99'),
(320, 14, 65, '890123456'),
(321, 15, 66, 'Transferencia de tickets'),
(322, 16, 66, 'Soporte'),
(323, 17, 66, '0'),
(324, 18, 66, '2025-09-04'),
(325, 15, 67, 'Versión de prueba'),
(326, 16, 67, 'Ventas'),
(327, 17, 67, '0'),
(328, 18, 67, '2025-09-03'),
(329, 15, 68, 'Protección de datos (GDPR)'),
(330, 16, 68, 'Legal'),
(331, 17, 68, '1'),
(332, 18, 68, '2025-09-04'),
(333, 15, 69, 'Integración con sitio web'),
(334, 16, 69, 'Producto'),
(335, 17, 69, '0'),
(336, 18, 69, '2025-09-03'),
(337, 15, 70, 'Acuerdo de Nivel de Servicio (SLA)'),
(338, 16, 70, 'Soporte'),
(339, 17, 70, '1'),
(340, 18, 70, '2025-09-04'),
(341, 19, 71, '1. Cargar una imagen. 2. Observar la vista previa.'),
(342, 20, 71, 'La imagen se muestra en la orientación correcta.'),
(343, 21, 71, 'La imagen se muestra rotada.'),
(344, 22, 71, '2025-09-04'),
(345, 23, 71, 'Chrome'),
(346, 24, 71, 'iOS 15'),
(347, 25, 71, '0'),
(348, 19, 72, '1. Navegar a la sección de \"Usuarios\". 2. Escribir un nombre en el buscador.'),
(349, 20, 72, 'El buscador muestra la lista de usuarios filtrados.'),
(350, 21, 72, 'No hay resultados.'),
(351, 22, 72, '2025-09-04'),
(352, 23, 72, 'Firefox'),
(353, 24, 72, 'Windows 11'),
(354, 25, 72, '1'),
(355, 19, 73, '1. Navegar a \"Roles y permisos\". 2. Modificar un rol.'),
(356, 20, 73, 'El botón \"Guardar cambios\" se activa.'),
(357, 21, 73, 'El botón está inactivo y no puedo guardar.'),
(358, 22, 73, '2025-09-03'),
(359, 23, 73, 'N/A'),
(360, 24, 73, 'N/A'),
(361, 25, 73, '1'),
(362, 19, 74, '1. Abrir el dashboard. 2. Comparar el contador de tickets con la lista completa.'),
(363, 20, 74, 'El contador debe mostrar la cantidad total de tickets.'),
(364, 21, 74, 'El contador muestra una cantidad inferior a la real.'),
(365, 22, 74, '2025-09-04'),
(366, 23, 74, 'Chrome'),
(367, 24, 74, 'macOS Monterey'),
(368, 25, 74, '0'),
(369, 19, 75, '1. Generar un reporte. 2. Abrir el archivo generado.'),
(370, 20, 75, 'El reporte se visualiza de forma correcta.'),
(371, 21, 75, 'Las tablas y gráficas se ven mal.'),
(372, 22, 75, '2025-09-04'),
(373, 23, 75, 'N/A (problema del sistema)'),
(374, 24, 75, 'N/A (problema del sistema)'),
(375, 25, 75, '0');

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
  `rol` int(11) DEFAULT 2,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `totp_secret` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `password`, `phone`, `rol`, `profile_image`, `created_at`, `totp_secret`, `is_active`) VALUES
(1, 'root', 'root', '$2y$10$MNrnLJ4u49B0ygPV4dWhr.rWCVTvE6.3hgH1nLf1VxdhwLbl0cN0q', '01', 1, NULL, '2025-09-04 18:33:24', NULL, 0),
(2, 'Administrador 1', 'Admin1', '$2y$10$Oe.hH/3JaTbf4/7ElvgrZ.QjX7YLPvkySSKcssCbBdylboCYYecEu', '0', 1, NULL, '2025-09-04 13:38:38', NULL, 1),
(3, 'Administrador 2', 'Admin2', '$2y$10$xrT11DxLMiyqhGEW4cxpX.2azBWMEkWSjfbO8TKnz59hQ/ipKFB6S', '0', 1, NULL, '2025-09-04 13:38:56', NULL, 0),
(4, 'Administrador 3', 'Admin3', '$2y$10$lbwNK0MxEIDrl8tWMDu3SObNfIb/dh4A7ICZtEolMhvSzbnFVLYGe', '0', 1, NULL, '2025-09-04 13:39:09', NULL, 1),
(5, 'Supervisor 1', 'SUP-1', '$2y$10$32NzLgvw//ddgBYMOudRcetQuF2.T.IDhG2eE0jsWUGI452mVgzOW', '0', 3, NULL, '2025-09-04 13:39:25', NULL, 1),
(6, 'Supervisor 2', 'SUP-2', '$2y$10$r7a5HUVzTEIAg0m2gTy2GeH6pR/5kbe21ceRzdXak0xi3HTUaa9TK', '0', 3, NULL, '2025-09-04 13:39:39', NULL, 1),
(7, 'Supervisor 3', 'SUP-3', '$2y$10$44U2KIs.urZ9jH9hOftPSeq7tIa2Z.Nvpl2MG13uu5NSPON.XB38y', '0', 3, NULL, '2025-09-04 13:39:53', NULL, 1),
(8, 'Supervisor 4', 'SUP-4', '$2y$10$OZ3XFSuxbsbyKKksoeVKC.9WnYZnJnXq1p68CXtG2qy1zTDB4Sgsm', '0', 3, NULL, '2025-09-04 13:40:13', NULL, 0),
(9, 'Supervisor 5', 'SUP-5', '$2y$10$/GvDTznvApPUTcO6R.D2CeoY4bNBOumvfZDt9IqJeolUDFeMLJAry', '0', 3, NULL, '2025-09-04 13:40:35', NULL, 1),
(10, 'Agente 1', 'AG-1', '$2y$10$5lJsHFSQdXSJOwdUc8j8ve4OhETazozji5WvXMRKzASYSsQShFIA6', '0', 4, NULL, '2025-09-04 13:40:55', NULL, 1),
(11, 'Agente 2', 'AG-2', '$2y$10$B7njtBQb6tGFQmJON3P/juy8RQpaQeNh75xDSIhn9w8FdIZdoCkKy', '0', 4, NULL, '2025-09-04 13:41:07', NULL, 1),
(12, 'Agente 3', 'AG-3', '$2y$10$NznGCwqbih5n3s2NbTQyVeN.Vhh65Y5b7PtZ1fpMHZj0W6u6evT7W', '0', 4, NULL, '2025-09-04 13:41:19', NULL, 1),
(13, 'Agente 4', 'AG-4', '$2y$10$5CPZ4QS5YjAltLFdrxkGAeorIgTpcFLlixA1oECmR2oSJcndhioA.', '0', 4, NULL, '2025-09-04 13:41:37', NULL, 1),
(15, 'Agente 5', 'AG-5', '$2y$10$1PcK3aADNgBLsXQEqG5LFedI8Fyutbi5d50AOxAlccNUknDbOnqTa', '0', 4, NULL, '2025-09-04 13:42:02', NULL, 0),
(16, 'Agente 6', 'AG-6', '$2y$10$L/8EoHJnktlNzfunIesQ0O0sMka6WI/i8DR2LWR7yD8xGCj71Zas6', '0', 4, NULL, '2025-09-04 13:42:26', NULL, 1),
(17, 'Agente 7', 'AG-7', '$2y$10$UHURk9qwl8PclckuMIBIPO/Zng/gdTZ.oop5S0DClnqW6DSCVTOWy', '0', 4, NULL, '2025-09-04 13:42:47', NULL, 1),
(18, 'Agente 8', 'AG-8', '$2y$10$xOeVHgsyybaeohP34vxii.cfJY2lrIad84.vObbg8bTJM/W/LMTNC', '0', 4, NULL, '2025-09-04 13:43:03', NULL, 1),
(19, 'Agente 9', 'AG-9', '$2y$10$C.fOY7sRqjXv4biV125PTOFhTUWaF7/Df8mPqs.9Wr7/wl/FHv4FO', '0', 4, NULL, '2025-09-04 13:43:16', NULL, 0),
(20, 'Agente 10', 'AG-10', '$2y$10$lHTtVtNJKns83w.zLI.km.41kn7VgniaKtCW3kdhvi668DboA87sO', '0', 4, NULL, '2025-09-04 13:43:28', NULL, 1),
(21, 'Ana Pérez', 'ana.perez', '$2y$10$wLj7UFZpnrGZOJlUW8YWrOT8jdCaDyXU0djAvJ48Gv7KNVnLZIFje', '1234567890', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(22, 'Luis García', 'luis.garcia', '$2y$10$JgjSuQbjj9jNHu0TGj230O1ojUyggzw2QMe7moQo/GzzXKasDwT5i', '1234567891', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(23, 'Sofía Ramírez', 'sofia.ramirez', '$2y$10$4mev9BLG6uhTjQ2YeeOPRegMGbq8NXPgyquwnvrl2bMVtbhjqM4c.', '1234567892', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(24, 'Carlos Castro', 'carlos.castro', '$2y$10$4gCoK5CEOcOY5r5x.g0ar.5Svue2n30ncqDbCAd6ZdtwWSsAM99J.', '1234567893', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(25, 'Elena Torres', 'elena.torres', '$2y$10$ESMf67h9ifbzvcePcdwZu.ezX3uWDb1r4Q0aXRzI.YjSXZ9go917e', '1234567894', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(26, 'Javier Morales', 'javier.morales', '$2y$10$kP7aqfY.5H0sVD4n.anGpeIULAcK3rnfltVJDhdOFksSrmGDaJ4Fu', '1234567895', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(27, 'Isabel Ruiz', 'isabel.ruiz', '$2y$10$jNiiqv1DytAM68MU6STwN.EFqMXNC1ZouSoYBXZvQhIBgk.ISgqIC', '1234567896', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(28, 'Diego López', 'diego.lopez', '$2y$10$HThy9BrfO89Sfmww1U3Gjewx1B//TmhBZ3UKqWAwc1CAz791l29HS', '1234567897', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(29, 'Paola Gómez', 'paola.gomez', '$2y$10$a.8UtyVknUtEhcLuGXfLwuhcMdqiefBA8gbtdClOqmizswhgwstI2', '1234567898', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(30, 'Mario Díaz', 'mario.diaz', '$2y$10$sDRSf8wDN4y2rSUD3aLWFOltGuqP9/j1IYbdasNWiO5woUcjCEmX.', '1234567899', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(31, 'Laura Hernández', 'laura.hernandez', '$2y$10$/zgEEWvGBWwtaywUBwfP3eQj8RuuN1hn6dAOvU06Y6zL2Z5JI.Yj6', '1234567900', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(32, 'Fernando Vargas', 'fernando.vargas', '$2y$10$OCRM5MLygoktZno52ZkvjOpSpGwjs/eOqXRPUGAKSwNY/Nu8MxNcq', '1234567901', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(33, 'Gabriela Ortiz', 'gabriela.ortiz', '$2y$10$5Tcx4VbXWPUBtvM4dE77BezV5i9n6FvWnm56d4G02hE2Y4qGD03Sa', '1234567902', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(34, 'Alejandro Salazar', 'alejandro.salazar', '$2y$10$a1gGm08XoE5u02/AXI0OUuWADbtHLK3IJPXiYPkXWm83mOnGAuqsa', '1234567903', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(35, 'Daniela Mendoza', 'daniela.mendoza', '$2y$10$EaLjcwTxQvvfgThSgnIzGOm23.mp/BSQB2LlGJtH8nmokLA6AWFwi', '1234567904', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(36, 'Ricardo Reyes', 'ricardo.reyes', '$2y$10$S3i2jtkEz297RLKB7nJ0gO2pMGEtisRtjr67PsgbVtMWV8p5l7SUi', '1234567905', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(37, 'Valeria Navarro', 'valeria.navarro', '$2y$10$5kNDyeyUkQefjdrMz9uXluThHpiealbEbj6i6W0feC1/y3Vm4atmq', '1234567906', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(38, 'Héctor Jiménez', 'hector.jimenez', '$2y$10$dVUWdV.m59Otdq6QUINMuezqS3SYWVJZ1VCNHlUUgNTDaaxNriU8y', '1234567907', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(39, 'Andrea Aguilar', 'andrea.aguilar', '$2y$10$CyZK0RJuE8ruHa7Fy4IH2uIIiXwtMGT5E1K5TDJJ6OsjT3eCPdVom', '1234567908', 2, NULL, '2025-09-04 13:57:02', NULL, 1),
(40, 'Juan Orozco', 'juan.orozco', '$2y$10$wNS9bp/X7z6TdVciZsZAnehbUSXP1ql3H2LqRCObYkElWJF4xyTVm', '1234567909', 2, NULL, '2025-09-04 13:57:02', NULL, 1);

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
-- Indices de la tabla `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key_UNIQUE` (`key`);

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
  ADD KEY `id_client` (`id_client`),
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `custom_ticket_fields`
--
ALTER TABLE `custom_ticket_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `interactions`
--
ALTER TABLE `interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT de la tabla `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

--
-- AUTO_INCREMENT de la tabla `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `terminals`
--
ALTER TABLE `terminals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de la tabla `ticket_custom_values`
--
ALTER TABLE `ticket_custom_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `users` (`id`) ON DELETE CASCADE,
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
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `worklog`
--
ALTER TABLE `worklog`
  ADD CONSTRAINT `worklog_ibfk_1` FOREIGN KEY (`id_ticket`) REFERENCES `tickets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `worklog_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;


