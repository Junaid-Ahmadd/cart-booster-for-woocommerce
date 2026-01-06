<?php

/**
 * Cross-sells Product Class
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
     * Get Cross Sell Product Ids for all cart items
     */
    public function get_cross_sell_ids()
    {
        $cart = WC()->cart;

        if ($cart->is_empty()) {
            return array();
        }

        $cross_sell_ids = array();

        // Loop through cart items
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
        $cart_product_ids = array();
        foreach ($cart->get_cart() as $cart_item) {
            $cart_product_ids[] = $cart_item['product_id'];
        }
        $cross_sell_ids = array_diff($cross_sell_ids, $cart_product_ids);

        // Get limit from settings
        $limit = absint(get_option('quantwp_sidecart_cross_sells_limit', 6));
        $cross_sell_ids = array_slice($cross_sell_ids, 0, $limit);

        return $cross_sell_ids;
    }

    /**
     * Get cross sell products
     */
    public function get_cross_sell_products()
    {

        $product_ids = $this->get_cross_sell_ids();

        if (empty($product_ids)) {
            return array();
        }

        $products = array();

        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);

            if (!$product || !$product->is_visible()) {
                continue;
            }

            $products[] = $product;
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
            return;
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
