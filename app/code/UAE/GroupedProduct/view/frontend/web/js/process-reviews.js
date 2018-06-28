/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    'use strict';

    /**
     * @param {String} url
     * @param {Int} productId
     */
    function processReviews(url, productId) {
        $.ajax({
            url: url,
            cache: true,
            dataType: 'html',
            showLoader: false,
            loaderContext: $('.product.data.items')
        }).done(function (data) {
            $('#product-review-container-' + productId).html(data);
        });
    }

    return function (config) {
        var reviewTab = $(config.reviewsTabSelector),
            requiredReviewTabRole = 'tab',
            productId = config.productId;

        if (reviewTab.attr('role') === requiredReviewTabRole && reviewTab.hasClass('active')) {
            processReviews(config.productReviewUrl, productId);
        } else {
            reviewTab.one('beforeOpen', function () {
                processReviews(config.productReviewUrl, productId);
            });
        }
    };
});
