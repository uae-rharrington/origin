/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

define([
    'domReady!'
], function () {
    'use strict';

    return function () {
        var params = window.location.search;
        var scrollToSku = function (sku) {
            var skuElements = document.getElementsByClassName('product-item-sku');

            for (var j = 0; j < skuElements.length; j++) {
                var element = skuElements[j];
                if (element.innerHTML.toUpperCase() === sku.toUpperCase()) {
                    var pageTopOffset = element.getBoundingClientRect().top + document.getElementsByTagName('html')[0].scrollTop;
                    var headerHeight = 106;
                    var scrollDistance = pageTopOffset - (window.innerHeight - headerHeight) * 0.4;
                    var inputForSku = element.parentElement.parentElement.getElementsByTagName('input')[0];

                    window.scrollTo(0, scrollDistance);
                    inputForSku.focus();

                    return false;
                }
            }

            return false;
        };

        if (params.length) {
            params = params.substr(1).split('&');

            for (var i = 0; i < params.length; i++) {
                if (params[i].indexOf('item') > -1) {
                    var itemParam = params[i].split('=');
                    return scrollToSku(itemParam[1]);
                }
            }
        }
    };
});
