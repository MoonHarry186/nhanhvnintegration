<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Nhanhvn_Constants {
    public $version = '1.0.0';
    public function __construct() {
        $this->define('NHANHVN_VERSION', $this->version);
        $this->define('NHANHVN_ABSPATH', dirname(NHANHVN_FILE) . '/');
        $this->define('NHANHVN_ADMIN_ABSPATH', dirname(NHANHVN_FILE) . '/includes/admin/');
        $this->define('NHANHVN_CORE_ABSPATH', dirname(NHANHVN_FILE) . '/core/');
        $this->define('NHANHVN_CORE_API_ABSPATH', NHANHVN_CORE_ABSPATH . 'api/');
        $this->define('NHANHVN_CORE_INCLUDES_ABSPATH', NHANHVN_CORE_ABSPATH . 'includes/');
        $this->define('NHANH_SECRET_KEY', AUTH_KEY);
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }
}

new Nhanhvn_Constants();