/**
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
define([
    'jquery'
], function ($) {
    'use strict';

    return function (target) {
        var guestShippingAddress = window.checkoutConfig.guestShippingAddress;
        if (target.getShippingAddressFromData() === null) {
            target.getShippingAddressFromData = function () {
                return {
                    "company": guestShippingAddress.company ? guestShippingAddress.company : '',
                    "telephone": guestShippingAddress.telephone ? guestShippingAddress.telephone : '',
                    "firstname": guestShippingAddress.firstname ? guestShippingAddress.firstname : '',
                    "lastname": guestShippingAddress.lastname ? guestShippingAddress.lastname : '',
                    "street": guestShippingAddress.street ? $.extend({}, guestShippingAddress.street) : '',
                    "city": guestShippingAddress.city ? guestShippingAddress.city : '',
                    "postcode": guestShippingAddress.postcode ? guestShippingAddress.postcode : '',
                    "country_id": guestShippingAddress.country_id ? guestShippingAddress.country_id : '',
                    "region_id": guestShippingAddress.region_id ? guestShippingAddress.region_id : '',
                    "region": guestShippingAddress.region ? guestShippingAddress.region : ''
                };
            };
        }

        if (target.getInputFieldEmailValue() === '') {
            target.getInputFieldEmailValue = function () {
                return guestShippingAddress.email ? guestShippingAddress.email : '';
            };
        }
        return target;
    };
});