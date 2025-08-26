<?php
return [
    // Mensajes generales
    'success_default'       => 'Operación exitosa.',
    'fail_default'          => 'Ocurrió un error.',
    'unauthorized'          => 'No tienes permisos para esta acción.',
    'forbidden'             => 'Acceso denegado.',
    'not_found'             => 'Recurso no encontrado.',

    // Usuarios
    'user_not_found'        => 'Usuario no encontrado.',
    'user_created'          => 'Usuario creado correctamente.',
    'user_updated'          => 'Usuario actualizado correctamente.',
    'user_deleted'          => 'Usuario eliminado correctamente.',

    // Mensajes dinámicos
    'results_found'         => 'Se han encontrado {x} resultados.',
    'error_with_code'       => 'Error {code} - {description}.',
    'items_processed'       => '{x} elementos procesados correctamente.',
    'invalid_input'         => 'El valor "{field}" es inválido.',

    // Autenticación
    'login_success'         => 'Inicio de sesión exitoso.',
    'login_failed'          => 'Credenciales inválidas.',
    'token_invalid'         => 'Token inválido o expirado.',
    'token_required'        => 'Se requiere un token de autenticación.',

    // Base de datos / Servidor
    'internal_error'        => 'Error interno del servidor.',
    'db_connection_failed'  => 'No se pudo conectar a la base de datos.',

    // Ejemplos de placeholders adicionales
    'operation_failed_for'  => 'La operación falló para {entity}.',
    'operation_success_for' => 'La operación se realizó correctamente para {entity}.'
];
