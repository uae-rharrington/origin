/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (quickOrderItemTable) {

        $.widget('mage.quickOrderItemTable', quickOrderItemTable, {
            formSelector: '[data-role="send-sku"]',

            /**
             * @inheritdoc
             */
            _add: function (event, data) {
                var newRowIndex = this.rowIndex + 1,
                    self = this;
                this.options.addBlockData.skuTabIndex = this.options.addBlockData.removeTabIndex
                    ? this.options.addBlockData.removeTabIndex + 1 : 1;
                this.options.addBlockData.qtyTabIndex = this.options.addBlockData.skuTabIndex
                    ? this.options.addBlockData.skuTabIndex + 1 : 2;
                this.options.addBlockData.removeTabIndex = this.options.addBlockData.qtyTabIndex
                    ? this.options.addBlockData.qtyTabIndex + 1 : 3;
                this.options.itemsRenderCallbacks[newRowIndex] = data ? data.callback : function () {};

                this._super();

                $('input').keydown(function(e) {
                    self._switch(e);
                });
            },

            /**
             * Switch to the next input if 'enter' button is clicked.
             *
             * @param {Event} e
             */
            _switch: function (e) {
                var focusable = null,
                    next = null,
                    form = $(this.formSelector);
                if (e.keyCode == 13 || e.keyCode == 9) {
                    e.preventDefault();
                    focusable = form.find('input').filter(':visible');
                    next = focusable.eq(focusable.index(e.target) + 1);
                    if (next.length) {
                        $(next).focus();
                    }


                    return false;
                }
            }
        });

        return $.mage.quickOrderItemTable;
    }
});
