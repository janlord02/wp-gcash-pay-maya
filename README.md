# GCash & PayMaya Payment Gateway for WooCommerce

A professional WooCommerce payment gateway plugin that allows merchants to accept payments via QR codes for GCash and PayMaya with manual payment confirmation. Perfect for Philippine-based e-commerce stores.

## 🚀 Features

- **Dual Payment Methods**: Support for both GCash and PayMaya payment methods
- **QR Code Integration**: Upload and display QR codes for each payment method
- **Payment Details Collection**: Collect amount sent and phone number used for payment verification
- **Manual Payment Confirmation**: Orders are placed on hold until payment is manually verified
- **WooCommerce Integration**: Full integration with WooCommerce checkout, orders, and emails
- **Shortcode Compatible**: Works with WooCommerce checkout shortcode `[woocommerce_checkout]`
- **HPOS Compatible**: Compatible with WooCommerce High-Performance Order Storage
- **Classic Checkout Compatible**: Works with WooCommerce classic checkout
- **Responsive Design**: Mobile-friendly interface for both admin and frontend
- **Customizable**: Configurable titles, descriptions, and instructions
- **Form Validation**: Client-side validation for payment details with error handling
- **Admin Order Management**: Payment details displayed in order admin page for easy verification
- **Professional UI**: Beautiful, modern interface with step-by-step payment instructions

## ⚠️ Important Note

**Block-based Checkout Support**: This plugin currently works with WooCommerce's classic checkout. Block-based checkout support is planned for future versions but is not yet fully implemented. Users will see a compatibility notice when using block-based checkout, but the plugin will continue to function normally with classic checkout.

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **WooCommerce**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.6 or higher

## 📦 Installation

### Method 1: Manual Installation
1. Download the plugin files
2. Upload the `gcash-paymaya-payment-gateway` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Go to **WooCommerce > Settings > Payments**
5. Find "GCash & PayMaya Payment Gateway" and click "Manage"
6. Configure the gateway settings and upload your QR codes

### Method 2: WordPress Admin
1. Go to **Plugins > Add New**
2. Click "Upload Plugin"
3. Choose the plugin zip file
4. Click "Install Now" and then "Activate"
5. Configure the gateway settings

## 🎯 Shortcode Usage

### Checkout Shortcode
The plugin works seamlessly with WooCommerce's checkout shortcode. You can use the standard WooCommerce checkout shortcode on any page:

```
[woocommerce_checkout]
```

### Custom Checkout Page
To create a custom checkout page with the GCash & PayMaya payment gateway:

1. **Create a new page** in WordPress
2. **Add the shortcode**: `[woocommerce_checkout]`
3. **Set as checkout page** in WooCommerce settings (WooCommerce > Settings > Advanced > Page setup)
4. **Configure the gateway** (WooCommerce > Settings > Payments > GCash & PayMaya Payment Gateway)

### Example Usage
```php
// In your theme or custom plugin
echo do_shortcode('[woocommerce_checkout]');

// Or in a page/post
[woocommerce_checkout]
```

### Shortcode with Parameters
You can also use the shortcode with additional parameters for customization:

```
[woocommerce_checkout order_button_text="Pay with GCash/PayMaya"]
```

## ⚙️ Configuration

### Basic Settings

1. **Enable/Disable**: Toggle the gateway on or off
2. **Title**: The payment method title shown to customers (default: "GCash & PayMaya Payment")
3. **Description**: Description displayed during checkout

### Payment Methods

#### GCash Settings
- **Enable GCash**: Enable or disable GCash payment method
- **GCash Title**: Custom title for GCash payment method (default: "GCash Payment")
- **GCash QR Code**: Upload your GCash QR code image using the media uploader

#### PayMaya Settings
- **Enable PayMaya**: Enable or disable PayMaya payment method
- **PayMaya Title**: Custom title for PayMaya payment method (default: "PayMaya Payment")
- **PayMaya QR Code**: Upload your PayMaya QR code image using the media uploader

### Instructions
- **Payment Instructions**: Custom instructions shown on thank you page and emails

## 🔄 How It Works

### Customer Checkout Process
1. **Select Payment Method**: Customer chooses between GCash or PayMaya
2. **View QR Code**: Relevant QR code is displayed for the selected payment method
3. **Complete Payment**: Customer scans QR code and completes payment in their mobile app
4. **Enter Details**: Customer enters the amount sent and phone number used
5. **Submit Order**: Customer clicks "Place Order" to submit the order
6. **Confirmation**: Order is created with "On Hold" status

### Order Processing
1. **Order Creation**: Order is created with "On Hold" status
2. **Data Storage**: Payment method, amount sent, and phone number are stored
3. **Email Notification**: Customer receives confirmation email with payment details
4. **Manual Verification**: Merchant verifies payment in admin panel
5. **Order Update**: Merchant updates order status to "Processing" or "Completed"

### Admin Verification
1. **Order Details**: Payment details are displayed in order admin page
2. **Amount Comparison**: Automatic comparison of amount sent vs. order total
3. **QR Code Reference**: QR code is displayed for verification
4. **Status Indicators**: Visual indicators for verification status

## 📁 File Structure

