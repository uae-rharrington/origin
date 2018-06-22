/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
define(function() {
    'use strict';

    return function(CheckoutDetails) {
        return CheckoutDetails.extend({
            defaults: {
                template: 'UAE_AdvancedCheckout/view/summary/item/details'
            },

            getSku: function(itemId) {
                var itemsData = window.checkoutConfig.quoteItemData;
                var productSku = null;
                itemsData.forEach(function(item) {
                    if (item.item_id == itemId) {
                        productSku = item.sku;
                    }
                });
                if (productSku != null) {
                    return productSku;
                } else {
                    return '';
                }
            }
        });
    };
});
