/**
 * Side Cart JavaScript - OPTIMIZED with Debouncing
 */

(function ($) {
  'use strict';

  // Debounce timer
  let quantityUpdateTimer = null;
  let pendingUpdates = {};

  const QuantWPSideCart = {

    init: function () {
      this.bindEvents();
    },

    bindEvents: function () {
      const self = this;

      // Open/close cart
      $(document).on('click', '.quantwp-sidecart-trigger', function (e) {
        e.preventDefault();
        self.openCart();
      });

      $(document).on('click', '.quantwp-close-button, .quantwp-sidecart-overlay', function (e) {
        e.preventDefault();
        self.closeCart();
      });

      // Quantity buttons - WITH DEBOUNCING
      $(document).on('click', '.quantity-controls .qty-btn', function (e) {
        e.preventDefault();
        self.handleQuantityChange($(this));
      });

      // Remove item
      $(document).on('click', '.remove-item', function (e) {
        e.preventDefault();
        self.removeItem($(this));
      });

      // Auto-open on add to cart
      $(document.body).on('added_to_cart', function () {
        if (quantwpData.autoOpen) {
          self.openCart();
        }
      });
    },

    /**
     * Handle quantity change with DEBOUNCING to prevent CPU spikes
     */
    handleQuantityChange: function ($button) {
      const $controls = $button.closest('.quantity-controls');
      const $input = $controls.find('.qty-input');
      const cartKey = $controls.data('cart-key');
      const change = parseInt($button.data('qty-change'));
      const currentQty = parseInt($input.val());
      const newQty = Math.max(0, currentQty + change);

      // Update UI immediately for better UX
      $input.val(newQty);

      // Store pending update
      pendingUpdates[cartKey] = newQty;

      // Clear existing timer
      if (quantityUpdateTimer) {
        clearTimeout(quantityUpdateTimer);
      }

      // Set new timer - only fires after 500ms of no activity
      quantityUpdateTimer = setTimeout(() => {
        this.processPendingUpdates();
      }, 500);

      // Disable buttons during debounce
      $controls.addClass('updating');
    },

    /**
     * Process all pending quantity updates at once
     */
    processPendingUpdates: function () {
      const updates = { ...pendingUpdates };
      pendingUpdates = {};

      // Process each update
      Object.keys(updates).forEach(cartKey => {
        this.updateCart(cartKey, updates[cartKey]);
      });
    },

    /**
     * Update cart via AJAX
     */
    updateCart: function (cartKey, newQty) {
      const self = this;

      $.ajax({
        url: quantwpData.ajaxUrl,
        type: 'POST',
        data: {
          action: 'quantwp_update',
          nonce: quantwpData.nonce,
          cart_key: cartKey,
          new_qty: newQty
        },
        beforeSend: function () {
          $('.quantwp-sidecart-wrapper').addClass('loading');
        },
        success: function (response) {
          if (response.success && response.data.fragments) {
            self.updateFragments(response.data.fragments);

            // Trigger WooCommerce event for other plugins
            $(document.body).trigger('wc_fragment_refresh');
          }
        },
        error: function () {
          console.error('Failed to update cart');
        },
        complete: function () {
          $('.quantwp-sidecart-wrapper').removeClass('loading');
          $('.quantity-controls').removeClass('updating');
        }
      });
    },

    /**
     * Remove item from cart
     */
    removeItem: function ($button) {
      const cartKey = $button.data('cart-key');
      this.updateCart(cartKey, 0);
    },

    /**
     * Update all fragments
     */
    updateFragments: function (fragments) {
      $.each(fragments, function (key, value) {
        $(key).replaceWith(value);
      });
    },

    openCart: function () {
      $('.quantwp-sidecart-drawer').addClass('open');
      $('.quantwp-sidecart-overlay').addClass('active');
      $('body').addClass('quantwp-cart-open');
    },

    closeCart: function () {
      $('.quantwp-sidecart-drawer').removeClass('open');
      $('.quantwp-sidecart-overlay').removeClass('active');
      $('body').removeClass('quantwp-cart-open');
    }
  };

  // Initialize on document ready
  $(document).ready(function () {
    QuantWPSideCart.init();
  });

})(jQuery);
