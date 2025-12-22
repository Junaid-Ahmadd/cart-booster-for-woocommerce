<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('woo_side_cart_auto_open');
delete_option('woo_side_cart_shipping_bar_enabled');
delete_option('woo_side_cart_shipping_threshold');
delete_option('woo_side_cart_cross_sells_enabled');
delete_option('woo_side_cart_cross_sells_limit');
delete_option('woo_side_cart_icon');