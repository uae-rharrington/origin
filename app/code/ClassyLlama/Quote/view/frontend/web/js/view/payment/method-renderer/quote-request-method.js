/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'ClassyLlama_Quote/payment/quote-request'
            },

            /** Returns is method available */
            isAvailable: function() {
                var available = typeof window.checkoutConfig != 'undefined' && window.checkoutConfig.isQuoteRequest;

                if (available && !this.paymentMethodSelected) {
                    this.selectPaymentMethod();
                    this.paymentMethodSelected = true;
                }

                return available;
            }
        });
    }
);
