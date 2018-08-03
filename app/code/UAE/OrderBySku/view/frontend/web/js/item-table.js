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
                this.options.addBlockData.skuTabIndex = this.options.addBlockData.qtyTabIndex
                    ? this.options.addBlockData.qtyTabIndex + 1 : 1;
                this.options.addBlockData.qtyTabIndex = this.options.addBlockData.skuTabIndex
                    ? this.options.addBlockData.skuTabIndex + 1 : 2;
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
                if (e.keyCode == 13) {
                    e.preventDefault();
                    focusable = form.find('input').filter(':visible');
                    next = focusable.eq(focusable.index(e.target) + 1);

                    this.element.find(this.options.itemsSelector).children()
                        .on('contentUpdated', function() {
                            setTimeout(function () {
                                $(next).focus();
                            },10);
                        });

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
