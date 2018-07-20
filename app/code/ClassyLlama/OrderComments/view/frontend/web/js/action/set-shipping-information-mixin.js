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

            if (shippingAddress.customAttributes !== undefined) {
                shippingAddress['extension_attributes']['order_comment'] = shippingAddress.customAttributes['order_comment'];
            } else {
                var orderComment = $('#onepage-checkout-shipping-method-additional-load')
                    .find("div[name='shippingAddress.custom_attributes.order_comment']")
                    .find('textarea')
                    .val();
                if (orderComment !== undefined) {
                    shippingAddress['extension_attributes']['order_comment'] = orderComment;
                }
            }
            return originalAction();
        });
    };
});