/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery',
    'underscore',
    'ko',
    'Magento_Customer/js/customer-data'
], function ($, _, ko, customerData) {
    var isCustomerLoggedIn = ko.observable(null);
    var customer = customerData.get('customer');
    var setIsCustomerIsLoggedIn = function(_customer) {
        isCustomerLoggedIn(_customer && typeof _customer.fullname === 'string' && _customer.fullname.length > 0);
    };

    customer.subscribe(function(_customer){
        setIsCustomerIsLoggedIn(_customer);
    }, this);


    var visibilityFunctions = {
        'showForCustomer': function (node) {
            isCustomerLoggedIn.subscribe(function(isLoggedIn){
                if (isLoggedIn) {
                    $(node).show();
                } else {
                    $(node).hide();
                }
            });
        },
        'showForGuest': function (node) {
            isCustomerLoggedIn.subscribe(function(isLoggedIn){
                if (!isLoggedIn) {
                    $(node).show();
                } else {
                    $(node).hide();
                }
            });
        }
    };

    return function(config, node) {
        if (config && Object.keys(config).length > 0) {
            _.each(config, function(value, index){
               if (value && typeof visibilityFunctions[index] === 'function') {
                   visibilityFunctions[index](node);
               }
            });
            setIsCustomerIsLoggedIn(customer());
        }
    };
});
