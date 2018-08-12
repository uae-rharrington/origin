/**
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (target) {
        if (window.checkoutConfig.quoteData.originating_quote_id !== null) {
            var shippingAddress;

            if (window.checkoutConfig.customerData.id) {
                shippingAddress = window.checkoutConfig.customerShippingAddress;
            } else {
                shippingAddress = window.checkoutConfig.guestShippingAddress;
            }

            if (target.getShippingAddressFromData() === null && shippingAddress !== null) {
                target.getShippingAddressFromData = function () {
                    return {
                        "company": shippingAddress.company ? shippingAddress.company : '',
                        "telephone": shippingAddress.telephone ? shippingAddress.telephone : '',
                        "firstname": shippingAddress.firstname ? shippingAddress.firstname : '',
                        "lastname": shippingAddress.lastname ? shippingAddress.lastname : '',
                        "street": shippingAddress.street ? $.extend({}, shippingAddress.street) : '',
                        "city": shippingAddress.city ? shippingAddress.city : '',
                        "postcode": shippingAddress.postcode ? shippingAddress.postcode : '',
                        "country_id": shippingAddress.country_id ? shippingAddress.country_id : '',
                        "region_id": shippingAddress.region_id ? shippingAddress.region_id : '',
                        "region": shippingAddress.region ? shippingAddress.region : ''
                    };
                };
            }

            if (target.getInputFieldEmailValue() === '' && shippingAddress !== null) {
                target.getInputFieldEmailValue = function () {
                    return shippingAddress.email ? shippingAddress.email : '';
                };
            }

            if (shippingAddress !== null && shippingAddress.address_id !== null) {
                target.setSelectedShippingAddress('customer-address' + shippingAddress.address_id);
            }
        }

        return target;
    };
});
