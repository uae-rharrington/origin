/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

require([
    'jquery',
    'rjsResolver'
], function ($, resolver) {
    var hidePaymentOptions = function() {
        if (typeof window.checkoutConfig != 'undefined' && window.checkoutConfig.isQuoteRequest) {
            $('#giftcardaccount-placer').hide();
            $('.payment-option.discount-code').hide();
        }
    };

    resolver(hidePaymentOptions.bind(null));
});
