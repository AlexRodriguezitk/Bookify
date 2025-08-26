<?php
return [
    // General messages
    'success_default'       => 'Operation successful.',
    'fail_default'          => 'An error occurred.',
    'unauthorized'          => 'You do not have permission for this action.',
    'forbidden'             => 'Access denied.',
    'not_found'             => 'Resource not found.',

    // Users
    'user_not_found'        => 'User not found.',
    'user_created'          => 'User created successfully.',
    'user_updated'          => 'User updated successfully.',
    'user_deleted'          => 'User deleted successfully.',

    // Dynamic messages
    'results_found'         => '{x} results found.',
    'error_with_code'       => 'Error {code} - {description}.',
    'items_processed'       => '{x} items processed successfully.',
    'invalid_input'         => 'The value "{field}" is invalid.',

    // Authentication
    'login_success'         => 'Login successful.',
    'login_failed'          => 'Invalid credentials.',
    'token_invalid'         => 'Invalid or expired token.',
    'token_required'        => 'Authentication token is required.',

    // Database / Server
    'internal_error'        => 'Internal server error.',
    'db_connection_failed'  => 'Could not connect to the database.',

    // Additional placeholders
    'operation_failed_for'  => 'The operation failed for {entity}.',
    'operation_success_for' => 'The operation was successful for {entity}.'
];
