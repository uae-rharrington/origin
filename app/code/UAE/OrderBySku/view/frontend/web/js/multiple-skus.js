/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (quickOrderMultipleSkus) {

        $.widget('mage.quickOrderMultipleSkus', quickOrderMultipleSkus, {
            /**
             * Get all sku names
             *
             * @returns {Array} sku names
             * @private
             */
            _getValueArray: function () {
                return $(this.options.textArea).val().split(/,|\s|\n/);
            }
        });

        return $.mage.quickOrderMultipleSkus;
    }
});