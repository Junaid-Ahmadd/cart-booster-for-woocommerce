<?php

/**
 * Side Cart Main Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class QuantWP_SideCart_Main
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

        // Output cart HTML
        add_action('wp_footer', array($this, 'quantwp_render_cart_html'));

        // AJAX handlers
        add_action('wp_ajax_quantwp_update', array($this, 'quantwp_ajax_update_cart'));
        add_action('wp_ajax_nopriv_quantwp_update', array($this, 'quantwp_ajax_update_cart'));

        // Register fragments
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'quantwp_cart_fragment'));

        // Add shortcode for cart icon
        add_shortcode('quantwp_cart_shortcode', array($this, 'quantwp_icon_shortcode'));
    }

    public function enqueue_assets()
    {
        // JavaScript
        wp_enqueue_script(
            'quantwp-sidecart',
            QUANTWP_URL . 'assets/js/side-cart.js',
            array('jquery'),
            QUANTWP_VERSION,
            true
        );

        // CSS
        wp_enqueue_style(
            'quantwp-sidecart',
            QUANTWP_URL . 'assets/css/side-cart.css',
            array(),
            QUANTWP_VERSION
        );

        // Localize script
        wp_localize_script('quantwp-sidecart', 'quantwpData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quantwp_sidecart_nonce'),
            'autoOpen' => (bool) get_option('quantwp_sidecart_auto_open', 1),
        ));

        // 1. Get sanitize colors from database
            $threshold_color = sanitize_hex_color(
            get_option('quantwp_sidecart_carousel_background_color', '#92C1E9')
        );
        $carousel_bg = sanitize_hex_color(
            get_option('quantwp_sidecart_carousel_background_color', '#E0F1FF')
        );
        $btn_bg = sanitize_hex_color(
            get_option('quantwp_sidecart_checkout_btn_bg', '#F87C56')
        );

        // 2. Fallback to defaults if sanitization fails
        $threshold_color = $threshold_color ?: '#92C1E9';
        $carousel_bg = $carousel_bg ?: '#E0F1FF';
        $btn_bg = $btn_bg ?: '#F87C56';

        // 3. Create CSS with sanitized values
        $custom_css = "
        :root {
        --quantwp-threshold-color: {$threshold_color};
        --quantwp-carousel-bg: {$carousel_bg};
        --quantwp-btn-bg: {$btn_bg};
         }
        ";

        // 5. Inject into page
        wp_add_inline_style('quantwp-sidecart', $custom_css);
    }

    public function quantwp_render_cart_html()
    {

?>

        <div class="quantwp-sidecart-overlay"></div>

        <div class="quantwp-sidecart-drawer">
            <div class="quantwp-sidecart-wrapper">
                <?php echo $this->get_cart_content_html(); ?>
            </div>
        </div>
    <?php
    }

    public function get_cart_content_html()
    {
        $cart = WC()->cart;
        $cart_items = $cart->get_cart();
        $cart_has_items = !$cart->is_empty();

        ob_start();
    ?>
        <header class="quantwp-sidecart-header">
            <h4 class="quantwp-sidecart-title">
                <?php
                printf(
                    /* translators: %d: The number of items in the cart */
                    esc_html__('Cart (%d)', 'quantwp-sidecart-for-woocommerce'),
                    absint($cart->get_cart_contents_count())
                );
                ?>
            </h4>
            <button class="quantwp-close-button" type="button">&times;</button>
        </header>

        <?php
        // Shipping bar will be added here by Shipping_Bar class
        do_action('quantwp_sidecart_after_header');
        ?>

        <div class="quantwp-sidecart-content">
            <?php if ($cart_has_items) : ?>
                <?php foreach ($cart_items as $cart_item_key => $cart_item) : ?>
                    <?php
                    $_product = $cart_item['data'];

                    if (!$_product || !$_product->exists()) {
                        continue;
                    }
                    ?>
                    <div class="quantwp-sidecart-item">
                        <div class="quantwp-sidecart-item-image">
                            <?php echo wp_kses_post($_product->get_image('thumbnail')); ?>
                        </div>

                        <div class="quantwp-sidecart-item-details">
                            <a href="<?php echo esc_url($_product->get_permalink()); ?>" class="product-name">
                                <?php
                                // If it's a variation, show just the parent name (e.g. "T-Shirt")
                                echo esc_html($_product->is_type('variation') ? $_product->get_parent_data()['title'] : $_product->get_name());
                                ?>
                            </a>

                            <?php if (isset($cart_item['variation'])) {
                                echo wc_get_formatted_variation($cart_item['variation'], false);
                            } ?>

                            <div class="quantwp-sidecart-item-details-inner">
                                <div class="quantity-controls" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <button class="qty-btn minus" data-qty-change="-1">-</button>
                                    <input type="number" class="qty-input" value="<?php echo esc_attr($cart_item['quantity']); ?>" readonly>
                                    <button class="qty-btn plus" data-qty-change="1">+</button>
                                </div>

                                <button class="remove-item" data-cart-key="<?php echo esc_attr($cart_item_key); ?>">
                                    <svg viewBox="0 0 50 50" fill="currentColor">
                                        <path d="M10.289 14.211h3.102l1.444 25.439c0.029 0.529 0.468 0.943 0.998 0.943h18.933 c0.53 0 0.969-0.415 0.998-0.944l1.421-25.438h3.104c0.553 0 1-0.448 1-1s-0.447-1-1-1h-3.741c-0.055 0-0.103 0.023-0.156 0.031 c-0.052-0.008-0.1-0.031-0.153-0.031h-5.246V9.594c0-0.552-0.447-1-1-1h-9.409c-0.553 0-1 0.448-1 1v2.617h-5.248 c-0.046 0-0.087 0.021-0.132 0.027c-0.046-0.007-0.087-0.027-0.135-0.027h-3.779c-0.553 0-1 0.448-1 1S9.736 14.211 10.289 14.211z M21.584 10.594h7.409v1.617h-7.409V10.594z M35.182 14.211L33.82 38.594H16.778l-1.384-24.383H35.182z" />
                                        <path d="M20.337 36.719c0.02 0 0.038 0 0.058-0.001c0.552-0.031 0.973-0.504 0.941-1.055l-1.052-18.535 c-0.031-0.552-0.517-0.967-1.055-0.942c-0.552 0.031-0.973 0.504-0.941 1.055l1.052 18.535 C19.37 36.308 19.811 36.719 20.337 36.719z" />
                                        <path d="M30.147 36.718c0.02 0.001 0.038 0.001 0.058 0.001c0.526 0 0.967-0.411 0.997-0.943l1.052-18.535 c0.031-0.551-0.39-1.024-0.941-1.055c-0.543-0.023-1.023 0.39-1.055 0.942l-1.052 18.535C29.175 36.214 29.596 36.687 30.147 36.718 z" />
                                        <path d="M25.289 36.719c0.553 0 1-0.448 1-1V17.184c0-0.552-0.447-1-1-1s-1 0.448-1 1v18.535 C24.289 36.271 24.736 36.719 25.289 36.719z" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div class="quantwp-sidecart-item-price">
                            <?php
                            // 1. Check if the product is on sale
                            if ($_product->is_on_sale()) {

                                // 2. Calculate the Regular Price Subtotal (Raw Regular Price * Quantity)
                                // We use wc_price() to format it with your currency settings
                                $regular_price = $_product->get_regular_price();
                                $regular_subtotal = wc_price($regular_price * $cart_item['quantity']);

                                // 3. Get the Active Price Subtotal (Sale Price) using the standard function
                                // This handles taxes/settings automatically
                                $active_subtotal = $cart->get_product_subtotal($_product, $cart_item['quantity']);

                                // 4. Output: <del>Regular</del> <ins>Sale</ins>
                                echo '<ins class="sale-price" style="text-decoration: none; margin-left: 5px;">' . wp_kses_post($active_subtotal) . '</ins>';
                                echo '<del class="original-price">' . wp_kses_post($regular_subtotal) . '</del>';
                            } else {
                                // Not on sale? Just show the standard subtotal
                                echo wp_kses_post($cart->get_product_subtotal($_product, $cart_item['quantity']));
                            }
                            ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="quantwp-empty-state">
                    <p class="empty-cart-message">
                        <?php esc_html_e('Your cart is empty', 'quantwp-sidecart-for-woocommerce'); ?>
                    </p>
                    <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="quantwp-shop-button">
                        <?php esc_html_e('Checkout Our Best Sellers', 'quantwp-sidecart-for-woocommerce'); ?>
                    </a>
                </div>
            <?php endif; ?>
            <?php
            // Cross Sells will be added here by Cross_Sells class
            do_action('quantwp_sidecart_after_cart_items');
            ?>
        </div>



        <footer class="quantwp-sidecart-footer">
            <?php if ($cart_has_items) : ?>
                <div class="cart-subtotal">
                    <span><?php esc_html_e('Subtotal:', 'quantwp-sidecart-for-woocommerce'); ?></span>
                    <span><?php echo wp_kses_post(wc_price($cart->get_subtotal())); ?></span>
                </div>

                <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="checkout-button">
                    <?php esc_html_e('Checkout', 'quantwp-sidecart-for-woocommerce'); ?>
                </a>
            <?php endif; ?>
        </footer>

    <?php
        return ob_get_clean();
    }

    public function quantwp_cart_fragment($fragments)
    {
        ob_start();
    ?>
        <div class="quantwp-sidecart-wrapper">
            <?php echo $this->get_cart_content_html(); ?>
        </div>
    <?php
        $fragments['.quantwp-sidecart-wrapper'] = ob_get_clean();

        // 2. Update the Cart Count Badge (ALWAYS SHOW)
        $count = WC()->cart->get_cart_contents_count();
        $fragments['.cart-count-badge'] = '<span class="cart-count-badge">' . esc_html($count) . '</span>';

        return $fragments;
    }


    public function quantwp_ajax_update_cart()
    {
        check_ajax_referer('quantwp_sidecart_nonce', 'nonce');

        if (!isset($_POST['cart_key']) || !isset($_POST['new_qty'])) {
            wp_send_json_error(array('message' => 'Missing data'));
        }

        $cart_key = sanitize_text_field(wp_unslash($_POST['cart_key']));
        $new_qty = absint($_POST['new_qty']);

        if ($new_qty === 0) {
            WC()->cart->remove_cart_item($cart_key);
        } else {
            WC()->cart->set_quantity($cart_key, $new_qty);
        }

        WC()->cart->calculate_totals();

        // Manually build all fragments by calling your own methods
        $fragments = array();

        // Cart content fragment
        ob_start();
    ?>
        <div class="quantwp-sidecart-wrapper">
            <?php echo $this->get_cart_content_html(); ?>
        </div>
    <?php
        $fragments['.quantwp-sidecart-wrapper'] = ob_get_clean();

        //  Update Cart Count Badge fragment 
        $count = WC()->cart->get_cart_contents_count();
        $fragments['.cart-count-badge'] = '<span class="cart-count-badge">' . esc_html($count) . '</span>';

        // Trigger other classes to add their fragments
        // Shipping bar
        $shipping_bar = QuantWP_SideCart_Shipping_Bar::get_instance();
        ob_start();
        $shipping_bar->render_shipping_bar_content();
        $fragments['.quantwp-shipping-bar-wrapper'] = ob_get_clean();

        // Cross-sells
        $cross_sells = QuantWP_SideCart_Cross_Sells::get_instance();
        $fragments['.quantwp-cross-sells-wrapper'] = $cross_sells->render_cross_sells();

        // Allow other plugins to add their own fragments
        $fragments = apply_filters('quantwp_sidecart_fragments', $fragments);

        wp_send_json_success(array(
            'fragments' => $fragments,
            'cart_hash' => WC()->cart->get_cart_hash(),
            'cart_count' => WC()->cart->get_cart_contents_count()
        ));
    }

    /**
     * Shortcode for cart icon: [quantwp_cart_shortcode]
     */
    public function quantwp_icon_shortcode()
    {

        $cart_count = WC()->cart->get_cart_contents_count();
        $icon_key = get_option('quantwp_sidecart_icon', 'cart-classic');
        $icons = QuantWP_SideCart_Settings::get_cart_icons();
        $svg = isset($icons[$icon_key]) ? $icons[$icon_key] : $icons['cart-classic'];

        $allowed_svg = array(
            'svg' => array('viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'class' => true, 'width' => true, 'height' => true, 'version' => true),
            'path' => array('d' => true, 'fill' => true, 'stroke' => true, 'fill-rule' => true, 'clip-rule' => true),
            'circle' => array('cx' => true, 'cy' => true, 'r' => true, 'stroke' => true, 'fill' => true),
            'g' => array('fill' => true, 'stroke' => true),
        );

        ob_start();
    ?>
        <a href="#" class="quantwp-sidecart-trigger" aria-label="<?php esc_attr_e('View Cart', 'quantwp-sidecart-for-woocommerce'); ?>">
            <?php echo wp_kses($svg, $allowed_svg); ?>
            <?php if ($cart_count > 0) : ?>
                <span class="cart-count-badge"><?php echo esc_html($cart_count); ?></span>
            <?php endif; ?>
        </a>
<?php
        return ob_get_clean();
    }
}
