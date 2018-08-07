/**
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress(),
            orderComment;

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes !== undefined) {
                orderComment = shippingAddress.customAttributes['order_comment'];
            }

            if (orderComment === undefined) {
                orderComment = $('#onepage-checkout-shipping-method-additional-load')
                    .find("div[name='shippingAddress.custom_attributes.order_comment']")
                    .find('textarea')
                    .val();
            }

            shippingAddress['extension_attributes']['order_comment'] = orderComment ? orderComment : '';
            return originalAction();
        });
    };
});
