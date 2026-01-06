<?php

/**
 * Plugin Name: QuantWP – Side Cart for WooCommerce
 * Plugin URI: https://github.com/Junaid-Ahmadd/cart-booster-for-woocommerce
 * Description: A lightweight WooCommerce side cart with AJAX updates, free shipping progress bar, and cross-sell carousel.
 * Version: 1.0.0
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * Author: Junaid Ahmad
 * Author URI: https://github.com/Junaid-Ahmadd
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quantwp-sidecart-for-woocommerce
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */


if (!defined('ABSPATH')) {
    exit;
}


// Define plugin constants
define('QUANTWP_VERSION', '1.0.0');
define('QUANTWP_PATH', plugin_dir_path(__FILE__));
define('QUANTWP_URL', plugin_dir_url(__FILE__));


// Check if WooCommerce is active
function quantwp_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', 'quantwp_woocommerce_missing_notice');
        return false;
    }
    return true;
}

function quantwp_woocommerce_missing_notice()
{
?>
    <div class="notice notice-error">
        <p><?php esc_html_e('QuantWP – Side Cart for WooCommerce requires WooCommerce to be installed and activated.', 'quantwp-sidecart-for-woocommerce'); ?></p>
    </div>
<?php
}

// Load plugin classes
function quantwp_side_cart_init()
{
    if (!quantwp_check_woocommerce()) {
        return;
    }

    // Load classes
    require_once QUANTWP_PATH . 'includes/quantwp-class-side-cart.php';
    require_once QUANTWP_PATH . 'includes/quantwp-class-shipping-bar.php';
    require_once QUANTWP_PATH . 'includes/quantwp-class-cross-sells.php';
    require_once QUANTWP_PATH . 'includes/quantwp-class-settings.php';

    // Initialize
    QuantWP_SideCart_Main::get_instance();
    QuantWP_SideCart_Shipping_Bar::get_instance();
    QuantWP_SideCart_Cross_Sells::get_instance();
    QuantWP_SideCart_Settings::get_instance();
}
add_action('plugins_loaded', 'quantwp_side_cart_init');
