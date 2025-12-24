<?php

class Nhanhvn_API {
    private $api_classes = [];

    public function __construct() {
        $this->load_api_classes();
    }

    private function load_api_classes() {
        // Load base API class first
        require_once NHANHVN_CORE_API_ABSPATH . 'nhanhvn-class-api-callback.php';
        // Initialize controllers
        $this->api_classes[] = Nhanhvn_Api_Callback::get_instance();
    }


}

new Nhanhvn_API();