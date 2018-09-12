/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
define([
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Catalog/js/price-utils'
], function ($, Component, getTotalsAction, priceUtils) {
    'use strict';

    return Component.extend({
        defaults: {
            priceFormat: {
                decimalSymbol: ".",
                groupLength: 3,
                groupSymbol: ",",
                integerRequired: 1,
                pattern: "$%s",
                precision: 2,
                requiredPrecision: 2
            }
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();
            var self = this;

            $('#cart-' + this.item_id + '-qty').keyup(function(event) {
                self._showItemButton($(event.target));
            });

            $('#update-cart-item-' + this.item_id).click(function(event) {
                self._updateItemQty($(event.currentTarget));
            });
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _showItemButton: function (elem) {
            var itemId = this.item_id,
                itemQty = elem.data('item-qty');
            if (this._isValidQty(itemQty, elem.val())) {
                $('#update-cart-item-' + itemId).show('fade', 300);
            } else if (elem.val() == 0) { //eslint-disable-line eqeqeq
                this._hideItemButton(elem);
            } else {
                this._hideItemButton(elem);
            }
        },

        /**
         * @param {*} origin - origin qty. 'data-item-qty' attribute.
         * @param {*} changed - new qty.
         * @returns {Boolean}
         * @private
         */
        _isValidQty: function (origin, changed) {
            return origin != changed && //eslint-disable-line eqeqeq
                changed.length > 0 &&
                changed - 0 == changed && //eslint-disable-line eqeqeq
                changed - 0 > 0;
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _hideItemButton: function (elem) {
            var itemId = this.item_id;

            $('#update-cart-item-' + itemId).hide('fade', 300);
        },

        /**
         * @param {HTMLElement} elem
         * @private
         */
        _updateItemQty: function (elem) {
            var itemId = this.item_id;

            this._ajax('/advancedcheckout/sidebar/updateItemQty', {
                'item_id': itemId,
                'item_qty': $('#cart-' + itemId + '-qty').val()
            }, elem, this._updateItemQtyAfter);
        },

        /**
         * Update content after update qty
         *
         * @param {HTMLElement} elem
         */
        _updateItemQtyAfter: function (elem) {
            var itemId = this.item_id,
                currentQty = $('#cart-' + itemId + '-qty').val();
            $('#cart-' + itemId + '-qty').data('item-qty', currentQty);
            this._hideItemButton(elem);
        },

        /**
         * @param {Array} itemData
         * @private
         */
        _updateItemPrice: function (itemData) {
            var price = priceUtils.formatPrice(itemData.price, this.priceFormat),
                subtotal = priceUtils.formatPrice(itemData.subtotal, this.priceFormat);

            $('.item-' + this.item_id + ' .price span.price').text(price);
            $('.item-' + this.item_id + ' .subtotal span.price').text(subtotal);
        },

        /**
         * @param {String} url - ajax url
         * @param {Object} data - post data for ajax call
         * @param {Object} elem - element that initiated the event
         * @param {Function} callback - callback method to execute after AJAX success
         */
        _ajax: function (url, data, elem, callback) {
            $.extend(data, {
                'form_key': $.mage.cookies.get('form_key')
            });

            $.ajax({
                url: url,
                data: data,
                type: 'post',
                dataType: 'json',
                context: this,

                /** @inheritdoc */
                beforeSend: function () {
                    elem.attr('disabled', 'disabled');
                },

                /** @inheritdoc */
                complete: function () {
                    elem.attr('disabled', null);
                }
            })
                .done(function (response) {
                    var msg;

                    if (response.success) {
                        callback.call(this, elem, response);
                        var deferred = $.Deferred();
                        getTotalsAction([], deferred);
                        this._updateItemPrice(response.itemData);
                    } else {
                        msg = response['error_message'];

                        if (msg) {
                            alert({
                                content: msg
                            });
                        }
                    }
                })
                .fail(function (error) {
                    console.log(JSON.stringify(error));
                });
        }
    });
});
