<?php
/**
 * GCash & PayMaya Payment Gateway
 *
 * @package GCash_PayMaya_Payment_Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * WC_Gateway_GCash_PayMaya class
 */
class WC_Gateway_GCash_PayMaya extends WC_Payment_Gateway
{
    /**
     * Whether GCash payment method is enabled
     *
     * @var string
     */
    public $gcash_enabled;

    /**
     * Whether PayMaya payment method is enabled
     *
     * @var string
     */
    public $paymaya_enabled;

    /**
     * GCash QR code URL
     *
     * @var string
     */
    public $gcash_qr_code;

    /**
     * PayMaya QR code URL
     *
     * @var string
     */
    public $paymaya_qr_code;

    /**
     * Payment instructions
     *
     * @var string
     */
    public $instructions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'gcash_paymaya';
        $this->icon = '';
        $this->has_fields = true;
        $this->method_title = __('GCash & PayMaya Payment Gateway', 'gcash-paymaya-payment-gateway');
        $this->method_description = __('Accept payments via QR codes for GCash and PayMaya with manual payment confirmation.', 'gcash-paymaya-payment-gateway');
        $this->supports = array('products', 'refunds', 'woocommerce_blocks');

        // Load the settings
        $this->init_form_fields();
        $this->init_settings();

        // Define properties
        $this->title = $this->get_option('title');
        $this->description = $this->get_option('description');
        $this->enabled = $this->get_option('enabled');
        $this->gcash_enabled = $this->get_option('gcash_enabled');
        $this->paymaya_enabled = $this->get_option('paymaya_enabled');
        $this->gcash_qr_code = $this->get_option('gcash_qr_code');
        $this->paymaya_qr_code = $this->get_option('paymaya_qr_code');
        $this->instructions = $this->get_option('instructions');

        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));
        add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);

        // Add payment details to order admin page
        add_action('add_meta_boxes', array($this, 'add_payment_details_meta_box'));
        add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'display_payment_details_in_order'));

        // HPOS compatibility
        add_action('before_woocommerce_init', function () {
            if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_orders_table', __FILE__, true);
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
            }
        });
    }

    /**
     * Initialize Gateway Settings Form Fields
     */
    public function init_form_fields()
    {
        $this->form_fields = array(
            'enabled' => array(
                'title' => __('Enable/Disable', 'gcash-paymaya-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable GCash & PayMaya Payment Gateway', 'gcash-paymaya-payment-gateway'),
                'default' => 'no'
            ),
            'title' => array(
                'title' => __('Title', 'gcash-paymaya-payment-gateway'),
                'type' => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'gcash-paymaya-payment-gateway'),
                'default' => __('GCash & PayMaya Payment', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'description' => array(
                'title' => __('Description', 'gcash-paymaya-payment-gateway'),
                'type' => 'textarea',
                'description' => __('This controls the description which the user sees during checkout.', 'gcash-paymaya-payment-gateway'),
                'default' => __('Pay securely using GCash or PayMaya via QR code. You will receive a confirmation email once payment is verified.', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'gcash_enabled' => array(
                'title' => __('Enable GCash', 'gcash-paymaya-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable GCash payment method', 'gcash-paymaya-payment-gateway'),
                'default' => 'yes'
            ),
            'gcash_title' => array(
                'title' => __('GCash Title', 'gcash-paymaya-payment-gateway'),
                'type' => 'text',
                'description' => __('Title for GCash payment method', 'gcash-paymaya-payment-gateway'),
                'default' => __('GCash Payment', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'gcash_qr_code' => array(
                'title' => __('GCash QR Code', 'gcash-paymaya-payment-gateway'),
                'type' => 'text',
                'description' => __('Upload or enter the URL of your GCash QR code image', 'gcash-paymaya-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                    'class' => 'qr-code-input'
                )
            ),
            'gcash_qr_code_upload' => array(
                'title' => __('Upload GCash QR Code', 'gcash-paymaya-payment-gateway'),
                'type' => 'button',
                'description' => __('Click to upload GCash QR code image', 'gcash-paymaya-payment-gateway'),
                'default' => __('Upload QR Code', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'paymaya_enabled' => array(
                'title' => __('Enable PayMaya', 'gcash-paymaya-payment-gateway'),
                'type' => 'checkbox',
                'label' => __('Enable PayMaya payment method', 'gcash-paymaya-payment-gateway'),
                'default' => 'yes'
            ),
            'paymaya_title' => array(
                'title' => __('PayMaya Title', 'gcash-paymaya-payment-gateway'),
                'type' => 'text',
                'description' => __('Title for PayMaya payment method', 'gcash-paymaya-payment-gateway'),
                'default' => __('PayMaya Payment', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'paymaya_qr_code' => array(
                'title' => __('PayMaya QR Code', 'gcash-paymaya-payment-gateway'),
                'type' => 'text',
                'description' => __('Upload or enter the URL of your PayMaya QR code image', 'gcash-paymaya-payment-gateway'),
                'default' => '',
                'desc_tip' => true,
                'custom_attributes' => array(
                    'readonly' => 'readonly',
                    'class' => 'qr-code-input'
                )
            ),
            'paymaya_qr_code_upload' => array(
                'title' => __('Upload PayMaya QR Code', 'gcash-paymaya-payment-gateway'),
                'type' => 'button',
                'description' => __('Click to upload PayMaya QR code image', 'gcash-paymaya-payment-gateway'),
                'default' => __('Upload QR Code', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
            'instructions' => array(
                'title' => __('Instructions', 'gcash-paymaya-payment-gateway'),
                'type' => 'textarea',
                'description' => __('Instructions that will be added to the thank you page and emails.', 'gcash-paymaya-payment-gateway'),
                'default' => __('Thank you for your order. Please scan the QR code with your GCash or PayMaya app to complete the payment. We will confirm your payment and process your order.', 'gcash-paymaya-payment-gateway'),
                'desc_tip' => true,
            ),
        );
    }

    /**
     * Generate button HTML for QR code upload
     */
    public function generate_button_html($key, $data)
    {
        $field_key = $this->get_field_key($key);
        $defaults = array(
            'title' => '',
            'description' => '',
            'default' => '',
        );
        $data = wp_parse_args($data, $defaults);

        ob_start();
        ?>
        <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></label>
            </th>
            <td class="forminp">
                <fieldset>
                    <button type="button" id="<?php echo esc_attr($field_key); ?>" class="button-secondary qr-upload-btn">
                        <?php echo esc_html($data['default']); ?>
                    </button>
                    <p class="description"><?php echo wp_kses_post($data['description']); ?></p>
                </fieldset>
            </td>
        </tr>
        <?php
        return ob_get_clean();
    }

    /**
     * Check if this gateway is available for use
     */
    public function is_available()
    {
        if ($this->enabled === 'no') {
            return false;
        }

        // Check if at least one payment method is enabled
        if ($this->gcash_enabled !== 'yes' && $this->paymaya_enabled !== 'yes') {
            return false;
        }

        return parent::is_available();
    }

    /**
     * Check if this gateway supports WooCommerce blocks
     */
    public function supports_woocommerce_blocks()
    {
        return true;
    }

    /**
     * Payment fields on checkout page
     */
    public function payment_fields()
    {
        echo '<div class="gcash-paymaya-payment-fields">';

        if ($this->description) {
            echo '<p>' . wp_kses_post($this->description) . '</p>';
        }

        echo '<div class="payment-methods">';

        if ($this->gcash_enabled === 'yes') {
            echo '<div class="payment-method gcash-method">';
            echo '<input type="radio" name="gcash_paymaya_method" id="gcash_method" value="gcash" checked>';
            echo '<label for="gcash_method">' . esc_html($this->get_option('gcash_title', __('GCash Payment', 'gcash-paymaya-payment-gateway'))) . '</label>';

            if (!empty($this->gcash_qr_code)) {
                echo '<div class="qr-code-display">';
                echo '<img src="' . esc_url($this->gcash_qr_code) . '" alt="GCash QR Code" style="max-width: 200px; height: auto;">';
                echo '</div>';
            } else {
                echo '<p class="no-qr-message">' . __('No QR code uploaded for GCash', 'gcash-paymaya-payment-gateway') . '</p>';
            }
            echo '</div>';
        }

        if ($this->paymaya_enabled === 'yes') {
            echo '<div class="payment-method paymaya-method">';
            echo '<input type="radio" name="gcash_paymaya_method" id="paymaya_method" value="paymaya"' . ($this->gcash_enabled !== 'yes' ? ' checked' : '') . '>';
            echo '<label for="paymaya_method">' . esc_html($this->get_option('paymaya_title', __('PayMaya Payment', 'gcash-paymaya-payment-gateway'))) . '</label>';

            if (!empty($this->paymaya_qr_code)) {
                echo '<div class="qr-code-display">';
                echo '<img src="' . esc_url($this->paymaya_qr_code) . '" alt="PayMaya QR Code" style="max-width: 200px; height: auto;">';
                echo '</div>';
            } else {
                echo '<p class="no-qr-message">' . __('No QR code uploaded for PayMaya', 'gcash-paymaya-payment-gateway') . '</p>';
            }
            echo '</div>';
        }

        echo '</div>';

        // Payment details input fields
        echo '<div class="payment-details-fields">';
        echo '<h4>' . __('Payment Details', 'gcash-paymaya-payment-gateway') . '</h4>';

        // Amount field
        echo '<div class="form-row form-row-wide">';
        echo '<label for="gcash_paymaya_amount">' . __('Amount Sent (PHP)', 'gcash-paymaya-payment-gateway') . ' <span class="required">*</span></label>';
        echo '<input type="number" id="gcash_paymaya_amount" name="gcash_paymaya_amount" step="0.01" min="0" required>';
        echo '<small>' . __('Enter the exact amount you sent via the payment app', 'gcash-paymaya-payment-gateway') . '</small>';
        echo '</div>';

        // Phone number field
        echo '<div class="form-row form-row-wide">';
        echo '<label for="gcash_paymaya_phone">' . __('Phone Number Used', 'gcash-paymaya-payment-gateway') . ' <span class="required">*</span></label>';
        echo '<input type="tel" id="gcash_paymaya_phone" name="gcash_paymaya_phone" pattern="[0-9+\-\s()]+" required>';
        echo '<small>' . __('Enter the phone number you used to send the payment', 'gcash-paymaya-payment-gateway') . '</small>';
        echo '</div>';
        echo '</div>';

        echo '<div class="payment-instructions">';
        echo '<h4>' . __('Payment Instructions', 'gcash-paymaya-payment-gateway') . '</h4>';
        echo '<ol>';
        echo '<li>' . __('Select your preferred payment method above', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '<li>' . __('Scan the QR code with your mobile app', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '<li>' . __('Complete the payment in your app', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '<li>' . __('Enter the amount you sent and the phone number you used', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '<li>' . __('Click "Place Order" to submit your order', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '<li>' . __('We will verify your payment and process your order', 'gcash-paymaya-payment-gateway') . '</li>';
        echo '</ol>';
        echo '</div>';

        echo '</div>';
    }

    /**
     * Process the payment
     */
    public function process_payment($order_id)
    {
        $order = wc_get_order($order_id);
        $payment_method = isset($_POST['gcash_paymaya_method']) ? sanitize_text_field($_POST['gcash_paymaya_method']) : 'gcash';
        $amount_sent = isset($_POST['gcash_paymaya_amount']) ? sanitize_text_field($_POST['gcash_paymaya_amount']) : '';
        $phone_number = isset($_POST['gcash_paymaya_phone']) ? sanitize_text_field($_POST['gcash_paymaya_phone']) : '';

        // Mark as on-hold (we're awaiting the payment)
        $order->update_status('on-hold', __('Awaiting payment confirmation via ' . ucfirst($payment_method), 'gcash-paymaya-payment-gateway'));

        // Store payment method used
        $order->update_meta_data('_gcash_paymaya_method', $payment_method);
        $order->update_meta_data('_gcash_paymaya_amount', $amount_sent);
        $order->update_meta_data('_gcash_paymaya_phone', $phone_number);
        $order->save();

        // Reduce stock levels
        wc_reduce_stock_levels($order_id);

        // Remove cart
        WC()->cart->empty_cart();

        // Return thankyou redirect
        return array(
            'result' => 'success',
            'redirect' => $this->get_return_url($order)
        );
    }

    /**
     * Output for the order received page
     */
    public function thankyou_page($order_id)
    {
        $order = wc_get_order($order_id);
        $payment_method = $order->get_meta('_gcash_paymaya_method', true);
        $amount_sent = $order->get_meta('_gcash_paymaya_amount', true);
        $phone_number = $order->get_meta('_gcash_paymaya_phone', true);

        if ($order && $order->get_payment_method() === $this->id) {
            echo '<div class="gcash-paymaya-thankyou">';

            if ($payment_method === 'gcash' && !empty($this->gcash_qr_code)) {
                echo '<div class="qr-code-section">';
                echo '<h3>' . __('GCash QR Code', 'gcash-paymaya-payment-gateway') . '</h3>';
                echo '<img src="' . esc_url($this->gcash_qr_code) . '" alt="GCash QR Code" style="max-width: 300px; height: auto;">';
                echo '</div>';
            } elseif ($payment_method === 'paymaya' && !empty($this->paymaya_qr_code)) {
                echo '<div class="qr-code-section">';
                echo '<h3>' . __('PayMaya QR Code', 'gcash-paymaya-payment-gateway') . '</h3>';
                echo '<img src="' . esc_url($this->paymaya_qr_code) . '" alt="PayMaya QR Code" style="max-width: 300px; height: auto;">';
                echo '</div>';
            }

            echo '<div class="order-details">';
            echo '<h3>' . __('Order Details', 'gcash-paymaya-payment-gateway') . '</h3>';
            echo '<p><strong>' . __('Order Number:', 'gcash-paymaya-payment-gateway') . '</strong> ' . $order->get_order_number() . '</p>';
            echo '<p><strong>' . __('Total Amount:', 'gcash-paymaya-payment-gateway') . '</strong> ' . $order->get_formatted_order_total() . '</p>';
            echo '<p><strong>' . __('Payment Method:', 'gcash-paymaya-payment-gateway') . '</strong> ' . ucfirst($payment_method) . '</p>';

            // Display payment details if provided
            if (!empty($amount_sent)) {
                echo '<p><strong>' . __('Amount Sent:', 'gcash-paymaya-payment-gateway') . '</strong> ₱' . number_format((float) $amount_sent, 2) . '</p>';
            }
            if (!empty($phone_number)) {
                echo '<p><strong>' . __('Phone Number Used:', 'gcash-paymaya-payment-gateway') . '</strong> ' . esc_html($phone_number) . '</p>';
            }
            echo '</div>';

            echo '</div>';
        }
    }

    /**
     * Add content to the WC emails
     */
    public function email_instructions($order, $sent_to_admin, $plain_text = false)
    {
        if ($sent_to_admin || $order->get_payment_method() !== $this->id || $order->get_status() === 'on-hold') {
            return;
        }

        $payment_method = $order->get_meta('_gcash_paymaya_method', true);
        $amount_sent = $order->get_meta('_gcash_paymaya_amount', true);
        $phone_number = $order->get_meta('_gcash_paymaya_phone', true);

        if ($plain_text) {
            echo "\n\n" . __('Payment Instructions:', 'gcash-paymaya-payment-gateway') . "\n";
            echo "============================================\n\n";
            echo __('Please scan the QR code with your ' . ucfirst($payment_method) . ' app to complete the payment.', 'gcash-paymaya-payment-gateway') . "\n\n";
            echo __('Order Number:', 'gcash-paymaya-payment-gateway') . ' ' . $order->get_order_number() . "\n";
            echo __('Total Amount:', 'gcash-paymaya-payment-gateway') . ' ' . $order->get_formatted_order_total() . "\n";

            if (!empty($amount_sent)) {
                echo __('Amount Sent:', 'gcash-paymaya-payment-gateway') . ' ₱' . number_format((float) $amount_sent, 2) . "\n";
            }
            if (!empty($phone_number)) {
                echo __('Phone Number Used:', 'gcash-paymaya-payment-gateway') . ' ' . $phone_number . "\n";
            }
            echo "\n";
        } else {
            echo '<h2>' . __('Payment Instructions', 'gcash-paymaya-payment-gateway') . '</h2>';
            echo '<p>' . __('Please scan the QR code with your ' . ucfirst($payment_method) . ' app to complete the payment.', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '<p><strong>' . __('Order Number:', 'gcash-paymaya-payment-gateway') . '</strong> ' . $order->get_order_number() . '</p>';
            echo '<p><strong>' . __('Total Amount:', 'gcash-paymaya-payment-gateway') . '</strong> ' . $order->get_formatted_order_total() . '</p>';

            if (!empty($amount_sent)) {
                echo '<p><strong>' . __('Amount Sent:', 'gcash-paymaya-payment-gateway') . '</strong> ₱' . number_format((float) $amount_sent, 2) . '</p>';
            }
            if (!empty($phone_number)) {
                echo '<p><strong>' . __('Phone Number Used:', 'gcash-paymaya-payment-gateway') . '</strong> ' . esc_html($phone_number) . '</p>';
            }
        }
    }

    /**
     * Process refund
     */
    public function process_refund($order_id, $amount = null, $reason = '')
    {
        $order = wc_get_order($order_id);

        if (!$order) {
            return new WP_Error('error', __('Order not found', 'gcash-paymaya-payment-gateway'));
        }

        // For manual payment gateways, we just mark the refund as processed
        // The actual refund would need to be processed manually
        $order->add_order_note(
            sprintf(
                __('Refund processed manually. Amount: %s. Reason: %s', 'gcash-paymaya-payment-gateway'),
                wc_price($amount),
                $reason
            )
        );

        return true;
    }

    /**
     * Add payment details meta box to order admin page
     */
    public function add_payment_details_meta_box()
    {
        add_meta_box(
            'gcash_paymaya_payment_details',
            __('GCash & PayMaya Payment Details', 'gcash-paymaya-payment-gateway'),
            array($this, 'display_payment_details_meta_box_content'),
            'shop_order',
            'side',
            'default'
        );
    }

    /**
     * Display payment details in the order admin page
     */
    public function display_payment_details_in_order($order)
    {
        $payment_method = $order->get_meta('_gcash_paymaya_method', true);
        $amount_sent = $order->get_meta('_gcash_paymaya_amount', true);
        $phone_number = $order->get_meta('_gcash_paymaya_phone', true);

        echo '<div class="gcash-paymaya-order-details">';
        echo '<h4>' . __('GCash & PayMaya Payment Details', 'gcash-paymaya-payment-gateway') . '</h4>';

        echo '<p><strong>' . __('Payment Method:', 'gcash-paymaya-payment-gateway') . '</strong> ' . ucfirst($payment_method) . '</p>';

        if ($payment_method === 'gcash') {
            if (!empty($this->gcash_qr_code)) {
                echo '<div class="qr-code-section">';
                echo '<h5>' . __('GCash QR Code', 'gcash-paymaya-payment-gateway') . '</h5>';
                echo '<img src="' . esc_url($this->gcash_qr_code) . '" alt="GCash QR Code" style="max-width: 150px; height: auto;">';
                echo '</div>';
            } else {
                echo '<p>' . __('No QR code uploaded for GCash.', 'gcash-paymaya-payment-gateway') . '</p>';
            }
        } elseif ($payment_method === 'paymaya') {
            if (!empty($this->paymaya_qr_code)) {
                echo '<div class="qr-code-section">';
                echo '<h5>' . __('PayMaya QR Code', 'gcash-paymaya-payment-gateway') . '</h5>';
                echo '<img src="' . esc_url($this->paymaya_qr_code) . '" alt="PayMaya QR Code" style="max-width: 150px; height: auto;">';
                echo '</div>';
            } else {
                echo '<p>' . __('No QR code uploaded for PayMaya.', 'gcash-paymaya-payment-gateway') . '</p>';
            }
        }

        if (!empty($amount_sent)) {
            echo '<p><strong>' . __('Amount Sent:', 'gcash-paymaya-payment-gateway') . '</strong> ₱' . number_format((float) $amount_sent, 2) . '</p>';
        }
        if (!empty($phone_number)) {
            echo '<p><strong>' . __('Phone Number Used:', 'gcash-paymaya-payment-gateway') . '</strong> ' . esc_html($phone_number) . '</p>';
        }

        echo '</div>';
    }

    /**
     * Display payment details meta box content
     */
    public function display_payment_details_meta_box_content($post)
    {
        $order = wc_get_order($post->ID);

        if (!$order || $order->get_payment_method() !== $this->id) {
            echo '<p>' . __('This order was not paid using GCash & PayMaya Payment Gateway.', 'gcash-paymaya-payment-gateway') . '</p>';
            return;
        }

        $payment_method = $order->get_meta('_gcash_paymaya_method', true);
        $amount_sent = $order->get_meta('_gcash_paymaya_amount', true);
        $phone_number = $order->get_meta('_gcash_paymaya_phone', true);
        $order_total = $order->get_total();

        echo '<div class="gcash-paymaya-meta-box">';

        // Payment method
        echo '<p><strong>' . __('Payment Method:', 'gcash-paymaya-payment-gateway') . '</strong><br>';
        echo '<span style="color: #0073aa; font-weight: bold;">' . ucfirst($payment_method) . '</span></p>';

        // Amount comparison
        if (!empty($amount_sent)) {
            $amount_sent_float = (float) $amount_sent;
            $order_total_float = (float) $order_total;
            $amount_match = abs($amount_sent_float - $order_total_float) < 0.01; // Allow for small rounding differences

            echo '<p><strong>' . __('Amount Sent:', 'gcash-paymaya-payment-gateway') . '</strong><br>';
            echo '<span style="font-size: 16px; font-weight: bold;">₱' . number_format($amount_sent_float, 2) . '</span></p>';

            echo '<p><strong>' . __('Order Total:', 'gcash-paymaya-payment-gateway') . '</strong><br>';
            echo '<span style="font-size: 16px;">₱' . number_format($order_total_float, 2) . '</span></p>';

            // Amount verification status
            if ($amount_match) {
                echo '<p style="color: #46b450; font-weight: bold;">✓ ' . __('Amount matches order total', 'gcash-paymaya-payment-gateway') . '</p>';
            } else {
                echo '<p style="color: #dc3232; font-weight: bold;">⚠ ' . __('Amount does not match order total', 'gcash-paymaya-payment-gateway') . '</p>';
                echo '<p style="color: #dc3232; font-size: 12px;">' . __('Difference: ₱' . number_format(abs($amount_sent_float - $order_total_float), 2)) . '</p>';
            }
        }

        // Phone number
        if (!empty($phone_number)) {
            echo '<p><strong>' . __('Phone Number Used:', 'gcash-paymaya-payment-gateway') . '</strong><br>';
            echo '<span style="font-family: monospace; background: #f1f1f1; padding: 2px 4px; border-radius: 3px;">' . esc_html($phone_number) . '</span></p>';
        }

        // QR Code display
        if ($payment_method === 'gcash' && !empty($this->gcash_qr_code)) {
            echo '<div style="text-align: center; margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 4px;">';
            echo '<p style="margin: 0 0 5px 0; font-weight: bold; color: #0073aa;">' . __('GCash QR Code', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '<img src="' . esc_url($this->gcash_qr_code) . '" alt="GCash QR Code" style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px; background: white;">';
            echo '</div>';
        } elseif ($payment_method === 'paymaya' && !empty($this->paymaya_qr_code)) {
            echo '<div style="text-align: center; margin: 10px 0; padding: 10px; background: #f9f9f9; border-radius: 4px;">';
            echo '<p style="margin: 0 0 5px 0; font-weight: bold; color: #0073aa;">' . __('PayMaya QR Code', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '<img src="' . esc_url($this->paymaya_qr_code) . '" alt="PayMaya QR Code" style="max-width: 120px; height: auto; border: 1px solid #ddd; padding: 5px; background: white;">';
            echo '</div>';
        }

        // Verification status
        $order_status = $order->get_status();
        if ($order_status === 'on-hold') {
            echo '<div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; border-radius: 4px; margin-top: 10px;">';
            echo '<p style="margin: 0; color: #856404; font-weight: bold;">' . __('Payment Verification Required', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '<p style="margin: 5px 0 0 0; font-size: 12px; color: #856404;">' . __('Please verify the payment details above and update the order status accordingly.', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '</div>';
        } elseif ($order_status === 'processing' || $order_status === 'completed') {
            echo '<div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 10px; border-radius: 4px; margin-top: 10px;">';
            echo '<p style="margin: 0; color: #155724; font-weight: bold;">✓ ' . __('Payment Verified', 'gcash-paymaya-payment-gateway') . '</p>';
            echo '</div>';
        }

        echo '</div>';
    }
}