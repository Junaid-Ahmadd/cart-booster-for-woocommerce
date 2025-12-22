jQuery(document).ready(function($) {
    
    // Initialize carousel after fragments load
    $(document.body).on('wc_fragments_refreshed', function() {
        initCarousel();
    });
    
    // Also init on page load
    initCarousel();
    
    function initCarousel() {
        const $carousel = $('.cross-sells-carousel');
        
        if (!$carousel.length) {
            return;
        }
        
        const $track = $carousel.find('.carousel-track');
        const $items = $track.find('.cross-sell-item');
        const $prev = $('.carousel-prev');
        const $next = $('.carousel-next');
        
        if ($items.length === 0) {
            return;
        }
        
        let currentIndex = 0;
        const itemsToShow = 1;
        const maxIndex = Math.max(0, $items.length - itemsToShow);
        
        function updateButtons() {
            $prev.prop('disabled', currentIndex === 0);
            $next.prop('disabled', currentIndex >= maxIndex);
        }
        
        
        function moveCarousel() {
            const itemWidth = $items.first().outerWidth(true);
            const offset = -(currentIndex * itemWidth);
            $track.css('transform', 'translateX(' + offset + 'px)');
            updateButtons();
        }
        
        $next.off('click').on('click', function() {
            if (currentIndex < maxIndex) {
                currentIndex++;
                moveCarousel();
            }
        });
        
        $prev.off('click').on('click', function() {
            if (currentIndex > 0) {
                currentIndex--;
                moveCarousel();
            }
        });
        
        let resizeTimer;
        $(window).on('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                currentIndex = 0;
                moveCarousel();
            }, 250);
        });
        
        updateButtons();
    }
    
    // Handle add to cart from cross-sells
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        
        const $btn = $(this);
        const productId = $btn.data('product-id');
        const originalText = $btn.text();
        
        // Disable button and show loading
        $btn.prop('disabled', true).text('Adding...');
        
        // Add to cart
        $.ajax({
            url: wooSideCart.ajaxUrl,
            type: 'POST',
            data: {
                action: 'woocommerce_add_to_cart',
                product_id: productId,
                quantity: 1
            },
            success: function(response) {
                if (response.error) {
                    alert(response.error);
                    $btn.prop('disabled', false).text(originalText);
                } else {
                    // Show success
                    $btn.text('Added!');
                    
                    // Get fresh fragments
                    $.ajax({
                        url: wooSideCart.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: 'woocommerce_get_refreshed_fragments'
                        },
                        success: function(data) {
                            if (data && data.fragments) {
                                // Replace all fragments
                                $.each(data.fragments, function(selector, html) {
                                    $(selector).replaceWith(html);
                                });
                                
                                // Trigger WooCommerce events
                                $(document.body).trigger('added_to_cart', [
                                    data.fragments,
                                    data.cart_hash,
                                    $btn
                                ]);
                                
                                $(document.body).trigger('wc_fragments_refreshed');
                            }
                            
                            // Re-enable button
                            $btn.prop('disabled', false);
                            
                            // Reset text after delay
                            setTimeout(function() {
                                $btn.text(originalText);
                            }, 2000);
                        },
                        error: function() {
                            alert('Failed to refresh cart');
                            $btn.prop('disabled', false).text(originalText);
                        }
                    });
                }
            },
            error: function() {
                alert('Failed to add product');
                $btn.prop('disabled', false).text(originalText);
            }
        });
    });
    
});