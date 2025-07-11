<?php
/**
 * Toggle HPOS Script
 * This script can temporarily disable HPOS to test plugin compatibility
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>HPOS Toggle Script</h2>";

// Check if WooCommerce is active
if (!class_exists('WooCommerce')) {
    echo "<p style='color: red;'>❌ WooCommerce is not active</p>";
    exit;
}

echo "<p style='color: green;'>✅ WooCommerce is active</p>";

// Check current HPOS status
echo "<h3>Current HPOS Status</h3>";
$hpos_enabled = get_option('woocommerce_custom_orders_table_enabled', 'no');
echo "<p>HPOS currently: " . ($hpos_enabled === 'yes' ? 'Enabled' : 'Disabled') . "</p>";

// Check if user wants to toggle
if (isset($_GET['action'])) {
    if ($_GET['action'] === 'disable') {
        update_option('woocommerce_custom_orders_table_enabled', 'no');
        echo "<p style='color: green;'>✅ HPOS has been disabled</p>";
        echo "<p>Please refresh the WooCommerce Features page to see the change.</p>";
    } elseif ($_GET['action'] === 'enable') {
        update_option('woocommerce_custom_orders_table_enabled', 'yes');
        echo "<p style='color: green;'>✅ HPOS has been enabled</p>";
        echo "<p>Please refresh the WooCommerce Features page to see the change.</p>";
    }
} else {
    echo "<h3>Actions</h3>";
    if ($hpos_enabled === 'yes') {
        echo "<p><a href='?action=disable' style='background: #dc3232; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>Disable HPOS (Use Legacy Storage)</a></p>";
        echo "<p style='color: orange;'>⚠️ This will switch to WordPress posts storage (legacy)</p>";
    } else {
        echo "<p><a href='?action=enable' style='background: #46b450; color: white; padding: 10px; text-decoration: none; border-radius: 3px;'>Enable HPOS (Use High-Performance Storage)</a></p>";
        echo "<p style='color: blue;'>ℹ️ This will switch to High-performance order storage</p>";
    }
}

echo "<h3>What This Does</h3>";
echo "<ul>";
echo "<li><strong>Disable HPOS:</strong> Switches to WordPress posts storage (legacy) - no compatibility issues</li>";
echo "<li><strong>Enable HPOS:</strong> Switches to High-performance order storage - requires plugin compatibility</li>";
echo "</ul>";

echo "<h3>Recommendation</h3>";
if ($hpos_enabled === 'yes') {
    echo "<p style='color: orange;'>⚠️ Since your plugin shows as incompatible with HPOS, you can temporarily disable HPOS to use the plugin.</p>";
    echo "<p>This will allow the plugin to work while we resolve the compatibility issue.</p>";
} else {
    echo "<p style='color: green;'>✅ HPOS is currently disabled. Your plugin should work without compatibility issues.</p>";
}

echo "<p><strong>Please delete this file after testing.</strong></p>";