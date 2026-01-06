jQuery(document).ready(function ($) {
  // Track if an update is in progress
  let isUpdating = false;

  // Open/close cart
  $(".quantwp-sidecart-trigger").click(function (e) {
    e.preventDefault();
    $("body").toggleClass("quantwp-sidecart-open");
  });

  // Use .on() for Event Delegation
  $(document).on(
    "click",
    ".quantwp-close-button, .quantwp-sidecart-overlay",
    function (e) {
      e.preventDefault();
      $("body").removeClass("quantwp-sidecart-open");
    }
  );

  // Listen to WooCommerce events
  $(document.body).on("added_to_cart", function () {
    refreshCart();

    // Auto-open if enabled in settings
    if (quantwpData.autoOpen) {
      $("body").addClass("quantwp-sidecart-open");
    }
  });

  // Refresh cart fragments
  function refreshCart() {
    $.ajax({
      url: quantwpData.ajaxUrl,
      type: "POST",
      data: {
        action: "woocommerce_get_refreshed_fragments",
      },
      success: function (data) {
        if (data && data.fragments) {
          $.each(data.fragments, function (selector, html) {
            $(selector).replaceWith(html);
          });
          $(document.body).trigger("wc_fragments_refreshed");
        }
      },
    });
  }

  // Refresh on page load
  refreshCart();

  // Listen to WooCommerce events
  $(document.body).on(
    "added_to_cart updated_wc_div updated_cart_totals removed_from_cart",
    function () {
      refreshCart();
    }
  );

  // Update quantity 
  $(document).on("click", ".qty-btn", function (e) {
    e.preventDefault();

    // Ignore if already updating
    if (isUpdating) {
      return;
    }

    const $btn = $(this);
    const $wrap = $btn.closest(".quantity-controls");
    const $input = $wrap.find(".qty-input");
    const cartKey = $wrap.data("cart-key");
    const change = parseInt($btn.data("qty-change"));
    let newQty = parseInt($input.val()) + change;

    if (newQty < 0) newQty = 0;

    $input.val(newQty);

    // Set updating flag
    isUpdating = true;

    $.ajax({
      type: "POST",
      url: quantwpData.ajaxUrl,
      data: {
        action: "quantwp_update",
        nonce: quantwpData.nonce,
        cart_key: cartKey,
        new_qty: newQty,
      },
      success: function (response) {
        if (response.success && response.data.fragments) {
          $.each(response.data.fragments, function (selector, html) {
            $(selector).replaceWith(html);
          });
          $(document.body).trigger("wc_fragments_refreshed");
        }
      },
      error: function () {
        alert("Failed to update cart");
      },
      complete: function () {
        // Reset flag after cart content is updated
        isUpdating = false;
      },
    });
  });

  // Remove item
  $(document).on("click", ".remove-item", function (e) {
    e.preventDefault();

    // Ignore if already updating
    if (isUpdating) {
      return;
    }

    const cartKey = $(this).data("cart-key");

    $.ajax({
      type: "POST",
      url: quantwpData.ajaxUrl,
      data: {
        action: "quantwp_update",
        nonce: quantwpData.nonce,
        cart_key: cartKey,
        new_qty: 0,
      },
      success: function (response) {
        if (response.success && response.data.fragments) {
          $.each(response.data.fragments, function (selector, html) {
            $(selector).replaceWith(html);
          });
          $(document.body).trigger("wc_fragments_refreshed");
        }
      },
      error: function () {
        alert("Failed to remove item");
      },
      complete: function () {
        // Reset flag after cart content is updated
        isUpdating = false;
      },
    });
  });
});
