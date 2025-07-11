<?php
/**
 * GCash & PayMaya Payment Gateway - Blocks Support
 *
 * @package GCash_PayMaya_Payment_Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * GCash_PayMaya_Blocks_Support class
 */
class GCash_PayMaya_Blocks_Support extends Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType
{
    /**
     * Payment method name/id/slug.
     *
     * @var string
     */
    protected $name = 'gcash_paymaya';

    /**
     * Initializes the payment method type.
     */
    public function initialize()
    {
        $this->settings = get_option('woocommerce_gcash_paymaya_settings', array());
    }

    /**
     * Returns if this payment method should be active.
     *
     * @return boolean
     */
    public function is_active()
    {
        return true;
    }

    /**
     * Returns an array of scripts/handles to be registered for this payment method.
     *
     * @return array
     */
    public function get_payment_method_script_handles()
    {
        return array();
    }

    /**
     * Returns an array of key=>value pairs of data made available to the payment methods script.
     *
     * @return array
     */
    public function get_payment_method_data()
    {
        return array(
            'title' => 'GCash & PayMaya Payment',
            'description' => 'Pay via GCash or PayMaya',
            'supports' => array('products', 'refunds'),
        );
    }
}