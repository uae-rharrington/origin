/**
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
define([
    'ko',
    'Magento_Checkout/js/action/set-shipping-information',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/action/redirect-on-success'
], function (ko, setShippingInformationAction, stepNavigator, redirectOnSuccessAction) {
    'use strict';

    var mixin = {
        isQuoteCheckout: ko.computed(function () {
            var pageUrl = decodeURIComponent(window.location.search.substring(1)),
                urlParams = pageUrl.split('&'),
                param;

            for (var i = 0; i < urlParams.length; i++) {
                param = urlParams[i].split('=');

                if (param[0] === 'quote') {
                    return param[1] === undefined ? false : param[1];
                }
            }

            return false;
        }),

        /**
         * Set shipping information handler
         */
        setShippingInformation: function () {
            if (this.validateShippingInformation()) {
                setShippingInformationAction().done(
                    function () {
                        var isQuoteRequest = (
                            typeof window.checkoutConfig.isQuoteRequest != 'undefined'
                            && window.checkoutConfig.isQuoteRequest
                        );
                        if (isQuoteRequest){
                            redirectOnSuccessAction.execute();
                        } else {
                            stepNavigator.next();
                        }
                    }
                );
            }
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});