```
gcash-paymaya-payment-gateway/
├── gcash-paymaya-payment-gateway.php    # Main plugin file
├── README.md                            # This documentation
├── LICENSE                              # GPL v2 license
├── .gitignore                           # Git ignore rules
├── includes/
│   ├── class-gcash-paymaya-gateway.php  # Gateway class
│   └── class-gcash-paymaya-blocks-support.php # Block checkout support (experimental)
└── assets/
    ├── js/
    │   ├── admin.js                     # Admin JavaScript (QR upload)
    │   └── frontend.js                  # Frontend JavaScript (validation)
    └── css/
        └── style.css                    # Styles for admin and frontend
```

## 🎨 Customization

### Styling
The plugin includes CSS classes that can be customized:
- `.gcash-paymaya-payment-fields` - Main payment fields container
- `.payment-method` - Individual payment method container
- `.qr-code-display` - QR code display area
- `.payment-instructions` - Payment instructions area
- `.payment-details-fields` - Payment details input fields

### Hooks and Filters
The plugin uses standard WooCommerce hooks and filters for integration:
- `woocommerce_payment_gateways` - Register the gateway
- `woocommerce_thankyou_{gateway_id}` - Thank you page content
- `woocommerce_email_before_order_table` - Email instructions

## 🔮 Future Development

### Planned Features
- **Block-based Checkout Support**: Full compatibility with WooCommerce block checkout
- **Enhanced QR Code Management**: Better QR code organization and management
- **Payment Verification API**: Automated payment verification (if APIs become available)
- **Multi-language Support**: Additional language translations
- **Advanced Analytics**: Payment method usage analytics and reporting

### Contributing
Contributions are welcome! Please feel free to submit pull requests or report issues on the plugin's GitHub repository.

## 🔧 Troubleshooting

### Block-based Checkout Compatibility
- **Compatibility Notice**: The plugin shows a compatibility notice with block-based checkout
- **Solution**: Use WooCommerce's classic checkout for full functionality
- **Workaround**: The plugin will still work with block checkout but may have limited functionality
- **Future**: Block checkout support is planned for future versions

### Payment Methods Not Showing
1. **Check Gateway Settings**: Ensure the gateway is enabled in WooCommerce settings
2. **Verify Payment Methods**: Check that at least one payment method (GCash or PayMaya) is enabled
3. **WooCommerce Configuration**: Verify WooCommerce is properly configured
4. **Plugin Conflicts**: Check for conflicts with other payment plugins

### Shortcode Issues
1. **Shortcode Not Working**: Ensure WooCommerce is active and properly configured
2. **Payment Gateway Not Showing**: Check that the gateway is enabled in WooCommerce settings
3. **Page Setup**: Verify the checkout page is set in WooCommerce > Settings > Advanced > Page setup
4. **Theme Compatibility**: Some themes may require additional setup for shortcode functionality

### QR Codes Not Uploading
1. **Media Library**: Ensure WordPress media library is working
2. **File Permissions**: Check file permissions on uploads directory
3. **File Format**: Verify the image file is valid (JPG, PNG, GIF)
4. **File Size**: Ensure file size is within WordPress limits

### Orders Not Processing
1. **Gateway Configuration**: Check that the gateway is properly configured
2. **WooCommerce Order Processing**: Verify WooCommerce order processing is working
3. **Server Logs**: Check server error logs for any issues
4. **PHP Version**: Ensure PHP version meets requirements

### Payment Details Not Saving
1. **Form Validation**: Check that all required fields are filled
2. **JavaScript Errors**: Check browser console for JavaScript errors
3. **Database Permissions**: Verify database write permissions
4. **Plugin Updates**: Ensure plugin is up to date

## 📧 Support

For support and questions:
- **Author**: Janlord Luga
- **Website**: https://janlordluga.com/
- **Email**: Contact through website

## 📝 Changelog

### Version 1.2.0
- ✅ Added payment details collection (amount sent and phone number)
- ✅ Enhanced form validation with client-side error handling
- ✅ Improved user experience with visual feedback for payment method selection
- ✅ Added payment details display on thank you page and email notifications
- ✅ Enhanced CSS styling for payment details fields
- ✅ Added admin order page integration with payment details meta box
- ✅ Improved payment instructions with modern, step-by-step design
- ✅ Added amount comparison and verification status indicators
- ✅ Enhanced QR code upload functionality with separate handling for GCash and PayMaya
- ✅ Added HPOS compatibility
- ✅ Improved plugin structure and documentation

### Version 1.1.0
- ✅ Added payment details collection (amount sent and phone number)
- ✅ Enhanced form validation with client-side error handling
- ✅ Improved user experience with visual feedback for payment method selection
- ✅ Added payment details display on thank you page and email notifications
- ✅ Enhanced CSS styling for payment details fields
- ✅ Added admin order page integration with payment details meta box
- ✅ Improved payment instructions with modern, step-by-step design
- ✅ Added amount comparison and verification status indicators
- ✅ Enhanced QR code upload functionality with separate handling for GCash and PayMaya
- ✅ Added block-based checkout compatibility

### Version 1.0.0
- ✅ Initial release
- ✅ Support for GCash and PayMaya payment methods
- ✅ QR code upload functionality
- ✅ Manual payment confirmation
- ✅ WooCommerce integration
- ✅ HPOS compatibility

## 📄 License

This plugin is licensed under the **GPL v2 or later**.

**GNU General Public License v2.0 or later**

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

## 🙏 Credits

**Developed by**: Janlord Luga  
**Website**: https://janlordluga.com/

---

**Note**: This plugin is designed specifically for Philippine e-commerce stores using GCash and PayMaya payment methods. Ensure compliance with local payment regulations and security requirements. 