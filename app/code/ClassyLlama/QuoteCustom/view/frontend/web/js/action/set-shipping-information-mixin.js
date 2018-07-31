define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            var isQuoteRequest = (
                typeof window.checkoutConfig.isQuoteRequest != 'undefined'
                && window.checkoutConfig.isQuoteRequest
            );

            shippingAddress['extension_attributes']['is_quote_request'] = isQuoteRequest;
            shippingAddress['extension_attributes']['customer_email'] = quote.guestEmail;
            return originalAction();
        });
    };
});