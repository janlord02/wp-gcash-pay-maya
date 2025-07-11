# GCash & PayMaya Payment Gateway

A WooCommerce payment gateway plugin that allows customers to pay via QR codes using GCash and PayMaya with manual payment confirmation.

## Features

- **Dual Payment Methods**: Support for both GCash and PayMaya QR code payments
- **QR Code Upload**: Easy image upload for QR codes in admin settings
- **Manual Payment Confirmation**: Orders are placed on-hold until payment is manually verified
- **Payment Details Collection**: Collects amount sent and phone number used for payment
- **Order Management**: Payment details displayed in order admin pages
- **Email Notifications**: Payment instructions included in order emails
- **Block-Based Checkout**: Compatible with WooCommerce block-based checkout
- **Legacy Order Storage**: Currently works with WooCommerce's legacy order storage system

## Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher

## Installation

1. Upload the plugin files to `/wp-content/plugins/gcash-paymaya-payment-gateway/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Payments
4. Find "GCash & PayMaya Payment Gateway" and click "Manage"
5. Configure your settings and upload QR codes

## Configuration

### Basic Settings

- **Enable/Disable**: Turn the gateway on or off
- **Title**: Payment method title shown to customers
- **Description**: Payment method description shown to customers

### GCash Settings

- **Enable GCash**: Enable or disable GCash payment method
- **GCash Title**: Title for GCash payment option
- **GCash QR Code**: Upload your GCash QR code image

### PayMaya Settings

- **Enable PayMaya**: Enable or disable PayMaya payment method
- **PayMaya Title**: Title for PayMaya payment option
- **PayMaya QR Code**: Upload your PayMaya QR code image

### Instructions

- **Payment Instructions**: Instructions shown to customers on thank you page and emails

## How It Works

1. **Customer Checkout**: Customer selects GCash or PayMaya payment method
2. **QR Code Display**: Selected QR code is shown on checkout page
3. **Payment Details**: Customer enters amount sent and phone number used
4. **Order Placement**: Order is placed with "On Hold" status
5. **Manual Verification**: Admin verifies payment and updates order status
6. **Order Processing**: Order is processed once payment is confirmed

## Order Management

### Payment Details Display

Payment details are automatically displayed in:
- Order admin page (meta box and order details)
- Order emails
- Thank you page

### Payment Verification

The plugin provides tools to verify payments:
- Amount comparison with order total
- Phone number tracking
- QR code display for reference
- Payment method identification

## HPOS Compatibility Status

**Current Status**: The plugin currently works with WooCommerce's legacy order storage system.

**HPOS Compatibility**: High-Performance Order Storage (HPOS) compatibility is being developed. The plugin includes HPOS compatibility declarations, but full compatibility requires additional testing and refinement.

**Recommendation**: For now, use the plugin with legacy order storage enabled. HPOS can be enabled once compatibility is fully verified.

### To Use with Legacy Storage:

1. Go to WooCommerce > Settings > Advanced > Features
2. Ensure "High-performance order storage" is **disabled**
3. The plugin will work normally with legacy order storage

## Shortcode Usage

You can display payment information using the shortcode:

```
[gcash_paymaya_payment_info]
```

This shortcode displays:
- Available payment methods
- QR codes (if uploaded)
- Payment instructions

## Troubleshooting

### Payment Methods Not Showing

1. Ensure the gateway is enabled in WooCommerce settings
2. Check that at least one payment method (GCash or PayMaya) is enabled
3. Verify WooCommerce is properly configured
4. Clear any caching plugins

### QR Code Upload Issues

1. Ensure you have proper file permissions
2. Check that the image file is valid (PNG, JPG, etc.)
3. Try uploading a smaller image file
4. Check WordPress media library settings

### Order Status Issues

1. Orders are automatically set to "On Hold" status
2. Manually verify payment details in order admin
3. Update order status to "Processing" or "Completed" after verification

### Block-Based Checkout Issues

1. The plugin includes block support but may require additional configuration
2. Test with both classic and block-based checkout
3. Clear cache if using caching plugins

## Development

### File Structure

```
gcash-paymaya-payment-gateway/
├── gcash-paymaya-payment-gateway.php
├── includes/
│   ├── class-gcash-paymaya-gateway.php
│   └── class-gcash-paymaya-blocks-support.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── admin.js
│       └── frontend.js
└── README.md
```

### Hooks and Filters

The plugin provides several hooks for customization:

- `gcash_paymaya_payment_fields`: Modify payment fields display
- `gcash_paymaya_payment_instructions`: Modify payment instructions
- `gcash_paymaya_order_meta`: Modify order meta data

## Support

For support and feature requests, please contact the plugin author.

## Changelog

### Version 1.2.0
- Added QR code image upload functionality
- Improved payment details collection
- Enhanced order admin display
- Added block-based checkout support
- Improved HPOS compatibility declarations
- Fixed various bugs and compatibility issues

### Version 1.1.0
- Added payment details input fields
- Enhanced order management features
- Improved email notifications
- Added meta boxes for payment details

### Version 1.0.0
- Initial release
- Basic GCash and PayMaya payment functionality
- Manual payment confirmation system

## License

This plugin is licensed under the GPL v2 or later.

## Credits

Developed by Janlord Luga 