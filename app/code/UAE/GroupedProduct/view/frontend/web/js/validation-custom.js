/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

define([
    'jquery'
], function ($) {
    "use strict";

    return function () {
        $.validator.addMethod(
            'validate-grouped-qty-custom',
            function (value, element, params) {
                var val = $(element).val();

                if (!element.validity.valid) {
                    // If input is invalid, return false (to individually invalidate).
                    return false;
                } else if (val && val.length > 0) {
                    // If input has value, confirm that it's more than 0 (to individually validate/invalidate).
                    var valInt = parseInt(val, 10) || 0;

                    return valInt > 0;
                } else {
                    // If input is valid and has no value, then it's empty. Determine if all other inputs are also
                    // empty (to validate or invalidate collectively)...
                    var inputs = $(params).find('input[data-validate*="validate-grouped-qty-custom"]');
                    var values = 0;

                    inputs.each(function (i, e) {
                        var val = $(e).val();

                        if (val && val.length > 0 || !e.validity.valid) {
                            values++;
                        }
                    });

                    // ...If any input has a value, return true (so the form will evaluate that input individually).
                    // Otherwise, all inputs are empty, so return false (to collectively invalidate).
                    return values > 0;
                }
            },
            $.mage.__('Please specify the quantity of product(s).')
        );
    }
});
