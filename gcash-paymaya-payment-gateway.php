<?php
/**
 * Plugin Name: GCash & PayMaya Payment Gateway
 * Plugin URI: https://janlordluga.com/
 * Description: Accept payments via QR codes for GCash and PayMaya with manual payment confirmation
 * Version: 1.2.0
 * Requires at least: 5.0
 * Tested up to: 6.4
 * WC requires at least: 5.0
 * WC tested up to: 8.5
 * Requires PHP: 7.4
 * Author: Janlord Luga
 * Author URI: https://janlordluga.com/
 * Text Domain: gcash-paymaya-payment-gateway
 * Domain Path: /languages
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Network: false
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GCASH_PAYMAYA_PLUGIN_URL', plugin_dir_url(__FILE__));
define('GCASH_PAYMAYA_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('GCASH_PAYMAYA_PLUGIN_VERSION', '1.2.0');

// Declare HPOS compatibility - this MUST be the first thing after constants
add_action('before_woocommerce_init', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
    }
}, 0);

// Also declare compatibility on plugins_loaded for extra safety
add_action('plugins_loaded', function () {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
    }
}, 0);

// Check if WooCommerce is active
function gcash_paymaya_check_wc()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('GCash & PayMaya Payment Gateway requires WooCommerce to be installed and active.', 'gcash-paymaya-payment-gateway') .
                '</p></div>';
        });
        return false;
    }
    return true;
}

// Check PHP version compatibility
function gcash_paymaya_check_php_version()
{
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('GCash & PayMaya Payment Gateway requires PHP 7.4 or higher. Current version: ' . PHP_VERSION, 'gcash-paymaya-payment-gateway') .
                '</p></div>';
        });
        return false;
    }
    return true;
}

// Check WordPress version compatibility
function gcash_paymaya_check_wp_version()
{
    if (version_compare(get_bloginfo('version'), '5.0', '<')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('GCash & PayMaya Payment Gateway requires WordPress 5.0 or higher.', 'gcash-paymaya-payment-gateway') .
                '</p></div>';
        });
        return false;
    }
    return true;
}

// Initialize the plugin
function gcash_paymaya_init()
{
    // Check all requirements
    if (!gcash_paymaya_check_php_version() || !gcash_paymaya_check_wp_version() || !gcash_paymaya_check_wc()) {
        return;
    }

    // Include the gateway class
    $gateway_file = GCASH_PAYMAYA_PLUGIN_PATH . 'includes/class-gcash-paymaya-gateway.php';
    if (file_exists($gateway_file)) {
        require_once $gateway_file;
    } else {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('GCash & PayMaya Payment Gateway: Gateway class file not found.', 'gcash-paymaya-payment-gateway') .
                '</p></div>';
        });
        return;
    }

    // Check if class exists
    if (!class_exists('WC_Gateway_GCash_PayMaya')) {
        add_action('admin_notices', function () {
            echo '<div class="notice notice-error"><p>' .
                __('GCash & PayMaya Payment Gateway: Gateway class not found.', 'gcash-paymaya-payment-gateway') .
                '</p></div>';
        });
        return;
    }

    // Add the gateway to WooCommerce
    add_filter('woocommerce_payment_gateways', 'gcash_paymaya_add_gateway');
}
add_action('plugins_loaded', 'gcash_paymaya_init');

// Add the gateway to WooCommerce
function gcash_paymaya_add_gateway($gateways)
{
    $gateways[] = 'WC_Gateway_GCash_PayMaya';
    return $gateways;
}

// Register gateway for block-based checkout
function gcash_paymaya_register_block_support()
{
    try {
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            $blocks_file = GCASH_PAYMAYA_PLUGIN_PATH . 'includes/class-gcash-paymaya-blocks-support.php';
            if (file_exists($blocks_file)) {
                require_once $blocks_file;

                add_action(
                    'woocommerce_blocks_payment_method_type_registration',
                    function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
                        if (class_exists('GCash_PayMaya_Blocks_Support')) {
                            $payment_method_registry->register(new GCash_PayMaya_Blocks_Support());
                        }
                    }
                );
            }
        }
    } catch (Exception $e) {
        // Silently fail for blocks support
    }
}
add_action('init', 'gcash_paymaya_register_block_support', 5);

// Force WooCommerce to recognize block support
function gcash_paymaya_force_block_support($supports, $gateway_id)
{
    try {
        if ($gateway_id === 'gcash_paymaya') {
            $supports['woocommerce_blocks'] = true;
            $supports['cart_checkout_blocks'] = true;
        }
    } catch (Exception $e) {
        // Silently fail
    }
    return $supports;
}
add_filter('woocommerce_payment_gateway_supports', 'gcash_paymaya_force_block_support', 10, 2);

// Add block compatibility filter
function gcash_paymaya_block_compatibility($compatible, $gateway_id)
{
    try {
        if ($gateway_id === 'gcash_paymaya') {
            return true;
        }
    } catch (Exception $e) {
        // Silently fail
    }
    return $compatible;
}
add_filter('woocommerce_payment_gateway_block_compatibility', 'gcash_paymaya_block_compatibility', 10, 2);

// Register as block-compatible payment method
function gcash_paymaya_register_block_payment_method()
{
    try {
        if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
            add_filter('woocommerce_blocks_payment_method_type_registration', function ($registry) {
                if (class_exists('GCash_PayMaya_Blocks_Support')) {
                    $registry->register(new GCash_PayMaya_Blocks_Support());
                }
                return $registry;
            });
        }
    } catch (Exception $e) {
        // Silently fail
    }
}
add_action('init', 'gcash_paymaya_register_block_payment_method', 1);

// Clear cache on plugin activation
function gcash_paymaya_activate()
{
    try {
        // Clear any cached compatibility data
        delete_transient('wc_block_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php');
        delete_transient('wc_payment_gateway_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php');

        // Clear WordPress cache
        if (function_exists('wp_cache_flush')) {
            wp_cache_flush();
        }

        // Force WooCommerce to re-register payment gateways
        if (function_exists('do_action')) {
            do_action('woocommerce_payment_gateways');
        }
    } catch (Exception $e) {
        // Log error but don't prevent activation
        error_log('GCash PayMaya Gateway activation error: ' . $e->getMessage());
    }
}
register_activation_hook(__FILE__, 'gcash_paymaya_activate');

// Add plugin action links
function gcash_paymaya_plugin_links($links)
{
    $plugin_links = array(
        '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=gcash_paymaya') . '">' .
        __('Settings', 'gcash-paymaya-payment-gateway') . '</a>'
    );
    return array_merge($plugin_links, $links);
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'gcash_paymaya_plugin_links');

// Enqueue admin scripts and styles
function gcash_paymaya_admin_scripts($hook)
{
    // Load on WooCommerce settings pages
    if (strpos($hook, 'woocommerce_page_wc-settings') !== false) {
        // Check if we're on the payment gateway settings page
        if (isset($_GET['section']) && $_GET['section'] === 'gcash_paymaya') {
            wp_enqueue_media();
            wp_enqueue_script(
                'gcash-paymaya-admin',
                GCASH_PAYMAYA_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                GCASH_PAYMAYA_PLUGIN_VERSION,
                true
            );
        }
    }

    // Load on order edit pages
    if (strpos($hook, 'post.php') !== false && isset($_GET['post']) && get_post_type($_GET['post']) === 'shop_order') {
        wp_enqueue_style(
            'gcash-paymaya-admin',
            GCASH_PAYMAYA_PLUGIN_URL . 'assets/css/style.css',
            array(),
            GCASH_PAYMAYA_PLUGIN_VERSION
        );
    }

    // Load on WooCommerce settings pages
    if (strpos($hook, 'woocommerce_page_wc-settings') !== false) {
        wp_enqueue_style(
            'gcash-paymaya-admin',
            GCASH_PAYMAYA_PLUGIN_URL . 'assets/css/style.css',
            array(),
            GCASH_PAYMAYA_PLUGIN_VERSION
        );
    }
}
add_action('admin_enqueue_scripts', 'gcash_paymaya_admin_scripts');

// Enqueue frontend styles
function gcash_paymaya_frontend_styles()
{
    if (is_checkout() || is_wc_endpoint_url('order-received')) {
        wp_enqueue_style(
            'gcash-paymaya-frontend',
            GCASH_PAYMAYA_PLUGIN_URL . 'assets/css/style.css',
            array(),
            GCASH_PAYMAYA_PLUGIN_VERSION
        );
    }
}
add_action('wp_enqueue_scripts', 'gcash_paymaya_frontend_styles');

// Enqueue frontend scripts
function gcash_paymaya_frontend_scripts()
{
    if (is_checkout()) {
        wp_enqueue_script(
            'gcash-paymaya-frontend',
            GCASH_PAYMAYA_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            GCASH_PAYMAYA_PLUGIN_VERSION,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'gcash_paymaya_frontend_scripts');