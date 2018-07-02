/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'UAE_AdvancedCheckout/view/summary/qualifying-items'
        },

        /**
         * @return string
         */
        getQualifyingItemsMessage: function () {
            var message = '';
            if (window.checkoutConfig.qualifyingItems) {
                message = window.checkoutConfig.qualifyingItems.message;
            }

            return message
        },

        /**
         * @returns {Boolean}
         */
        isDisplayed: function () {
            return this.getQualifyingItemsMessage() != ''; //eslint-disable-line eqeqeq
        }
    });
});
