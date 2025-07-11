(function($) {
    'use strict';

    // Payment details validation and enhancement
    function initPaymentDetailsValidation() {
        const amountField = $('#gcash_paymaya_amount');
        const phoneField = $('#gcash_paymaya_phone');
        const orderTotal = parseFloat($('.order-total .amount').text().replace(/[^\d.-]/g, '')) || 0;

        // Set minimum amount to order total
        if (amountField.length && orderTotal > 0) {
            amountField.attr('min', orderTotal);
            amountField.attr('placeholder', '₱' + orderTotal.toFixed(2));
        }

        // Amount field validation
        amountField.on('input', function() {
            const value = parseFloat($(this).val());
            const minAmount = parseFloat($(this).attr('min')) || 0;
            
            if (value < minAmount) {
                $(this).addClass('error');
                if (!$(this).next('.error-message').length) {
                    $(this).after('<span class="error-message">Amount must be at least ₱' + minAmount.toFixed(2) + '</span>');
                }
            } else {
                $(this).removeClass('error');
                $(this).next('.error-message').remove();
            }
        });

        // Phone number formatting
        phoneField.on('input', function() {
            let value = $(this).val().replace(/[^\d+\-\s()]/g, '');
            $(this).val(value);
        });

        // Form submission validation
        $('form.checkout, .wc-block-checkout__form').on('submit', function(e) {
            if ($('#payment_method_gcash_paymaya').is(':checked')) {
                const amount = parseFloat(amountField.val());
                const phone = phoneField.val().trim();
                const minAmount = parseFloat(amountField.attr('min')) || 0;
                
                let hasError = false;
                
                // Clear previous error messages
                $('.error-message').remove();
                $('.error').removeClass('error');
                
                // Validate amount
                if (!amount || amount < minAmount) {
                    amountField.addClass('error');
                    amountField.after('<span class="error-message">Please enter a valid amount (minimum ₱' + minAmount.toFixed(2) + ')</span>');
                    hasError = true;
                }
                
                // Validate phone number
                if (!phone) {
                    phoneField.addClass('error');
                    phoneField.after('<span class="error-message">Please enter your phone number</span>');
                    hasError = true;
                } else if (phone.length < 10) {
                    phoneField.addClass('error');
                    phoneField.after('<span class="error-message">Please enter a valid phone number</span>');
                    hasError = true;
                }
                
                if (hasError) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: $('.payment-details-fields').offset().top - 100
                    }, 500);
                    return false;
                }
            }
        });

        // Payment method change handler
        $('input[name="gcash_paymaya_method"]').on('change', function() {
            const selectedMethod = $(this).val();
            const amountField = $('#gcash_paymaya_amount');
            
            // Update placeholder text based on selected method
            if (orderTotal > 0) {
                amountField.attr('placeholder', '₱' + orderTotal.toFixed(2) + ' (via ' + selectedMethod.charAt(0).toUpperCase() + selectedMethod.slice(1) + ')');
            }
        });
    }

    // Initialize when page loads
    $(document).ready(function() {
        initPaymentDetailsValidation();
    });

    // Re-initialize on WooCommerce checkout update
    $(document.body).on('updated_checkout', function() {
        initPaymentDetailsValidation();
    });

    // Add some visual feedback for the payment method selection
    $('input[name="gcash_paymaya_method"]').on('change', function() {
        $('.payment-method').removeClass('selected');
        $(this).closest('.payment-method').addClass('selected');
    });

    // Trigger change event on page load to set initial state
    $('input[name="gcash_paymaya_method"]:checked').trigger('change');

    // Block checkout compatibility
    if (typeof wp !== 'undefined' && wp.data && wp.data.subscribe) {
        wp.data.subscribe(function() {
            setTimeout(function() {
                initPaymentDetailsValidation();
            }, 100);
        });
    }

})(jQuery); 