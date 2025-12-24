<?php

// Prevent direct access
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

require_once NHANHVN_CORE_API_ABSPATH . 'base/nhanhvn-class-api-base.php';

class Nhanhvn_Api_Callback extends Nhanhvn_Base_API {
    protected static $instance = null;
    protected $rest_base = 'oauth/callback';

    public function __construct() {
        parent::__construct();
    }

    /**
     * Callback for get all products endpoint (GET)
     * /wp-json/customix/v1/products
     * 
     * @param WP_REST_Request $request The request object
     * @return WP_REST_Response The response object
     */
    public function register_routes() {
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
    public function redirect_url()
    {
       echo "Oauth Success";
    }


    
}

new Nhanhvn_Api_Callback();