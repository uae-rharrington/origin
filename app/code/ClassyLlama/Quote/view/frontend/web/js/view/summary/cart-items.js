/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define(
    [
        'Magento_Checkout/js/view/summary/cart-items',
        'Magento_Checkout/js/model/totals'
    ],
    function (Component, totals) {
        'use strict';
        return Component.extend({
            getItems: function () {
                var items = totals.getItems()();
                items.forEach(function(item) {
                    item.row_total = item.price;
                    item.row_total_incl_tax = item.price_incl_tax;
                    item.base_row_total = item.base_price;
                    item.base_row_total_incl_tax = item.base_price_incl_tax;
                });
                totals.getItems(items);
                return totals.getItems();
            },
            isItemsBlockExpanded: function () {
                return true;
            }
        });
    }
);
