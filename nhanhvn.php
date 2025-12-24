<?php
/**
 * Plugin Name: Nhanh.vn WooCommerce Sync
 * Plugin URI: https://nhanhvn.vn
 * Description: Plugin đồng bộ sản phẩm giữa Nhanh.vn và WooCommerce thông qua API
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * Text Domain: nhanhvn-sync
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.2
 * WC requires at least: 3.0
 * WC tested up to: 8.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


 // Prevent direct access
 if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
  
if (!defined('NHANHVN_FILE')) {
    define('NHANHVN_FILE', __FILE__);
}

if (!class_exists('Nhanhvn_Constants', false)) {
    include_once dirname(__FILE__) . '/includes/nhanhvn-class-constants.php';
}

// Include the main Nhanhvn class.
  if (!class_exists('Nhanhvn', false)) {
    include_once dirname(__FILE__) . '/includes/nhanhvn-class.php';
  }

  function Nhanhvn() {
    return Nhanhvn::instance();
  }
  
  $GLOBALS['nhanhvn'] = Nhanhvn();