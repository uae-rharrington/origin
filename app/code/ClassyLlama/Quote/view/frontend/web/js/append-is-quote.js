/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    /** Override default place order action and add agreement_ids to request */
    return function (paymentData) {
        if (paymentData['extension_attributes'] === undefined) {
            paymentData['extension_attributes'] = {};
        }

        var isQuoteRequest = (
            typeof window.checkoutConfig.isQuoteRequest != 'undefined'
            && window.checkoutConfig.isQuoteRequest
        );
        paymentData['extension_attributes']['is_quote_request'] = isQuoteRequest;
    };
});
