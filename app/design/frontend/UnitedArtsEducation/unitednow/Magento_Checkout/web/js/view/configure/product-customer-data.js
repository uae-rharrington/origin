/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

require([
    'jquery',
    'Magento_Customer/js/customer-data',
    'domReady!'
], function ($, customerData) {
    'use strict';

    var selectors = {
        qtySelector: '#product_addtocart_form [name="qty"]',
        productIdSelector: '#product_addtocart_form [name="product"]'
    },
    cartData = customerData.get('cart'),
    productId = $(selectors.productIdSelector).val(),
    productQty,
    productQtyInput,

    /**
    * Updates product's qty input value according to actual data
    */
    updateQty = function () {

        if (productQty || productQty === 0) {
            productQtyInput = productQtyInput || $(selectors.qtySelector);

            if (productQtyInput && productQty.toString() !== productQtyInput.val()) {
                productQtyInput.val(productQty);
            }
        }
    },

    /**
    * Sets productQty according to cart data from customer-data
    *
    * @param {Object} data - cart data from customer-data
    */
    setProductQty = function (data) {
        var product;

        if (!(data && data.items && data.items.length && productId)) {
            return;
        }
        // EDIT: Avoid "find()" array method, for IE11 compatibility
        for (var i=0; i<data.items.length; i++) {
            if (data.items[i]['product_id'] === productId || data.items[i]['item_id'] === productId) {
                product = data.items[i];
                break;
            }
        }

        if (!product) {
            return;
        }
        productQty = product.qty;
    };

    cartData.subscribe(function (updateCartData) {
        setProductQty(updateCartData);
        updateQty();
    });

    setProductQty(cartData());
    updateQty();
});
