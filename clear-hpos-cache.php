<?php
/**
 * Clear HPOS Cache Script
 * This script aggressively clears all WooCommerce HPOS-related cache
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>Clear HPOS Cache</h2>";

// Clear all possible transients
echo "<h3>Clearing Transients</h3>";

$transients = array(
    'wc_block_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_payment_gateway_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_feature_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_hpos_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_compatibility_gcash-paymaya-payment-gateway',
    'wc_hpos_compatibility',
    'wc_feature_compatibility'
);

foreach ($transients as $transient) {
    if (delete_transient($transient)) {
        echo "<p style='color: green;'>✅ Cleared: " . $transient . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Not found: " . $transient . "</p>";
    }
}

// Clear WordPress cache
echo "<h3>Clearing WordPress Cache</h3>";
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p style='color: green;'>✅ WordPress cache cleared</p>";
}

// Clear WooCommerce cache
echo "<h3>Clearing WooCommerce Cache</h3>";
if (function_exists('wc_get_container')) {
    try {
        $container = wc_get_container();
        if ($container && method_exists($container, 'get')) {
            // Try to clear various WooCommerce caches
            $services = array(
                'Automattic\WooCommerce\Internal\DataStores\Order\DataStore',
                'Automattic\WooCommerce\Internal\Features\FeaturesController',
                'Automattic\WooCommerce\Internal\DataStores\Order\OrdersTableDataStore'
            );

            foreach ($services as $service) {
                try {
                    $service_instance = $container->get($service);
                    if ($service_instance && method_exists($service_instance, 'clear_cache')) {
                        $service_instance->clear_cache();
                        echo "<p style='color: green;'>✅ Cleared cache for: " . $service . "</p>";
                    }
                } catch (Exception $e) {
                    // Ignore individual service errors
                }
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Could not clear WooCommerce cache: " . $e->getMessage() . "</p>";
    }
}

// Clear object cache
echo "<h3>Clearing Object Cache</h3>";
if (function_exists('wp_cache_delete')) {
    wp_cache_delete('wc_features_compatibility', 'woocommerce');
    wp_cache_delete('wc_hpos_compatibility', 'woocommerce');
    wp_cache_delete('wc_plugin_compatibility', 'woocommerce');
    echo "<p style='color: green;'>✅ Object cache cleared</p>";
}

// Force HPOS compatibility declaration
echo "<h3>Declaring HPOS Compatibility</h3>";
if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    try {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
        echo "<p style='color: green;'>✅ HPOS compatibility declared</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Failed to declare HPOS compatibility: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ WooCommerce FeaturesUtil not available</p>";
}

// Force WooCommerce to re-evaluate
echo "<h3>Forcing WooCommerce Re-evaluation</h3>";
if (function_exists('do_action')) {
    do_action('woocommerce_payment_gateways');
    echo "<p style='color: green;'>✅ Payment gateways re-registered</p>";

    do_action('woocommerce_init');
    echo "<p style='color: green;'>✅ WooCommerce re-initialized</p>";

    do_action('init');
    echo "<p style='color: green;'>✅ WordPress re-initialized</p>";
}

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li><strong>Deactivate the plugin</strong> in WordPress Admin > Plugins</li>";
echo "<li><strong>Wait 30 seconds</strong></li>";
echo "<li><strong>Activate the plugin</strong> again</li>";
echo "<li><strong>Go to WooCommerce > Settings > Advanced > Features</strong></li>";
echo "<li><strong>Check if HPOS compatibility is now working</strong></li>";
echo "<li><strong>Delete this file</strong> after testing</li>";
echo "</ol>";

echo "<p><strong>Cache clearing completed. Please follow the steps above.</strong></p>";