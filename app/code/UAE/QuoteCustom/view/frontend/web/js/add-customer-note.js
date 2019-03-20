/**
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
require([
    'jquery',
    'mage/translate',
    'Magento_Customer/js/model/customer',
    'rjsResolver'
], function ($, $translate, customer, resolver) {
    var addQuoteRequestCustomerNote = function() {
        if (typeof window.checkoutConfig != 'undefined' && window.checkoutConfig.isQuoteRequest) {
            var checkoutShippingStepElement = $('#shipping'),
                quoteLifetime = window.checkoutConfig.quoteLifetime;

            if (checkoutShippingStepElement.length) {
                var message = '';

                if (customer.isLoggedIn()) {
                    message = 'To use our instant quote feature please fill out the form below. We will email you a '
                        + 'quote which is valid for ' + quoteLifetime + ' days. The quote will also be stored in the "my account" section '
                        + 'of our website to access later.';
                } else {
                    message = 'To use our instant quote feature please fill out the form below. We will email you a '
                        + 'quote which is valid for ' + quoteLifetime + ' days. To be able to view the quote from the "my account" section '
                        + 'of our website to access later, be sure to log in now or create an account after you submit the quote.';
                }

                checkoutShippingStepElement.prepend(
                    '<div class="quote-request-customer-note-wrapper"><span class="quote-request-customer-note">'
                    + $translate(message)
                    + '</span></div>'
                );
            }
        }
    };

    resolver(addQuoteRequestCustomerNote.bind(null));
});
