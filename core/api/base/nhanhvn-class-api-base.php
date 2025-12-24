<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Nhanhvn Base API
 * 
 * @package Nhanhvn
 * @subpackage API
 * @since 1.0.0
 * @version 1.0.0
 * @author Nhanhvn
 */
abstract class Nhanhvn_Base_API {
    protected static $instance = null;
    protected $namespace = 'nhanh/v1';
    protected $rest_base = '';

    public function __construct() {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public static function get_instance() {
        if (null === static::$instance) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    abstract public function register_routes();

    /**
     * Verify nonce
     * @param string $nonce
     * @return bool
     */
    private function verify_nonce($nonce) {
        if ( ! empty( $nonce ) && ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return new WP_Error(
                'rest_nonce_invalid',
                __( 'Invalid or expired nonce.' ),
                [ 'status' => 403 ]
            );
        }
        return true;
    }

    /**
     * Check if the user is admin
     * @param WP_REST_Request $request
     * @return bool
     */
    public function is_admin($request) {
        $nonce = $request->get_header('X-WP-Nonce');
        if ($this->verify_nonce($nonce) === true && current_user_can('manage_options') === true) {
            return true;
        }
        return false;
    }

    public function dev_permission() {
        return true;
    }

    protected function get_error_response($message, $code = 400) {
        return new WP_Error(
            'pod_api_error',
            $message,
            ['status' => $code]
        );
    }

    protected function get_success_response($data, $code = 200) {
        return new WP_REST_Response($data, $code);
    }

    public function validate_numeric_param($param) {
        return is_numeric($param);
    }

    public function validate_required_param($param) {
        return !empty($param);
    }

    protected function sanitize_text($text) {
        return sanitize_text_field($text);
    }

    protected function sanitize_url($url) {
        return esc_url_raw($url);
    }

    protected function sanitize_array($array) {
        return array_map('sanitize_text_field', $array);
    }
} 