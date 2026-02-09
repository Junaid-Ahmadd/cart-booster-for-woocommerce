<?php

/**
 * Cross-sells Product Class - OPTIMIZED VERSION
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuantWP_SideCart_Cross_Sells
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

    public function init_hooks()
    {
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));

        // Add cross-sells to side cart (after shipping bar)
        add_action('quantwp_sidecart_after_cart_items', array($this, 'render_empty_wrapper'), 20);

        // Add cross-sells to fragments
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'cross_sells_fragment'));
        
        // Clear cache when cart changes significantly
        add_action('woocommerce_cart_item_removed', array($this, 'clear_cross_sell_cache'));
        add_action('woocommerce_add_to_cart', array($this, 'clear_cross_sell_cache'));
        
        // Clear cache when settings are saved
        add_action('update_option_quantwp_sidecart_cross_sells_limit', array($this, 'clear_cross_sell_cache'));
        add_action('update_option_quantwp_sidecart_cross_sells_enabled', array($this, 'clear_cross_sell_cache'));
    }

    public function enqueue_assets()
    {
        wp_enqueue_style(
            'quantwp-sidecart',
            QUANTWP_URL . 'assets/css/side-cart.css',
            array(),
            QUANTWP_VERSION
        );

        wp_enqueue_script(
            'quantwp-cross-sells',
            QUANTWP_URL . 'assets/js/cross-sells.js',
            array('jquery', 'quantwp-sidecart'),
            QUANTWP_VERSION,
            true
        );
    }

    /**
     * Clear cross-sell cache
     */
    public function clear_cross_sell_cache()
    {
        // Get current user's cart hash
        $cart_hash = WC()->cart->get_cart_hash();
        if ($cart_hash) {
            delete_transient('quantwp_cross_sells_' . $cart_hash);
        }
    }

    /**
     * Get Cross Sell Product Ids for all cart items - WITH CACHING
     */
    public function get_cross_sell_ids()
    {
        $cart = WC()->cart;

        if ($cart->is_empty()) {
            return array();
        }

        // Create cache key based on cart contents
        $cart_hash = $cart->get_cart_hash();
        $cache_key = 'quantwp_cross_sells_' . $cart_hash;
        
        // Try to get from cache first
        $cached_ids = get_transient($cache_key);
        if (false !== $cached_ids) {
            return $cached_ids;
        }

        // If not cached, calculate
        $cross_sell_ids = array();
        $cart_product_ids = array();

        // Collect cart product IDs first (single loop)
        foreach ($cart->get_cart() as $cart_item) {
            $cart_product_ids[] = $cart_item['product_id'];
        }

        // Loop through cart items to get cross-sells
        foreach ($cart->get_cart() as $cart_item) {
            $_product = $cart_item['data'];

            if (!$_product) {
                continue;
            }

            // Get cross-sells for this product
            $product_cross_sells = $_product->get_cross_sell_ids();

            if (!empty($product_cross_sells)) {
                $cross_sell_ids = array_merge($cross_sell_ids, $product_cross_sells);
            }
        }

        // Remove duplicates
        $cross_sell_ids = array_unique($cross_sell_ids);

        // Remove products already in cart
        $cross_sell_ids = array_diff($cross_sell_ids, $cart_product_ids);

        // Get limit from settings
        $limit = absint(get_option('quantwp_sidecart_cross_sells_limit', 6));
        $cross_sell_ids = array_slice($cross_sell_ids, 0, $limit);

        // Cache for 1 hour (or until cart changes)
        set_transient($cache_key, $cross_sell_ids, HOUR_IN_SECONDS);

        return $cross_sell_ids;
    }

    /**
     * Get cross sell products - OPTIMIZED to avoid N+1 queries
     */
    public function get_cross_sell_products()
    {
        $product_ids = $this->get_cross_sell_ids();

        if (empty($product_ids)) {
            return array();
        }

        // OPTIMIZATION: Use WP_Query to get all products at once instead of individual calls
        $args = array(
            'post_type' => 'product',
            'post__in' => $product_ids,
            'posts_per_page' => count($product_ids),
            'orderby' => 'post__in', // Maintain the order
            'no_found_rows' => true, // Don't count total rows (performance)
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        );

        $query = new WP_Query($args);
        $products = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product = wc_get_product(get_the_ID());

                if ($product && $product->is_visible()) {
                    $products[] = $product;
                }
            }
            wp_reset_postdata();
        }

        return $products;
    }

    /**
     *  Render empty wrapper (for caching)
     */
    public function render_empty_wrapper()
    {
        // Check if cross-sells enabled
        if (!get_option('quantwp_sidecart_cross_sells_enabled', 1)) {
            return;
        }

        $cart = WC()->cart;

        // Only show if cart has items
        if ($cart->is_empty()) {
            return;
        }

        echo '<div class="quantwp-cross-sells-wrapper"></div>';
    }

    /**
     * Render cross-sell carousel content
     */
    public function render_cross_sells()
    {
        // Check if cross-sells enabled
        if (!get_option('quantwp_sidecart_cross_sells_enabled', 1)) {
            return '';
        }

        $products = $this->get_cross_sell_products();

        if (empty($products)) {
            return '';
        }

        ob_start();
?>
        <div class="quantwp-cross-sells-wrapper">
            <div class="cross-sells-header">
                <h4><?php esc_html_e('You may also like', 'quantwp-sidecart-for-woocommerce'); ?></h4>
            </div>

            <div class="cross-sells-carousel">
                <button class="carousel-prev" aria-label="Previous">&lsaquo;</button>
                <div class="carousel-track">
                    <?php foreach ($products as $product) : ?>
                        <div class="cross-sell-item">
                            <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-image">
                                <?php echo wp_kses_post($product->get_image('thumbnail')); ?>
                            </a>

                            <div class="product-details">
                                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="product-name">
                                    <?php echo esc_html($product->get_name()); ?>
                                </a>

                                <div class="product-price">
                                    <?php echo wp_kses_post($product->get_price_html()); ?>
                                </div>

                                <a href="<?php echo esc_url('?add-to-cart=' . $product->get_id()); ?>"
                                    class="add-to-cart-btn"
                                    data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                    <?php esc_html_e('ADD', 'quantwp-sidecart-for-woocommerce'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="carousel-next" aria-label="Next">&rsaquo;</button>
            </div>
        </div>
<?php
        return ob_get_clean();
    }

    /**
     * Fragment for AJAX updates
     */
    public function cross_sells_fragment($fragments)
    {
        $fragments['.quantwp-cross-sells-wrapper'] = $this->render_cross_sells();
        return $fragments;
    }
}
