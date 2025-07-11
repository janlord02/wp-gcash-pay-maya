<?php
/**
 * Check HPOS Status Script
 * This script checks the exact HPOS compatibility status
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>HPOS Compatibility Status Check</h2>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce is active</p>";

// Check WooCommerce version
echo "<h3>WooCommerce Version</h3>";
echo "<p>Version: " . WC()->version . "</p>";

// Check if FeaturesUtil is available
if (!class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    echo "<p style='color: red;'>❌ WooCommerce FeaturesUtil not available</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce FeaturesUtil is available</p>";

// Check HPOS feature status
echo "<h3>HPOS Feature Status</h3>";
echo "<p>Checking if HPOS is enabled...</p>";

// Check plugin compatibility
echo "<h3>Plugin Compatibility Check</h3>";
$plugin_file = plugin_basename(__FILE__);
$plugin_file = str_replace('check-hpos-status.php', 'gcash-paymaya-payment-gateway.php', $plugin_file);

echo "<p>Plugin file: " . $plugin_file . "</p>";
echo "<p>Checking compatibility status...</p>";

// Check if plugin is active
echo "<h3>Plugin Status</h3>";
if (is_plugin_active('gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php')) {
    echo "<p style='color: green;'>✅ Plugin is active</p>";
} else {
    echo "<p style='color: red;'>❌ Plugin is not active</p>";
}

// Check plugin file exists
$plugin_path = WP_PLUGIN_DIR . '/gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php';
if (file_exists($plugin_path)) {
    echo "<p style='color: green;'>✅ Plugin file exists</p>";
} else {
    echo "<p style='color: red;'>❌ Plugin file not found</p>";
}

// Check for any cached compatibility data
echo "<h3>Cached Compatibility Data</h3>";
$transients = array(
    'wc_hpos_compatibility_' . $plugin_file,
    'wc_feature_compatibility_' . $plugin_file,
    'wc_compatibility_' . $plugin_file
);

foreach ($transients as $transient) {
    $cached_data = get_transient($transient);
    if ($cached_data !== false) {
        echo "<p style='color: orange;'>⚠️ Found cached data for: " . $transient . "</p>";
        echo "<pre>" . print_r($cached_data, true) . "</pre>";
    } else {
        echo "<p style='color: green;'>✅ No cached data for: " . $transient . "</p>";
    }
}

// Try to declare compatibility manually
echo "<h3>Manual Compatibility Declaration</h3>";
try {
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', $plugin_path, true);
    echo "<p style='color: green;'>✅ Manual compatibility declaration successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Manual compatibility declaration failed: " . $e->getMessage() . "</p>";
}

echo "<h3>Recommendations</h3>";
echo "<ol>";
echo "<li>If HPOS is enabled and plugin is not compatible, try the cache clearing script</li>";
echo "<li>If HPOS is disabled, you can ignore the compatibility warning</li>";
echo "<li>Try deactivating and reactivating the plugin</li>";
echo "<li>Check if there are any PHP errors in your error log</li>";
echo "</ol>";

echo "<p><strong>Status check completed. Please delete this file after testing.</strong></p>";