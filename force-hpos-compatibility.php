<?php
/**
 * Force HPOS Compatibility Script
 * Run this once to clear cache and force HPOS compatibility
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>Force HPOS Compatibility</h2>";

// Clear all relevant caches
echo "<h3>Clearing Caches</h3>";

// Clear transients
$transients_to_delete = array(
    'wc_block_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_payment_gateway_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_feature_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php',
    'wc_hpos_compatibility_gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php'
);

foreach ($transients_to_delete as $transient) {
    if (delete_transient($transient)) {
        echo "<p style='color: green;'>✅ Cleared transient: " . $transient . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Transient not found: " . $transient . "</p>";
    }
}

// Clear WordPress cache
if (function_exists('wp_cache_flush')) {
    wp_cache_flush();
    echo "<p style='color: green;'>✅ Cleared WordPress cache</p>";
}

// Clear WooCommerce cache
if (function_exists('wc_get_container')) {
    try {
        $container = wc_get_container();
        if ($container && method_exists($container, 'get')) {
            $cache = $container->get('Automattic\WooCommerce\Internal\DataStores\Order\DataStore');
            if ($cache && method_exists($cache, 'clear_cache')) {
                $cache->clear_cache();
                echo "<p style='color: green;'>✅ Cleared WooCommerce cache</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ Could not clear WooCommerce cache: " . $e->getMessage() . "</p>";
    }
}

// Force HPOS compatibility declaration
echo "<h3>Declaring HPOS Compatibility</h3>";

if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    try {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
        echo "<p style='color: green;'>✅ HPOS compatibility declared successfully</p>";
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
    echo "<p style='color: green;'>✅ Triggered payment gateways registration</p>";

    do_action('woocommerce_init');
    echo "<p style='color: green;'>✅ Triggered WooCommerce initialization</p>";
}

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>Go to <strong>WooCommerce > Settings > Advanced > Features</strong></li>";
echo "<li>Check if the plugin is now showing as compatible with HPOS</li>";
echo "<li>If still incompatible, try deactivating and reactivating the plugin</li>";
echo "<li>Delete this file after testing</li>";
echo "</ol>";

echo "<p><strong>Script completed. Please delete this file after testing.</strong></p>";