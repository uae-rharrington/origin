define([
    "jquery",
    'Magento_Catalog/js/price-utils'
], function ($, utils) {
    'use strict';

    var SoldTogether = {
        SoldTogether: function (settings) {
            this.priceFormat = settings.priceFormat;
            this.taxDisplay = settings.taxDisplay;
            SoldTogether.init();
        },

        init: function () {
            this.addObservers();
            this.updateTotals();
        },

        addObservers: function () {
            var self = this;
            $(".relatedorderamazon-checkbox").change(function () {
                var el = $("#soldtogether-image-" + $(this).val());
                $(el).toggleClass("item-inactive");
                // if ($(this).is(":checked")) {
                //     $(el).removeClass("item-inactive");
                // } else {
                //     $(el).addClass("item-inactive");
                // }
                self.updateTotals();
            });

            $(".soldtogether-block .soldtogether-cart-btn").click(function () {
                self.addToCartItems();
            });
        },

        updateTotals: function () {
            var totalPrice = 0,
                totalExclPrice = 0,
                elTotal = $($(".totalprice .price-box .price-container .price-wrapper .price")),
                elIncTax = $(".totalprice .price-box .price-container .price-including-tax .price"),
                elExclTax = $(".totalprice .price-box .price-container .price-excluding-tax .price");
            var items = $(".amazonstyle-checkboxes .product-name");
            items.each(function () {
                if ($(this).find(".checkbox").attr("checked")) {
                    if (3 == this.taxDisplay) {
                        totalPrice += $(this).find(".price-box .price-container .price-including-tax").data("price-amount");
                        totalExclPrice += $(this).find(".price-box .price-container .price-excluding-tax").data("price-amount");
                    } else {
                        totalPrice += $(this).find(".price-box .price-container .price-wrapper").data("price-amount");
                    }
                }
            });
            if (3 == this.taxDisplay) {
                $(elIncTax).html(utils.formatPrice(totalPrice, this.priceFormat));
                $(elExclTax).html(utils.formatPrice(totalExclPrice, this.priceFormat));
            } else {
                $(elTotal).html(utils.formatPrice(totalPrice, this.priceFormat));
            }
        },

        addToCartItems: function () {
            $("html, body").animate({ scrollTop: 0 }, "slow");
            var items = $(".amazonstyle-checkboxes .product-name"),
                values = [],
                item;
            items.each(function () {
                item = $(this).find(".checkbox");
                if (item.attr("checked") && !item.hasClass("main-product")) {
                    values.push(item.val());
                }
            });
            values = $.unique(values);
            $("#related-products-field").val(values.join(','));
            $("#product-addtocart-button").click();
        }
    };

    return SoldTogether;
});
