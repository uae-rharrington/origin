/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery',
    'mage/utils/wrapper',
    'ClassyLlama_Quote/js/append-is-quote'
], function ($, wrapper, isQuoteAppender) {
    'use strict';

    return function (placeOrderAction) {

        /** Override default place order action and add agreement_ids to request */
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            isQuoteAppender(paymentData);

            return originalAction(paymentData, messageContainer);
        });
    };
});
