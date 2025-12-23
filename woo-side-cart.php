<?php

   /**
    * Plugin Name: Woo Side Cart
    * Plugin URI: https://github.com/Junaid-Ahmadd/woo-side-cart
    * Description: A lightweight WooCommerce side cart with AJAX updates, free shipping progress bar, and cross-sell carousel.
    * Version: 1.0.0
    * Requires at least: 5.8
    * Requires PHP: 7.4
    * Author: Junaid Ahmad
    * Author URI: https://github.com/Junaid-Ahmadd
    * License: GPL v2 or later
    * License URI: https://www.gnu.org/licenses/gpl-2.0.html
    * Text Domain: woo-side-cart
    * Domain Path: /languages
    * Requires Plugins: woocommerce
    */


if (!defined('ABSPATH')) {
    exit;
}


// Define plugin constants
define('WOO_SIDE_CART_VERSION', '1.0.0');
define('WOO_SIDE_CART_PATH', plugin_dir_path(__FILE__));
define('WOO_SIDE_CART_URL', plugin_dir_url(__FILE__));

// Add after your constants, before the WooCommerce check:

function woo_side_cart_load_textdomain() {
    load_plugin_textdomain('woo-side-cart', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'woo_side_cart_load_textdomain', 5);


// Check if WooCommerce is active
function woo_side_cart_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'woo_side_cart_woocommerce_missing_notice');
        return false;
    }
    return true;
}

function woo_side_cart_woocommerce_missing_notice()
{
?>
    <div class="notice notice-error">
        <p><?php esc_html_e('Woo Side Cart requires WooCommerce to be installed and activated.', 'woo-side-cart'); ?></p>
    </div>
<?php
}

// Load plugin classes
function woo_side_cart_init()
{
    if (!woo_side_cart_check_woocommerce()) {
        return;
    }

    // Load classes
    require_once WOO_SIDE_CART_PATH . 'includes/class-side-cart.php';
    require_once WOO_SIDE_CART_PATH . 'includes/class-shipping-bar.php';
    require_once WOO_SIDE_CART_PATH . 'includes/class-cross-sells.php';
    require_once WOO_SIDE_CART_PATH . 'includes/class-settings.php';

    // Initialize
    Woo_Side_Cart_Main::get_instance();
    Woo_Side_Cart_Shipping_Bar::get_instance();
    Woo_Side_Cart_Cross_Sells::get_instance();
    Woo_Side_Cart_Settings::get_instance();
}
add_action('plugins_loaded', 'woo_side_cart_init');
