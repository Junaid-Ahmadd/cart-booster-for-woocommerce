=== QuantWP – Side Cart for WooCommerce ===
Contributors: junaidahmadd
Tags: woocommerce, cart, side cart, ajax cart
Requires at least: 5.8
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv2 or later

A lightweight WooCommerce side cart with free shipping bar and cross-sells.

== Description ==
QuantWP Side Cart for WooCommerce is a powerful, lightweight drawer that improves user experience and boosts Average Order Value (AOV).

= Key Features =
* **Auto-Open Cart:** The side cart automatically slides out the moment a customer adds an item, reducing friction and showing progress immediately.
* **Dynamic Shipping Progress Bar:** Motivate customers to spend more by showing how close they are to free shipping.
* **Cross-Sell Carousel:** Increase sales by displaying relevant product recommendations directly in the cart.
* **Customizable Cart Icons:** Choose from a library of professional icons to match your store's branding.
* **Shortcode Support:** Use `[quantwp_cart_shortcode]` to place your cart trigger anywhere—header, footer, or custom pages.

== Installation ==
1. Search for "QuantWP Side Cart" in your WordPress dashboard or upload the folder to `/wp-content/plugins/`.
2. Activate the plugin.
3. Go to **Settings > QuantWP Side Cart** to configure your options.
4. **Placement:** Add the shortcode `[quantwp_cart_shortcode]` to your site.
   - **Theme Customizer:** Use an "HTML" or "Shortcode" widget in your Header/Footer builder.
   - **Page Builders:** Use the "Shortcode" widget in Elementor, Divi, or Beaver Builder.

== Frequently Asked Questions ==

= How do I sync the Shipping Bar with WooCommerce Free Shipping? =
Our shipping bar acts as a visual mirror. To ensure it matches your checkout:
1. Go to **WooCommerce > Settings > Shipping** and create a "Free Shipping" method.
2. Note the "Minimum order amount" you set there.
3. Go to **Settings > QuantWP Side Cart** and enter that same number in the "Free Shipping Threshold" field.
Now, the progress bar will reach 100% exactly when WooCommerce activates free shipping at checkout.

= How do I set up the Cross-Sell Carousel? =
The carousel pulls data directly from WooCommerce:
1. Edit any product in your store.
2. Go to **Linked Products > Cross-sells**.
3. Add the products ( in cross-sell field currently) you want to recommend.
If an item in the cart has cross-sells assigned, they will appear in the side cart carousel.

= How does the Cross-Sell Limit work? =
If a customer has multiple items in their cart, the total number of cross-sell recommendations could become very large. Use the **Cross-Sell Limit** field in our settings to keep the drawer clean. For example, if you set the limit to "5", we will only show the first 5 available cross-sells, even if 30 are assigned.

= Where can I use the cart shortcode? =
You can use `[quantwp_cart_shortcode]` anywhere shortcodes are accepted. It is compatible with all major theme header builders and page builders like Elementor.

== Changelog ==

= 2.0.0 =
* Fix: Fixed CPU spike on settings save with heavy fragments cache and transient api.

= 1.0.3 =
* Fix: Resolved issue where shipping threshold color was linked to carousel background.

= 1.0.2 =
* **New:** Added "Appearance" settings for easy color customization (Checkout Button, Carousel Background, Text).
* **New:** Added "Start Shopping" button when the cart is empty.
* **Enhancement:** Added helpful tooltips and info boxes to the settings page for better guidance.
* **Enhancement:** Implemented CSS variables for lightweight dynamic styling.

= 1.0.1 =
* **Fix:** Resolved issue with the "Settings" link on the plugins page.
* **Documentation:** Updated README with detailed instructions for the Shipping Bar and Cross-sell features.

= 1.0.0 =
* Initial release.
```


---
