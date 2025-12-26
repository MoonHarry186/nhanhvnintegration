<?php

// Prevent direct access
defined('ABSPATH') || exit;

if (class_exists('Nhanhvn_Class_Menu', false)) {
    return new Nhanhvn_Class_Menu();
}

/**
 * Nhanhvn Class Menu
 * 
 * @package Nhanhvn
 * @subpackage Nhanhvn_Class_Menu
 * @since 1.0.0
 * @version 1.0.0
 * @author Nhanhvn
 * @link https://nhanhvn.com
 * @license GPL-2.0+
 * @copyright 2025 Nhanhvn
 */
class Nhanhvn_Class_Menu
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_page'));
    }

    public function add_menu_page()
    {
        add_menu_page(
            'Nhanhvn',
            'Nhanhvn',
            'manage_options',
            'nhanhvn',
            array($this, 'render_admin_content'),
            'dashicons-printer',
            100
        );

        add_submenu_page(
            'nhanhvn',
            'Products',
            'Products',
            'manage_options',
            'nhanhvn-products',
            [$this, 'render_products_page']
        );

        add_submenu_page(
            'nhanhvn',
            'Cronjob',
            'Cronjob',
            'manage_options',
            'nhanhvn-cronjob',
            [$this, 'render_cronjob_page']
        );
    }

    public function render_admin_content()
    {
        global $wpdb;

        $table = $wpdb->prefix . 'nhanh_tokens';

        $token = $wpdb->get_row(
            "SELECT * FROM {$table} ORDER BY id DESC LIMIT 1"
        );

        echo '<div class="wrap">';
        echo '<h1>Nhanh.vn Integration</h1>';

        if ($token) {

            $createdAt = date(
                'Y-m-d H:i:s',
                strtotime($token->created_at)
            );

            $expiresAt = date(
                'Y-m-d H:i:s',
                strtotime($token->expires_at)
            );

            $daysLeft = floor(
                (strtotime($token->expires_at) - time()) / DAY_IN_SECONDS
            );

            echo '<p style="color:green;font-weight:bold;">🟢 Connected</p>';

            echo '<table class="widefat striped" style="max-width:600px">';
            echo '<tbody>';
            echo '<tr><th>Created at</th><td>' . esc_html($createdAt) . '</td></tr>';
            echo '<tr><th>Expires at</th><td>' . esc_html($expiresAt) . '</td></tr>';
            echo '<tr><th>Days left</th><td>' . esc_html($daysLeft) . '</td></tr>';
            echo '</tbody>';
            echo '</table>';

            echo '<p style="margin-top:15px;">';
            echo '<a href="' . esc_url(
                admin_url('admin.php?page=nhanhvn&action=reconnect')
            ) . '" class="button button-primary">Reconnect</a> ';

            echo '<a href="' . esc_url(
                admin_url('admin.php?page=nhanhvn&action=disconnect')
            ) . '" class="button button-secondary"
                onclick="return confirm(\'Disconnect Nhanh.vn?\')">
                Disconnect</a>';
            echo '</p>';
        } else {

            $loginUrl = "https://nhanh.vn/oauth?version=1.0"
                . "&appId=" . urlencode(NHANH_APP_ID)
                . "&returnLink=" . urlencode(
                    rest_url('nhanh/v1/oauth/callback')
                );

            echo '<p style="color:red;font-weight:bold;">🔴 Not connected</p>';
            echo '<p>Connect your Nhanh.vn account to start syncing data.</p>';

            echo '<a href="' . esc_url($loginUrl) . '" class="button button-primary">
                Connect Nhanh.vn
            </a>';
        }

        echo '</div>';
    }

    public function sync_products_from_nhanh()
    {
        $payload = [
            'filters' => [
                'name' => 'Áo sơ mi',
            ],
            'paginator' => [
                'size' => 5,
                'sort' => ['id' => 'desc'],
                'next' => '',
            ],
        ];

        $response = $this->get_products_from_nhanh($payload);

        // echo "<pre>";
        // print_r($response);

        if (empty($response['data'])) return;

        foreach ($response['data'] as $item) {

            $nhanhCode = $item['code'];
            $stock     = $item['inventory']['available'] ?? 0;
            $price     = $item['prices']['retail'] ?? 0;

            $existing = wc_get_products([
                'limit'     => 1,
                'meta_key'  => '_nhanh_product_code',
                'meta_value' => $nhanhCode,
            ]);

            // if (empty($existing)) {
            //     $this->create_wc_product_from_nhanh($item);
            // } else {
            //     $this->update_wc_stock($existing[0]->get_id(), $stock);
            // }
        }
    }

    private function group_products_by_parent($data)
    {
        $grouped = [];
    
        foreach ($data as $item) {
            $parentId = $item['parentId'] ?? 0;
            if (!$parentId) continue;
    
            $grouped[$parentId][] = $item;
        }
    
        return $grouped;
    }

    private function get_products_from_nhanh($payload)
    {
        global $wpdb;

        $token = $wpdb->get_row(
            "SELECT * FROM {$wpdb->prefix}nhanh_tokens ORDER BY id DESC LIMIT 1"
        );

        if (!$token) {
            return new WP_Error('no_token', 'Nhanh.vn not connected');
        }

        $url = add_query_arg([
            'appId'       => NHANH_APP_ID,
            'businessId'  => NHANH_BUSINESS_ID,
        ], 'https://pos.open.nhanh.vn/v3.0/product/list');

        $response = wp_remote_post($url, [
            'headers' => [
                'Authorization' => Nhanhvn_Class_Encript::decrypt($token->access_token),
                'Content-Type'  => 'application/json',
            ],
            'body'    => wp_json_encode($payload),
            'timeout' => 30,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        return json_decode(wp_remote_retrieve_body($response), true);
    }

    public function render_products_page()
    {

        if (isset($_GET['nhanh_sync']) && wp_verify_nonce($_GET['_wpnonce'], 'nhanh_sync_action')) {
            $this->sync_products_from_nhanh();
        }

        echo '<div class="wrap">';
        echo '<h1>Nhanh.vn Products</h1>';
        // Button đồng bộ
        $sync_url = wp_nonce_url(
            add_query_arg([
                'page'       => $_GET['page'],
                'nhanh_sync' => 1,
            ], admin_url('admin.php')),
            'nhanh_sync_action'
        );
    

        echo '<p>';
        echo '<a href="' . esc_url($sync_url) . '" class="button button-primary">';
        echo '🔄 Đồng bộ sản phẩm từ Nhanh.vn';
        echo '</a>';
        echo '</p>';
    }

    private function update_wc_stock($product_id, $stock)
    {
        $product = wc_get_product($product_id);

        if (!$product) return;

        $product->set_stock_quantity($stock);
        $product->set_stock_status($stock > 0 ? 'instock' : 'outofstock');
        $product->save();
    }


    private function create_wc_product_from_nhanh($item)
    {

        $product = new WC_Product_Simple();

        $product->set_name($item['name']);
        $product->set_regular_price($item['prices']['retail']);
        $product->set_manage_stock(true);
        $product->set_stock_quantity($item['inventory']['available']);
        $product->set_stock_status(
            $item['inventory']['available'] > 0 ? 'instock' : 'outofstock'
        );

        $product_id = $product->save();

        // 🔑 LƯU CODE NHANH
        update_post_meta($product_id, '_nhanh_product_code', $item['code']);

        // (optional) lưu parentId
        update_post_meta($product_id, '_nhanh_parent_id', $item['parentId']);
    }

    public function render_cronjob_page() {
        ?>
        <div class="wrap">
            <h1>Nhah.vn Sync Cron Job</h1>
    
            <form method="post" action="">
                <?php wp_nonce_field('nhanh_cronjob_save', 'nhanh_cronjob_nonce'); ?>
    
                <table class="form-table">
                    <tr>
                        <th scope="row">Sync interval (minutes)</th>
                        <td>
                            <input type="number" name="nhanh_sync_interval"
                                   value="<?php echo esc_attr(get_option('nhanh_sync_interval', 30)); ?>"
                                   min="1" />
                            <p class="description">Ví dụ: 15 = mỗi 15 phút sync</p>
                        </td>
                    </tr>
                </table>
    
                <?php submit_button('Save & Schedule'); ?>
            </form>
        </div>
        <?php
    
        $this->handle_cronjob_form();
    }

    private function handle_cronjob_form() {
        if (!isset($_POST['nhanh_cronjob_nonce'])) {
            return;
        }
    
        if (!wp_verify_nonce($_POST['nhanh_cronjob_nonce'], 'nhanh_cronjob_save')) {
            return;
        }
    
        $interval = intval($_POST['nhanh_sync_interval']);
        if ($interval <= 0) {
            return;
        }
    
        update_option('nhanh_sync_interval', $interval);
    
        // clear cron cũ
        wp_clear_scheduled_hook('nhanh_sync_cron_hook');
    
        // schedule cron mới
        wp_schedule_event(
            time() + ($interval * 60),
            'nhanh_custom_interval',
            'nhanh_sync_cron_hook'
        );
    
        echo '<div class="updated"><p>Cron job scheduled successfully.</p></div>';
    }
    
}

new Nhanhvn_Class_Menu();
