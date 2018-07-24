/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * Clear add to cart qty input fields on form submit.
     */
    function clearQtyInputs() {
        $('.qty input').val('');
    }

    return function () {
        $(document).on('ajax:addToCart', clearQtyInputs);
    };
});
