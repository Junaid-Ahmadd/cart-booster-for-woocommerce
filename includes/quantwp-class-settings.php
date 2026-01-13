<?php

if (!defined('ABSPATH')) {
    exit;
}

class QuantWP_SideCart_Settings
{

    protected static $instance = null;

    private $option_group = 'quantwp_sidecart_settings';
    private $page_slug = 'quantwp_sidecart_settings';

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
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        // Add settings link to plugins page
        add_filter('plugin_action_links_' . QUANTWP_BASENAME, array($this, 'add_action_links'));
    }

    // Function which adds the settings link to the plugins page
    public function add_action_links($links)
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=' . $this->page_slug) . '">' . __('Settings', 'quantwp-sidecart-for-woocommerce') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Define the SVG Icon Library
     * Made 'public static' so the frontend class can access it safely.
     */
    public static function get_cart_icons()
    {
        return [
            // Standard Carts
            'cart-classic' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',

            'cart-2' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M4.78571 5H18.2251C19.5903 5 20.5542 6.33739 20.1225 7.63246L18.4558 12.6325C18.1836 13.4491 17.4193 14 16.5585 14H6.07142M4.78571 5L4.74531 4.71716C4.60455 3.73186 3.76071 3 2.76541 3H2M4.78571 5L6.07142 14M6.07142 14L6.25469 15.2828C6.39545 16.2681 7.23929 17 8.23459 17H17M17 17C15.8954 17 15 17.8954 15 19C15 20.1046 15.8954 21 17 21C18.1046 21 19 20.1046 19 19C19 17.8954 18.1046 17 17 17ZM11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"/></svg>',

            'cart-3' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3H4.5L6.5 17H17C18.1046 17 19 17.8954 19 19C19 20.1046 18.1046 21 17 21C15.8954 21 15 20.1046 15 19M9 5H21.0001L19.0001 11M18 14H6.07141M11 19C11 20.1046 10.1046 21 9 21C7.89543 21 7 20.1046 7 19C7 17.8954 7.89543 17 9 17C10.1046 17 11 17.8954 11 19Z"/></svg>',

            'cart-4' => '<svg viewBox="0 0 1024 1024" fill="currentColor" version="1.1"><path d="M800.8 952c-31.2 0-56-24.8-56-56s24.8-56 56-56 56 24.8 56 56-25.6 56-56 56z m-448 0c-31.2 0-56-24.8-56-56s24.8-56 56-56 56 24.8 56 56-25.6 56-56 56zM344 792c-42.4 0-79.2-33.6-84-76l-54.4-382.4-31.2-178.4c-2.4-19.2-19.2-35.2-37.6-35.2H96c-13.6 0-24-10.4-24-24s10.4-24 24-24h40.8c42.4 0 80 33.6 85.6 76l31.2 178.4 54.4 383.2C309.6 728 326.4 744 344 744h520c13.6 0 24 10.4 24 24s-10.4 24-24 24H344z m40-128c-12.8 0-23.2-9.6-24-22.4-0.8-6.4 1.6-12.8 5.6-17.6s10.4-8 16-8l434.4-32c19.2 0 36-15.2 38.4-33.6l50.4-288c1.6-13.6-2.4-28-10.4-36.8-5.6-6.4-12.8-9.6-21.6-9.6H320c-13.6 0-24-10.4-24-24s10.4-24 24-24h554.4c22.4 0 42.4 9.6 57.6 25.6 16.8 19.2 24.8 47.2 21.6 75.2l-50.4 288c-4.8 41.6-42.4 74.4-84 74.4l-432 32c-1.6 0.8-2.4 0.8-3.2 0.8z"/></svg>',

            'cart-5' => '<svg viewBox="0 0 24 24" fill="none"><path d="M5.33331 6H19.8672C20.4687 6 20.9341 6.52718 20.8595 7.12403L20.1095 13.124C20.0469 13.6245 19.6215 14 19.1172 14H16.5555H9.44442H7.99998" stroke="currentColor" stroke-linejoin="round"/><path d="M2 4H4.23362C4.68578 4 5.08169 4.30341 5.19924 4.74003L8.30076 16.26C8.41831 16.6966 8.81422 17 9.26638 17H19" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10" cy="20" r="1" stroke="currentColor" stroke-linejoin="round"/><circle cx="17.5" cy="20" r="1" stroke="currentColor" stroke-linejoin="round"/></svg>',

            'cart-6' => '<svg viewBox="0 -0.5 25 25" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path fill-rule="evenodd" clip-rule="evenodd" d="M18.194 7.55504H8.76001L9.41201 13.944L16.7 13.214C17.1551 13.2156 17.5783 12.9809 17.818 12.594L19.312 9.49404C19.5529 9.09564 19.5581 8.59777 19.3255 8.19445C19.093 7.79112 18.6595 7.54617 18.194 7.55504Z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M10.167 19.063C10.1648 19.5777 9.74612 19.9934 9.23136 19.992C8.7166 19.9905 8.30029 19.5724 8.30103 19.0576C8.30176 18.5429 8.71926 18.126 9.23402 18.126C9.48199 18.1265 9.7196 18.2255 9.89458 18.4012C10.0695 18.577 10.1675 18.815 10.167 19.063V19.063Z"/><path fill-rule="evenodd" clip-rule="evenodd" d="M15.767 19.063C15.7648 19.5777 15.3461 19.9934 14.8313 19.992C14.3166 19.9905 13.9003 19.5724 13.901 19.0576C13.9017 18.5429 14.3192 18.126 14.834 18.126C15.082 18.1265 15.3196 18.2255 15.4946 18.4012C15.6695 18.577 15.7675 18.815 15.767 19.063V19.063Z"/><path d="M8.03326 7.74034C8.13561 8.14171 8.54395 8.38411 8.94532 8.28176C9.34669 8.17941 9.58909 7.77106 9.48674 7.36969L8.03326 7.74034ZM8.136 5.10801L8.86281 4.92267L8.86017 4.91288L8.136 5.10801ZM7.993 5.00001V5.75009L8.00342 5.74994L7.993 5.00001ZM5.5 4.25001C5.08579 4.25001 4.75 4.5858 4.75 5.00001C4.75 5.41423 5.08579 5.75001 5.5 5.75001V4.25001ZM9.44322 14.6934C9.85707 14.6761 10.1786 14.3267 10.1614 13.9128C10.1441 13.4989 9.79464 13.1774 9.38078 13.1947L9.44322 14.6934ZM9.412 16.25L9.38078 16.9994C9.39118 16.9998 9.40159 17 9.412 17L9.412 16.25ZM16.054 17C16.4682 17 16.804 16.6642 16.804 16.25C16.804 15.8358 16.4682 15.5 16.054 15.5V17ZM9.48674 7.36969L8.86274 4.92269L7.40926 5.29334L8.03326 7.74034L9.48674 7.36969ZM8.86017 4.91288C8.75358 4.51729 8.39224 4.2444 7.98258 4.25009L8.00342 5.74994C7.72726 5.75378 7.48369 5.56982 7.41183 5.30315L8.86017 4.91288ZM7.993 4.25001H5.5V5.75001H7.993V4.25001ZM9.38078 13.1947C8.36094 13.2371 7.55603 14.0763 7.55603 15.097H9.05603C9.05603 14.8804 9.22682 14.7024 9.44322 14.6934L9.38078 13.1947ZM7.55603 15.097C7.55603 16.1177 8.36094 16.9569 9.38078 16.9994L9.44322 15.5007C9.22682 15.4916 9.05603 15.3136 9.05603 15.097H7.55603ZM9.412 17H16.054V15.5H9.412V17Z"/></svg>',

            'cart-7' => '<svg viewBox="0 0 24 24" fill="none"><path d="M3.86376 16.4552C3.00581 13.0234 2.57684 11.3075 3.47767 10.1538C4.3785 9 6.14721 9 9.68462 9H14.3153C17.8527 9 19.6214 9 20.5222 10.1538C21.4231 11.3075 20.9941 13.0234 20.1362 16.4552C19.5905 18.6379 19.3176 19.7292 18.5039 20.3646C17.6901 21 16.5652 21 14.3153 21H9.68462C7.43476 21 6.30983 21 5.49605 20.3646C4.68227 19.7292 4.40943 18.6379 3.86376 16.4552Z" stroke="currentColor" stroke-width="1.5"/><path d="M19.5 9.5L18.7896 6.89465C18.5157 5.89005 18.3787 5.38775 18.0978 5.00946C17.818 4.63273 17.4378 4.34234 17.0008 4.17152C16.5619 4 16.0413 4 15 4M4.5 9.5L5.2104 6.89465C5.48432 5.89005 5.62128 5.38775 5.90221 5.00946C6.18199 4.63273 6.56216 4.34234 6.99922 4.17152C7.43808 4 7.95872 4 9 4" stroke="currentColor" stroke-width="1.5"/><path d="M9 4C9 3.44772 9.44772 3 10 3H14C14.5523 3 15 3.44772 15 4C15 4.55228 14.5523 5 14 5H10C9.44772 5 9 4.55228 9 4Z" stroke="currentColor" stroke-width="1.5"/><path d="M8 13V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 13V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 13V17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',

            'cart-8' => '<svg viewBox="0 0 24 24" fill="none"><path d="M2 3L2.26491 3.0883C3.58495 3.52832 4.24497 3.74832 4.62248 4.2721C5 4.79587 5 5.49159 5 6.88304V9.5C5 12.3284 5 13.7426 5.87868 14.6213C6.75736 15.5 8.17157 15.5 11 15.5H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" stroke="currentColor" stroke-width="1.5"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" stroke="currentColor" stroke-width="1.5"/><path d="M11 9H8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M5 6H16.4504C18.5054 6 19.5328 6 19.9775 6.67426C20.4221 7.34853 20.0173 8.29294 19.2078 10.1818L18.7792 11.1818C18.4013 12.0636 18.2123 12.5045 17.8366 12.7523C17.4609 13 16.9812 13 16.0218 13H5" stroke="currentColor" stroke-width="1.5"/></svg>',

            'cart-9' => '<svg viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.99976 2.25C9.30136 2.25 8.69851 2.65912 8.4178 3.25077C7.73426 3.25574 7.20152 3.28712 6.72597 3.47298C6.15778 3.69505 5.66357 4.07255 5.29985 4.5623C4.93292 5.05639 4.76067 5.68968 4.5236 6.56133L4.47721 6.73169L3.96448 9.69473C3.77895 9.82272 3.61781 9.97428 3.47767 10.1538C2.57684 11.3075 3.00581 13.0234 3.86376 16.4552C4.40943 18.6379 4.68227 19.7292 5.49605 20.3646C6.30983 21 7.43476 21 9.68462 21H14.3153C16.5652 21 17.6901 21 18.5039 20.3646C19.3176 19.7292 19.5905 18.6379 20.1362 16.4552C20.9941 13.0234 21.4231 11.3075 20.5222 10.1538C20.382 9.97414 20.2207 9.82247 20.035 9.69442L19.5223 6.73169L19.4759 6.56133C19.2388 5.68968 19.0666 5.05639 18.6997 4.5623C18.336 4.07255 17.8417 3.69505 17.2736 3.47298C16.798 3.28712 16.2653 3.25574 15.5817 3.25077C15.301 2.65912 14.6982 2.25 13.9998 2.25H9.99976ZM18.4177 9.14571L18.0564 7.05765C17.7726 6.01794 17.6696 5.69121 17.4954 5.45663C17.2996 5.19291 17.0335 4.98964 16.7275 4.87007C16.5077 4.78416 16.2421 4.75888 15.5803 4.75219C15.299 5.34225 14.697 5.75 13.9998 5.75H9.99976C9.30252 5.75 8.70052 5.34225 8.41921 4.75219C7.75738 4.75888 7.4918 4.78416 7.272 4.87007C6.96605 4.98964 6.69994 5.19291 6.50409 5.45662C6.32988 5.6912 6.22688 6.01794 5.9431 7.05765L5.58176 9.14577C6.57992 9 7.9096 9 9.68462 9H14.3153C16.0901 9 17.4196 9 18.4177 9.14571ZM8 12.25C8.41421 12.25 8.75 12.5858 8.75 13V17C8.75 17.4142 8.41421 17.75 8 17.75C7.58579 17.75 7.25 17.4142 7.25 17V13C7.25 12.5858 7.58579 12.25 8 12.25ZM16.75 13C16.75 12.5858 16.4142 12.25 16 12.25C15.5858 12.25 15.25 12.5858 15.25 13V17C15.25 17.4142 15.5858 17.75 16 17.75C16.4142 17.75 16.75 17.4142 16.75 17V13ZM12 12.25C12.4142 12.25 12.75 12.5858 12.75 13V17C12.75 17.4142 12.4142 17.75 12 17.75C11.5858 17.75 11.25 17.4142 11.25 17V13C11.25 12.5858 11.5858 12.25 12 12.25Z" fill="currentColor"/></svg>',

            'cart-10' => '<svg viewBox="0 0 24 24" fill="none"><g><path d="M2.23737 2.28845C1.84442 2.15746 1.41968 2.36983 1.28869 2.76279C1.15771 3.15575 1.37008 3.58049 1.76303 3.71147L2.02794 3.79978C2.70435 4.02524 3.15155 4.17551 3.481 4.32877C3.79296 4.47389 3.92784 4.59069 4.01426 4.71059C4.10068 4.83049 4.16883 4.99538 4.20785 5.33722C4.24907 5.69823 4.2502 6.17 4.2502 6.883L4.2502 9.55484C4.25018 10.9224 4.25017 12.0247 4.36673 12.8917C4.48774 13.7918 4.74664 14.5497 5.34855 15.1516C5.95047 15.7535 6.70834 16.0124 7.60845 16.1334C8.47542 16.25 9.57773 16.25 10.9453 16.25H18.0002C18.4144 16.25 18.7502 15.9142 18.7502 15.5C18.7502 15.0857 18.4144 14.75 18.0002 14.75H11.0002C9.56479 14.75 8.56367 14.7484 7.80832 14.6468C7.07455 14.5482 6.68598 14.3677 6.40921 14.091C6.17403 13.8558 6.00839 13.5398 5.9034 13H16.0222C16.9817 13 17.4614 13 17.8371 12.7522C18.2128 12.5045 18.4017 12.0636 18.7797 11.1817L19.2082 10.1817C20.0177 8.2929 20.4225 7.34849 19.9779 6.67422C19.5333 5.99996 18.5058 5.99996 16.4508 5.99996H5.74526C5.73936 5.69227 5.72644 5.41467 5.69817 5.16708C5.64282 4.68226 5.52222 4.2374 5.23112 3.83352C4.94002 3.42965 4.55613 3.17456 4.1137 2.96873C3.69746 2.7751 3.16814 2.59868 2.54176 2.38991L2.23737 2.28845Z" fill="currentColor"/><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" fill="currentColor"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" fill="currentColor"/></g></svg>',

            'cart-11' => '<svg viewBox="0 0 24 24" fill="none"><path d="M2.08416 2.7512C2.22155 2.36044 2.6497 2.15503 3.04047 2.29242L3.34187 2.39838C3.95839 2.61511 4.48203 2.79919 4.89411 3.00139C5.33474 3.21759 5.71259 3.48393 5.99677 3.89979C6.27875 4.31243 6.39517 4.76515 6.4489 5.26153C6.47295 5.48373 6.48564 5.72967 6.49233 6H17.1305C18.8155 6 20.3323 6 20.7762 6.57708C21.2202 7.15417 21.0466 8.02369 20.6995 9.76275L20.1997 12.1875C19.8846 13.7164 19.727 14.4808 19.1753 14.9304C18.6236 15.38 17.8431 15.38 16.2821 15.38H10.9792C8.19028 15.38 6.79583 15.38 5.92943 14.4662C5.06302 13.5523 4.99979 12.5816 4.99979 9.64L4.99979 7.03832C4.99979 6.29837 4.99877 5.80316 4.95761 5.42295C4.91828 5.0596 4.84858 4.87818 4.75832 4.74609C4.67026 4.61723 4.53659 4.4968 4.23336 4.34802C3.91052 4.18961 3.47177 4.03406 2.80416 3.79934L2.54295 3.7075C2.15218 3.57012 1.94678 3.14197 2.08416 2.7512Z" fill="currentColor"/><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" fill="currentColor"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" fill="currentColor"/></svg>',

            'cart-12' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6.29977 5H21L19 12H7.37671M20 16H8L6 3H3M9 20C9 20.5523 8.55228 21 8 21C7.44772 21 7 20.5523 7 20C7 19.4477 7.44772 19 8 19C8.55228 19 9 19.4477 9 20ZM20 20C20 20.5523 19.5523 21 19 21C18.4477 21 18 20.5523 18 20C18 19.4477 18.4477 19 19 19C19.5523 19 20 19.4477 20 20Z"/></svg>',

            'cart-13' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.2998 5H22L20 12H8.37675M21 16H9L7 3H4M4 8H2M5 11H2M6 14H2M10 20C10 20.5523 9.55228 21 9 21C8.44772 21 8 20.5523 8 20C8 19.4477 8.44772 19 9 19C9.55228 19 10 19.4477 10 20ZM21 20C21 20.5523 20.5523 21 20 21C19.4477 21 19 20.5523 19 20C19 19.4477 19.4477 19 20 19C20.5523 19 21 19.4477 21 20Z"/></svg>',
        ];
    }

    public function add_settings_page()
    {
        add_options_page(
            __('QuantWP SideCart Settings', 'quantwp-sidecart-for-woocommerce'),
            __('QuantWP SideCart', 'quantwp-sidecart-for-woocommerce'),
            'manage_options',
            $this->page_slug,
            array($this, 'render_settings_page')
        );
    }

    public function register_settings()
    {
        register_setting(
            $this->option_group,
            'quantwp_sidecart_auto_open',
            array(
                'type' => 'boolean',
                'default' => 1,
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );


        // Shipping Bar Settings
        register_setting(
            $this->option_group,
            'quantwp_sidecart_shipping_bar_enabled',
            array(
                'type' => 'boolean',
                'default' => 1,
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );

        register_setting(
            $this->option_group,
            'quantwp_sidecart_shipping_threshold',
            array(
                'type' => 'number',
                'default' => 50,
                'sanitize_callback' => 'floatval'
            )
        );

        // Cross-Sell Settings
        register_setting(
            $this->option_group,
            'quantwp_sidecart_cross_sells_enabled',
            array(
                'type' => 'boolean',
                'default' => 1,
                'sanitize_callback' => 'rest_sanitize_boolean'
            )
        );

        register_setting(
            $this->option_group,
            'quantwp_sidecart_cross_sells_limit',
            array(
                'type' => 'integer',
                'default' => 6,
                'sanitize_callback' => 'absint'
            )
        );

        register_setting(
            $this->option_group,
            'quantwp_sidecart_icon',
            array(
                'type' => 'string',
                'default' => 'cart-classic',
                'sanitize_callback' => 'sanitize_key' // Secure text input
            )
        );
    }

    /**
     * Render the Visual Icon Selector for Admin
     */
    public function render_icon_selector()
    {
        $selected_icon = get_option('quantwp_sidecart_icon', 'cart-classic');
        // Fetch secure icons from this class using self::
        $icons = self::get_cart_icons();


        echo '<div class="side-cart-icon-grid">';
        foreach ($icons as $key => $svg) {
            $class = ($selected_icon === $key) ? 'selected' : '';
            echo '<label class="side-cart-option ' . esc_attr($class) . '">';
            echo '<input type="radio" name="quantwp_sidecart_icon" value="' . esc_attr($key) . '" ' . checked($selected_icon, $key, false) . '>';
            // Define allowed SVG tags for wp_kses
            $allowed_svg = array(
                'svg' => array(
                    'viewbox' => true,
                    'fill' => true,
                    'stroke' => true,
                    'stroke-width' => true,
                    'stroke-linecap' => true,
                    'stroke-linejoin' => true,
                    'version' => true,  // ADD THIS
                    'class' => true,
                    'width' => true,
                    'height' => true
                ),
                'path' => array(
                    'd' => true,
                    'fill' => true,
                    'stroke' => true,
                    'fill-rule' => true,      // ADD THIS
                    'clip-rule' => true       // ADD THIS
                ),
                'circle' => array(
                    'cx' => true,
                    'cy' => true,
                    'r' => true,
                    'stroke' => true,           // ADD THIS
                    'stroke-width' => true,     // ADD THIS
                    'stroke-linejoin' => true,  // ADD THIS
                    'fill' => true              // ADD THIS
                ),
                'g' => array(
                    'fill' => true,
                    'stroke' => true  // ADD THIS
                ),
                'rect' => array(
                    'x' => true,
                    'y' => true,
                    'width' => true,
                    'height' => true,
                    'rx' => true,
                    'fill' => true,    // ADD THIS
                    'stroke' => true   // ADD THIS
                ),
                'line' => array(
                    'x1' => true,
                    'y1' => true,
                    'x2' => true,
                    'y2' => true,
                    'stroke' => true,       // ADD THIS
                    'stroke-width' => true  // ADD THIS
                ),
                'polyline' => array(
                    'points' => true,
                    'stroke' => true,       // ADD THIS
                    'fill' => true          // ADD THIS
                ),
            );

            echo wp_kses($svg, $allowed_svg);
            echo '</label>';
        }
        echo '</div>';
    }


    /**
     * Render settings page
     */
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }


        settings_errors('quantwp_sidecart_messages');
?>

        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields($this->option_group); ?>

                <!-- General Settings -->
                <h2><?php esc_html_e('General Settings', 'quantwp-sidecart-for-woocommerce'); ?></h2>
                <table class="form-table">

                    <tr>
                        <th scope="row">
                            <label for="quantwp_sidecart_auto_open">
                                <?php esc_html_e('Auto-Open Cart', 'quantwp-sidecart-for-woocommerce'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox"
                                name="quantwp_sidecart_auto_open"
                                id="quantwp_sidecart_auto_open"
                                value="1"
                                <?php checked(get_option('quantwp_sidecart_auto_open', 1), 1); ?>>
                            <p class="description">
                                <?php esc_html_e('Automatically open side cart when item is added to cart.', 'quantwp-sidecart-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>

                </table>

                <!-- Shipping Bar Settings -->
                <h2><?php esc_html_e('Shipping Progress Bar', 'quantwp-sidecart-for-woocommerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="quantwp_sidecart_shipping_bar_enabled">
                                <?php esc_html_e('Enable Shipping Bar', 'quantwp-sidecart-for-woocommerce'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox"
                                name="quantwp_sidecart_shipping_bar_enabled"
                                id="quantwp_sidecart_shipping_bar_enabled"
                                value="1"
                                <?php checked(get_option('quantwp_sidecart_shipping_bar_enabled', 1), 1); ?>>
                            <p class="description">
                                <?php esc_html_e('Show free shipping progress bar in side cart.', 'quantwp-sidecart-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="quantwp_sidecart_shipping_threshold">
                                <?php esc_html_e('Free Shipping Threshold', 'quantwp-sidecart-for-woocommerce'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                name="quantwp_sidecart_shipping_threshold"
                                id="quantwp_sidecart_shipping_threshold"
                                value="<?php echo esc_attr(get_option('quantwp_sidecart_shipping_threshold', 50)); ?>"
                                min="0"
                                step="0.01"
                                class="regular-text">
                            <p class="description">
                                <?php
                                printf(
                                    /* translators: %s: Currency symbol (e.g. USD, EUR) */
                                    esc_html__('Minimum cart amount for free shipping. Enter amount in %s.', 'quantwp-sidecart-for-woocommerce'),
                                    esc_html(get_woocommerce_currency())
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <!-- Cross-Sell Settings -->
                <h2><?php esc_html_e('Cross-Sell Products', 'quantwp-sidecart-for-woocommerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="quantwp_sidecart_cross_sells_enabled">
                                <?php esc_html_e('Enable Cross-Sells', 'quantwp-sidecart-for-woocommerce'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="checkbox"
                                name="quantwp_sidecart_cross_sells_enabled"
                                id="quantwp_sidecart_cross_sells_enabled"
                                value="1"
                                <?php checked(get_option('quantwp_sidecart_cross_sells_enabled', 1), 1); ?>>
                            <p class="description">
                                <?php esc_html_e('Show cross-sell product carousel in side cart.', 'quantwp-sidecart-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="quantwp_sidecart_cross_sells_limit">
                                <?php esc_html_e('Products to Show', 'quantwp-sidecart-for-woocommerce'); ?>
                            </label>
                        </th>
                        <td>
                            <input type="number"
                                name="quantwp_sidecart_cross_sells_limit"
                                id="quantwp_sidecart_cross_sells_limit"
                                value="<?php echo esc_attr(get_option('quantwp_sidecart_cross_sells_limit', 6)); ?>"
                                min="1"
                                max="20"
                                class="small-text">
                            <p class="description">
                                <?php esc_html_e('Maximum number of cross-sell products to display (1-20).', 'quantwp-sidecart-for-woocommerce'); ?>
                            </p>
                        </td>
                    </tr>
                </table>

                <h2><?php esc_html_e('Cart Icon', 'quantwp-sidecart-for-woocommerce'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e('Choose Icon', 'quantwp-sidecart-for-woocommerce'); ?></th>
                        <td>
                            <?php $this->render_icon_selector(); ?>
                            <p class="description"><?php esc_html_e('Select the icon to display on your site trigger.', 'quantwp-sidecart-for-woocommerce'); ?></p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'quantwp-sidecart-for-woocommerce')); ?>
            </form>
        </div>

<?php
    }

    public function enqueue_admin_assets($hook)
    {
        // 1. Define your specific page hook
        // The format is usually: 'settings_page_' + your_menu_slug
        $my_settings_page = 'settings_page_quantwp_sidecart_settings';

        // 2. Check if the current page matches YOUR settings page
        if ($hook !== $my_settings_page) {
            return;
        }

        // 3. Safe to enqueue
        wp_enqueue_script(
            'quantwp-sidecart-admin',
            QUANTWP_URL . 'assets/js/admin.js',
            array('jquery'),
            QUANTWP_VERSION,
            true
        );

        // Enqueue admin CSS
        wp_enqueue_style(
            'quantwp-sidecart-admin',
            QUANTWP_URL . 'assets/css/admin.css',
            array(),
            QUANTWP_VERSION
        );
    }
}
