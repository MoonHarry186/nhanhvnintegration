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
        $this->init_hooks();
    }

    // Include files
    private function includes()
    {
        $include_files = array(
            'includes/nhanhvn-class-install.php',
            'includes/nhanhvn-class-encript.php',
            'includes/nhanhvn-class-menu.php',
            'includes/nhanhvn-class-api.php',
        );
        foreach ($include_files as $file) {
            include_once NHANHVN_ABSPATH . $file;
        }

        if (is_admin()) {
        }
    }


    private function init_hooks() {
        // Fired when the plugin is activated
        register_activation_hook(NHANHVN_FILE, array('Nhanhvn_Install', 'activate'));
        register_deactivation_hook(NHANHVN_FILE, array('Nhanhvn_Install', 'deactivate'));
    }

    /**
     * Instance
     *
     * @return Nhanhvn
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
