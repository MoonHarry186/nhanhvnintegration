<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once NHANHVN_CORE_API_ABSPATH . 'base/nhanhvn-class-api-base.php';

class Nhanhvn_Api_Callback extends Nhanhvn_Base_API
{
    protected static $instance = null;
    protected $rest_base = 'oauth/callback';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Callback for get all products endpoint (GET)
     * /wp-json/customix/v1/products
     * 
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response The response object
     */
    public function register_routes()
    {
        // Get all products endpoint (GET) /wp-json/customix/v1/products
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            'methods' => 'GET',
            'callback' => [$this, 'redirect_url'],
            'permission_callback' => [$this, 'dev_permission'],
        ]);
    }

    /**
     * Callback for get all products endpoint (GET)
     * /wp-json/customix/v1/products
     * 
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response
     */
    public function redirect_url(WP_REST_Request $request)
    {
        global $wpdb;
    
        $accessCode = sanitize_text_field(
            $request->get_param('accessCode')
        );
    
        if (empty($accessCode)) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Missing accessCode'
            ], 400);
        }
    
        $tokenData = $this->exchange_token($accessCode);
    
        if (!$tokenData || empty($tokenData['access_token'])) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to exchange access token'
            ], 500);
        }
    
        // Encrypt accessToken
        $encryptedToken = Nhanhvn_Class_Encript::encrypt(
            $tokenData['access_token']
        );
    
        // Convert expireTime (unix timestamp) to datetime
        $expiresAt = date(
            'Y-m-d H:i:s',
            (int) $tokenData['expire_time']
        );
    
        // Clear old token (single token mode)
        $wpdb->query(
            "TRUNCATE TABLE {$wpdb->prefix}nhanh_tokens"
        );
    
        // Save token
        $saved = $wpdb->insert(
            $wpdb->prefix . 'nhanh_tokens',
            [
                'access_token' => $encryptedToken,
                'expires_at'   => $expiresAt,
                'created_at'   => current_time('mysql'),
            ],
            ['%s', '%s', '%s']
        );
    
        if ($saved === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'Failed to save token'
            ], 500);
        }
    
        // Redirect back to admin page
        wp_redirect(
            admin_url('admin.php?page=nhanhvn&connected=1')
        );
        exit;
    }
    


    private function exchange_token($accessCode) {
        if (empty($accessCode)) {
            return false;
        }

        $appId     = defined('NHANH_APP_ID') ? NHANH_APP_ID : '';
        $secretKey = defined('NHANH_SECRET_KEY') ? NHANH_SECRET_KEY : '';

        if (!$appId || !$secretKey) {
            error_log('[NhanhVN] Missing appId or secretKey');
            return false;
        }

        $url = add_query_arg([
            'appId' => $appId,
        ], 'https://pos.open.nhanh.vn/v3.0/app/getaccesstoken');

        $response = wp_remote_post($url, [
            'timeout' => 15,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => wp_json_encode([
                'accessCode' => $accessCode,
                'secretKey'  => $secretKey,
            ]),
        ]);

        if (is_wp_error($response)) {
            error_log('[NhanhVN] HTTP Error: ' . $response->get_error_message());
            return false;
        }

        $statusCode = wp_remote_retrieve_response_code($response);
        $body       = wp_remote_retrieve_body($response);
        $result     = json_decode($body, true);

        if ($statusCode !== 200 || empty($result)) {
            error_log('[NhanhVN] Invalid response: ' . $body);
            return false;
        }

        if (!isset($result['code']) || (int)$result['code'] !== 1) {
            error_log('[NhanhVN] API error: ' . $body);
            return false;
        }

        if (empty($result['data']['accessToken'])) {
            error_log('[NhanhVN] accessToken missing');
            return false;
        }

        return [
            'access_token' => $result['data']['accessToken'],
            'expire_time'  => $result['data']['expireTime'] ?? null,
        ];
    }
}

new Nhanhvn_Api_Callback();
