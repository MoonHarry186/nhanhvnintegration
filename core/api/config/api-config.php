<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

return [
    'namespace' => 'nhanhvn/v1',
    'permission_callback' => 'current_user_can',
    'permission_capability' => 'manage_options',
    'default_error_code' => 400,
    'default_success_code' => 200,
    'max_items_per_page' => 100,
    'default_items_per_page' => 10,
    'supported_methods' => [
        'GET',
        'POST',
        'PUT',
        'DELETE',
        'PATCH'
    ],
    'response_formats' => [
        'json' => 'application/json',
        'xml' => 'application/xml'
    ]
]; 