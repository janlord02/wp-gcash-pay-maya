<?php
/**
 * Test HPOS Compatibility
 * This script tests if the plugin is properly compatible with HPOS
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

// Check WooCommerce version
echo "<h3>WooCommerce Version</h3>";
echo "<p>Version: " . WC()->version . "</p>";

// Check if FeaturesUtil is available
if (!class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
    echo "<p style='color: red;'>❌ WooCommerce FeaturesUtil not available</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce FeaturesUtil is available</p>";

// Test HPOS compatibility declaration
echo "<h3>Testing HPOS Compatibility Declaration</h3>";
try {
    \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
    echo "<p style='color: green;'>✅ HPOS compatibility declaration successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ HPOS compatibility declaration failed: " . $e->getMessage() . "</p>";
}

// Check if plugin file exists and is readable
echo "<h3>Plugin File Check</h3>";
$plugin_file = WP_PLUGIN_DIR . '/gcash-paymaya-payment-gateway/gcash-paymaya-payment-gateway.php';
if (file_exists($plugin_file)) {
    echo "<p style='color: green;'>✅ Plugin file exists</p>";
    if (is_readable($plugin_file)) {
        echo "<p style='color: green;'>✅ Plugin file is readable</p>";
    } else {
        echo "<p style='color: red;'>❌ Plugin file is not readable</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Plugin file not found</p>";
}

// Check if gateway class exists
echo "<h3>Gateway Class Check</h3>";
if (class_exists('WC_Gateway_GCash_PayMaya')) {
    echo "<p style='color: green;'>✅ Gateway class exists</p>";

    // Test creating a gateway instance
    try {
        $gateway = new WC_Gateway_GCash_PayMaya();
        echo "<p style='color: green;'>✅ Gateway instance created successfully</p>";

        // Check if gateway supports required features
        if (in_array('products', $gateway->supports)) {
            echo "<p style='color: green;'>✅ Gateway supports products</p>";
        } else {
            echo "<p style='color: red;'>❌ Gateway does not support products</p>";
        }

        if (in_array('refunds', $gateway->supports)) {
            echo "<p style='color: green;'>✅ Gateway supports refunds</p>";
        } else {
            echo "<p style='color: red;'>❌ Gateway does not support refunds</p>";
        }

    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Failed to create gateway instance: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Gateway class does not exist</p>";
}

// Test order creation (HPOS compatibility)
echo "<h3>Order Creation Test (HPOS Compatibility)</h3>";
try {
    // Create a test order
    $order = wc_create_order();
    if ($order) {
        echo "<p style='color: green;'>✅ Test order created successfully</p>";

        // Test adding meta data (HPOS compatible)
        $order->update_meta_data('_test_meta', 'test_value');
        $order->save();

        $retrieved_value = $order->get_meta('_test_meta');
        if ($retrieved_value === 'test_value') {
            echo "<p style='color: green;'>✅ Meta data operations work correctly</p>";
        } else {
            echo "<p style='color: red;'>❌ Meta data operations failed</p>";
        }

        // Clean up test order
        $order->delete(true);
        echo "<p style='color: green;'>✅ Test order cleaned up</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create test order</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Order creation test failed: " . $e->getMessage() . "</p>";
}

// Check for any PHP errors
echo "<h3>PHP Error Check</h3>";
$error_log = ini_get('error_log');
if ($error_log && file_exists($error_log)) {
    $recent_errors = file_get_contents($error_log);
    if (strpos($recent_errors, 'gcash-paymaya') !== false) {
        echo "<p style='color: orange;'>⚠️ Found recent errors related to the plugin in error log</p>";
    } else {
        echo "<p style='color: green;'>✅ No recent plugin errors found</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ️ Error log not available or WP_DEBUG disabled</p>";
}

echo "<h3>Next Steps</h3>";
echo "<ol>";
echo "<li>If all tests pass, try enabling HPOS again</li>";
echo "<li>Go to WooCommerce > Settings > Advanced > Features</li>";
echo "<li>Enable 'High-performance order storage'</li>";
echo "<li>Check if the plugin is now compatible</li>";
echo "</ol>";

echo "<p><strong>Test completed. Please delete this file after testing.</strong></p>";