<?php

// Prevent direct access
defined('ABSPATH') || exit;

if (class_exists('Nhanhvn_Class_Encript', false)) {
  return new Nhanhvn_Class_Encript();
}

/**
 * Nhanhvn Class Menu
 * 
 * @package Nhanhvn
 * @subpackage Nhanhvn_Class_Encript
 * @since 1.0.0
 * @version 1.0.0
 * @author Nhanhvn
 * @link https://nhanhvn.com
 * @license GPL-2.0+
 * @copyright 2025 Nhanhvn
 */
class Nhanhvn_Class_Encript {

    private static string $cipher = 'aes-256-cbc';

    public function __construct() {
    }


    public static function encrypt(string $plain): string {
        $key = hash('sha256', NHANH_SECRET_KEY, true);
        $iv  = random_bytes(16);

        $encrypted = openssl_encrypt($plain, self::$cipher, $key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt(string $encrypted): string {
        $data = base64_decode($encrypted);
        $iv   = substr($data, 0, 16);
        $text = substr($data, 16);

        $key = hash('sha256', NHANH_SECRET_KEY, true);
        return openssl_decrypt($text, self::$cipher, $key, 0, $iv);
    }
}

new Nhanhvn_Class_Encript();