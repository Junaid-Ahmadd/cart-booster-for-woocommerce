<?php

/**
 * Shipping Progress Bar Class - OPTIMIZED VERSION
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuantWP_SideCart_Shipping_Bar
{
    protected static $instance = null;

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks()
    {
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Add shipping bar to side cart
        add_action('quantwp_sidecart_after_header', array($this, 'render_empty_wrapper'));

        // Add shipping bar data to fragments
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'shipping_bar_fragment'));
        
        // Clear cache when settings change
        add_action('update_option_quantwp_sidecart_shipping_threshold', array($this, 'clear_shipping_cache'));
        add_action('update_option_quantwp_sidecart_shipping_bar_enabled', array($this, 'clear_shipping_cache'));
    }

    public function enqueue_assets()
    {
        wp_enqueue_style(
            'quantwp-sidecart',
            QUANTWP_URL . 'assets/css/side-cart.css',
            array(),
            QUANTWP_VERSION
        );
    }

    /**
     * Clear shipping bar cache
     */
    public function clear_shipping_cache()
    {
        // Clear for all users (you could make this per-user if needed)
        delete_transient('quantwp_shipping_threshold');
    }

    /**
     * Get threshold with caching
     */
    public function get_threshold()
    {
        $threshold = get_transient('quantwp_shipping_threshold');
        
        if (false === $threshold) {
            $threshold = floatval(get_option('quantwp_sidecart_shipping_threshold', 50));
            // Cache for 1 hour
            set_transient('quantwp_shipping_threshold', $threshold, HOUR_IN_SECONDS);
        }
        
        return $threshold;
    }

    public function get_cart_total()
    {
        return WC()->cart->get_subtotal();
    }

    /**
     * Calculate progress - lightweight calculation, no DB queries
     */
    public function calculate_progress()
    {
        $cart_total = $this->get_cart_total();
        $threshold = $this->get_threshold();

        if ($threshold <= 0) {
            return array(
                'percentage' => 0,
                'remaining' => 0,
                'qualified' => false
            );
        }

        $percentage = min(($cart_total / $threshold) * 100, 100);
        $remaining = max($threshold - $cart_total, 0);
        $qualified = $cart_total >= $threshold;

        return array(
            'percentage' => round($percentage, 2),
            'remaining' => $remaining,
            'qualified' => $qualified,
            'cart_total' => $cart_total,
            'threshold' => $threshold
        );
    }

    public function render_empty_wrapper()
    {
        $cart = WC()->cart;

        // Check if shipping bar is enabled
        if (!get_option('quantwp_sidecart_shipping_bar_enabled', 1)) {
            return;
        }

        // Only show if cart has items
        if ($cart->is_empty()) {
            return;
        }

        echo '<div class="quantwp-shipping-bar-wrapper"></div>';
    }

    public function render_shipping_bar_content()
    {
        $cart = WC()->cart;

        // Check if enabled
        if (!get_option('quantwp_sidecart_shipping_bar_enabled', 1)) {
            return '';
        }

        // Don't show if cart is empty
        if ($cart->is_empty()) {
            return '';
        }

        $progress = $this->calculate_progress();

?>
        <div class="quantwp-shipping-bar-wrapper">
            <div class="quantwp-shipping-bar-message">
                <?php if ($progress['qualified']) : ?>
                    <span class="success-message">
                        <?php
                        printf(
                            /* translators: %s: The text 'Free Shipping' in bold */
                            esc_html__('🎉 You qualify for %s', 'quantwp-sidecart-for-woocommerce'),
                            '<strong>' . esc_html__('Free Shipping', 'quantwp-sidecart-for-woocommerce') . '</strong>'
                        );
                        ?>
                    </span>
                <?php else : ?>
                    <span class="progress-message">
                        <?php
                        printf(
                            /* translators: %1$s: Remaining amount, %2$s: Opening strong tag, %3$s: Closing strong tag */
                            esc_html__('Add %1$s more to get %2$sFREE Shipping%3$s', 'quantwp-sidecart-for-woocommerce'),
                            '<strong>' . wp_kses_post(wc_price($progress['remaining'])) . '</strong>',
                            '<strong>',
                            '</strong>'
                        );
                        ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="quantwp-shipping-bar-progress">
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" style="width: <?php echo esc_attr($progress['percentage']); ?>%;">
                    </div>
                </div>
            </div>
        </div>
<?php
    }

    public function shipping_bar_fragment($fragments)
    {
        ob_start();
        $this->render_shipping_bar_content();
        $fragments['.quantwp-shipping-bar-wrapper'] = ob_get_clean();

        return $fragments;
    }
}
