/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (config, element) {
        var cartUpdates;

        if (navigator.userAgent.indexOf('Firefox') > -1 ||
            navigator.userAgent.indexOf('Edge') > -1 ||
            (navigator.userAgent.indexOf('Safari') === -1 && navigator.userAgent.indexOf('Chrome') === -1)
        ) {
            cartUpdates = 0;
        } else if (config.cartUpdates) {
            cartUpdates = Number(config.cartUpdates);
        }

        $(element).on('click', function (e) {
            e.preventDefault();
            history.go(-1 - cartUpdates);
        });
    };
});
