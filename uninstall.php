<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

delete_option('quantwp_sidecart_auto_open');
delete_option('quantwp_sidecart_shipping_bar_enabled');
delete_option('quantwp_sidecart_shipping_threshold');
delete_option('quantwp_sidecart_cross_sells_enabled');
delete_option('quantwp_sidecart_cross_sells_limit');
delete_option('quantwp_sidecart_icon');