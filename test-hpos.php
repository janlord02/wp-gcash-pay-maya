<?php
/**
 * Test HPOS Compatibility
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>HPOS Compatibility Test</h2>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce is active</p>";

// Check if FeaturesUtil is available
if (!class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    echo "<p style='color: red;'>❌ WooCommerce FeaturesUtil not available</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce FeaturesUtil is available</p>";

// Test HPOS compatibility declaration
try {
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
    echo "<p style='color: green;'>✅ HPOS compatibility declared successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Failed to declare HPOS compatibility: " . $e->getMessage() . "</p>";
}

echo "<p><strong>Test completed. Please delete this file after testing.</strong></p>";