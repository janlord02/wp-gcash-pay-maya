jQuery(document).ready(function($) {
    // QR Code Upload functionality
    $('.qr-upload-btn').on('click', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var fieldId = button.attr('id');
        
        // Determine which payment method this button is for
        var isGCash = false;
        var isPayMaya = false;
        
        if (fieldId.includes('gcash_qr_code_upload')) {
            isGCash = true;
        } else if (fieldId.includes('paymaya_qr_code_upload')) {
            isPayMaya = true;
        }
        
        // Get the corresponding input field
        var qrInputField;
        if (isGCash) {
            qrInputField = $('input[name="woocommerce_gcash_paymaya_gcash_qr_code"]');
        } else if (isPayMaya) {
            qrInputField = $('input[name="woocommerce_gcash_paymaya_paymaya_qr_code"]');
        } else {
            console.error('Could not determine payment method for button:', fieldId);
            return;
        }
        
        // Create media frame
        var frame = wp.media({
            title: isGCash ? 'Select GCash QR Code Image' : 'Select PayMaya QR Code Image',
            button: {
                text: 'Use this image'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        // When image selected
        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            qrInputField.val(attachment.url);
            
            // Trigger change event to enable save button
            qrInputField.trigger('change');
            
            // Update button text
            button.text('Change QR Code');
            
            // Show preview
            showQRPreview(attachment.url, isGCash, isPayMaya);
        });
        
        frame.open();
    });
    
    // Remove QR Code functionality
    $(document).on('click', '.remove-qr-btn', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var isGCash = button.hasClass('gcash-remove');
        var isPayMaya = button.hasClass('paymaya-remove');
        
        var qrInputField;
        var uploadBtn;
        
        if (isGCash) {
            qrInputField = $('input[name="woocommerce_gcash_paymaya_gcash_qr_code"]');
            uploadBtn = $('#woocommerce_gcash_paymaya_gcash_qr_code_upload');
        } else if (isPayMaya) {
            qrInputField = $('input[name="woocommerce_gcash_paymaya_paymaya_qr_code"]');
            uploadBtn = $('#woocommerce_gcash_paymaya_paymaya_qr_code_upload');
        } else {
            console.error('Could not determine payment method for remove button');
            return;
        }
        
        // Clear the input
        qrInputField.val('');
        
        // Trigger change event to enable save button
        qrInputField.trigger('change');
        
        // Reset button text
        uploadBtn.text('Upload QR Code');
        
        // Remove preview
        removeQRPreview(isGCash, isPayMaya);
    });
    
    // Show QR Code preview
    function showQRPreview(imageUrl, isGCash, isPayMaya) {
        var previewContainer;
        var containerClass;
        var removeBtnClass;
        
        if (isGCash) {
            previewContainer = $('.gcash-qr-preview');
            containerClass = 'gcash-qr-preview';
            removeBtnClass = 'remove-qr-btn gcash-remove';
        } else if (isPayMaya) {
            previewContainer = $('.paymaya-qr-preview');
            containerClass = 'paymaya-qr-preview';
            removeBtnClass = 'remove-qr-btn paymaya-remove';
        } else {
            console.error('Invalid payment method for preview');
            return;
        }
        
        if (previewContainer.length === 0) {
            // Create preview container if it doesn't exist
            var previewHtml = '<div class="' + containerClass + '" style="margin-top: 10px;">' +
                '<img src="' + imageUrl + '" style="max-width: 200px; height: auto; border: 1px solid #ddd; padding: 5px; background: white; border-radius: 4px;">' +
                '<br><button type="button" class="' + removeBtnClass + ' button-secondary" style="margin-top: 5px;">Remove QR Code</button>' +
                '</div>';
            
            var qrInputField;
            if (isGCash) {
                qrInputField = $('input[name="woocommerce_gcash_paymaya_gcash_qr_code"]');
            } else {
                qrInputField = $('input[name="woocommerce_gcash_paymaya_paymaya_qr_code"]');
            }
            
            qrInputField.closest('tr').after('<tr><td colspan="2">' + previewHtml + '</td></tr>');
        } else {
            // Update existing preview
            previewContainer.find('img').attr('src', imageUrl);
        }
    }
    
    // Remove QR Code preview
    function removeQRPreview(isGCash, isPayMaya) {
        var previewContainer;
        
        if (isGCash) {
            previewContainer = $('.gcash-qr-preview');
        } else if (isPayMaya) {
            previewContainer = $('.paymaya-qr-preview');
        } else {
            console.error('Invalid payment method for removing preview');
            return;
        }
        
        previewContainer.closest('tr').remove();
    }
    
    // Initialize previews on page load
    function initPreviews() {
        var gcashUrl = $('input[name="woocommerce_gcash_paymaya_gcash_qr_code"]').val();
        var paymayaUrl = $('input[name="woocommerce_gcash_paymaya_paymaya_qr_code"]').val();
        
        if (gcashUrl) {
            showQRPreview(gcashUrl, true, false);
            $('#woocommerce_gcash_paymaya_gcash_qr_code_upload').text('Change QR Code');
        }
        
        if (paymayaUrl) {
            showQRPreview(paymayaUrl, false, true);
            $('#woocommerce_gcash_paymaya_paymaya_qr_code_upload').text('Change QR Code');
        }
    }
    
    // Initialize on page load
    initPreviews();
    
    // Handle form field changes to enable save button
    $('input, textarea, select').on('change', function() {
        $('.woocommerce-save-button').prop('disabled', false);
    });
    
    // Handle checkbox changes
    $('input[type="checkbox"]').on('change', function() {
        $('.woocommerce-save-button').prop('disabled', false);
    });
}); 