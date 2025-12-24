<?php

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

class Nhanhvn
{
    private static $instance = null;

    public $version = '1.0.0';

    public function __construct() {
        $this->includes();
    }

    // Include files
    private function includes()
    {
        $include_files = array(
            'includes/nhanhvn-class-api.php',
        );
        foreach ($include_files as $file) {
            include_once NHANHVN_ABSPATH . $file;
        }

        if (is_admin()) {
        }
    }

    /**
     * Instance
     *
     * @return Customix
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
