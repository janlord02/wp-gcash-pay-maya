<?php
/**
 * Debug information for GCash & PayMaya Payment Gateway
 * Run this file to check system compatibility
 */

// Load WordPress
require_once('../../../wp-load.php');

echo "<h2>GCash & PayMaya Payment Gateway - Debug Information</h2>";

// Check PHP version
echo "<h3>PHP Version</h3>";
echo "<p>Current PHP version: " . PHP_VERSION . "</p>";
echo "<p>Required PHP version: 7.4+</p>";
if (version_compare(PHP_VERSION, '7.4', '>=')) {
    echo "<p style='color: green;'>✅ PHP version is compatible</p>";
} else {
    echo "<p style='color: red;'>❌ PHP version is too old</p>";
}

// Check WordPress version
echo "<h3>WordPress Version</h3>";
echo "<p>Current WordPress version: " . get_bloginfo('version') . "</p>";
echo "<p>Required WordPress version: 5.0+</p>";
if (version_compare(get_bloginfo('version'), '5.0', '>=')) {
    echo "<p style='color: green;'>✅ WordPress version is compatible</p>";
} else {
    echo "<p style='color: red;'>❌ WordPress version is too old</p>";
}

// Check WooCommerce
echo "<h3>WooCommerce</h3>";
if (class_exists('WooCommerce')) {
    echo "<p style='color: green;'>✅ WooCommerce is active</p>";
    echo "<p>WooCommerce version: " . WC()->version . "</p>";

    // Check HPOS compatibility
    if (class_exists('Automattic\WooCommerce\Utilities\FeaturesUtil')) {
        echo "<p style='color: green;'>✅ WooCommerce FeaturesUtil is available</p>";
        echo "<p style='color: blue;'>ℹ️ HPOS compatibility will be declared by the plugin</p>";
    } else {
        echo "<p style='color: red;'>❌ WooCommerce FeaturesUtil not available</p>";
    }
} else {
    echo "<p style='color: red;'>❌ WooCommerce is not active</p>";
}

// Check file permissions
echo "<h3>File Permissions</h3>";
$plugin_dir = plugin_dir_path(__FILE__);
echo "<p>Plugin directory: " . $plugin_dir . "</p>";
echo "<p>Directory readable: " . (is_readable($plugin_dir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory writable: " . (is_writable($plugin_dir) ? 'Yes' : 'No') . "</p>";

// Check required files
echo "<h3>Required Files</h3>";
$files = array(
    'gcash-paymaya-payment-gateway.php',
    'includes/class-gcash-paymaya-gateway.php',
    'includes/class-gcash-paymaya-blocks-support.php',
    'assets/js/admin.js',
    'assets/js/frontend.js',
    'assets/css/style.css'
);

foreach ($files as $file) {
    $file_path = $plugin_dir . $file;
    if (file_exists($file_path)) {
        echo "<p style='color: green;'>✅ " . $file . " exists</p>";
    } else {
        echo "<p style='color: red;'>❌ " . $file . " missing</p>";
    }
}

// Check PHP extensions
echo "<h3>PHP Extensions</h3>";
$extensions = array('json', 'mbstring', 'curl');
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✅ " . $ext . " extension loaded</p>";
    } else {
        echo "<p style='color: red;'>❌ " . $ext . " extension not loaded</p>";
    }
}

// Check WordPress constants
echo "<h3>WordPress Constants</h3>";
echo "<p>ABSPATH: " . (defined('ABSPATH') ? 'Defined' : 'Not defined') . "</p>";
echo "<p>WP_DEBUG: " . (defined('WP_DEBUG') && WP_DEBUG ? 'Enabled' : 'Disabled') . "</p>";

// Check for errors
echo "<h3>Recent Errors</h3>";
if (defined('WP_DEBUG') && WP_DEBUG) {
    $error_log = ini_get('error_log');
    if ($error_log && file_exists($error_log)) {
        $recent_errors = tail($error_log, 10);
        echo "<pre>" . htmlspecialchars($recent_errors) . "</pre>";
    } else {
        echo "<p>No error log found or WP_DEBUG is disabled</p>";
    }
} else {
    echo "<p>WP_DEBUG is disabled. Enable it to see error details.</p>";
}

echo "<p><strong>Debug completed. Please delete this file after testing.</strong></p>";

// Helper function to get last lines of a file
function tail($filename, $lines = 10)
{
    if (!file_exists($filename)) {
        return '';
    }

    $file = file($filename);
    return implode('', array_slice($file, -$lines));
}