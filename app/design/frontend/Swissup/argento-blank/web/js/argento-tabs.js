/**
 * ArgentoTabs allows to create mage.tabs on highlight widgets and any other
 * widgets with block and block title that will be used as tab title.
 */

define([
    "jquery",
    "jquery/ui",
    "mage/tabs",
    "jquery/visible"
], function($) {
    'use strict';

    $.widget('argento.argentoTabs', {
        options: {
            collapsibleElement: ".block-title",
            content: ".block",
            openedState: "active"
        },

        _create: function() {
            this._processBlocks();
            this._callTabs();
            this._bind();
        },

        _processBlocks: function() {
            var options = this.options;
            $(options.collapsibleElement, this.element).each(function() {
                var content = $(this).parents(options.content);
                content.addClass('item content');

                $(this).insertBefore(content).addClass('data item title');
                $(this).children(":first").addClass('data switch');
            });
            this.element.addClass('argento-tabs');
        },

        _callTabs: function() {
            this.element.tabs(this.options);
        },

        _bind: function() {
            this.element.on('dimensionsChanged', function(e, state) {
                if (!state.opened) {
                    return;
                }
                var el = $(e.target);
                if (el.visible(true)) {
                    return;
                }
                $('html, body').scrollTop(el.offset().top);
            });
        }
    });

    return $.argento.argentoTabs;
});
