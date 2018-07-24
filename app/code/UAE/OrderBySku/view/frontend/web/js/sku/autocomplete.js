/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (quickorderSkuAutocomplete) {

        $.widget('mage.quickorderSkuAutocomplete', quickorderSkuAutocomplete, {

            /**
             * Widget initializationю
             *
             * @private
             */
            _create: function () {
                var self = this;

                $(this.element).keyup(function(e) {
                    self._capitalize(e.target);
                });
            },

            /**
             * Capitalize letters in input.
             *
             * @param {Object} target
             */
            _capitalize: function (target) {
                var $element = $(target);

                $element.val($element.val().toUpperCase());
            }
        });

        return $.mage.quickorderSkuAutocomplete;
    }
});
