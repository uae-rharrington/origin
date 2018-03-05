/**
 * ArgentoSticky allows to create sicky elements with media rules
 */

define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    'js/lib/sticky-kit'
], function($, mediaCheck) {
    'use strict';

    $.widget('argento.argentoSticky', {
        options: {
            // media: '(min-width: 768px) and (min-height: 600px)',
            // parent: $('.page-wrapper')
        },

        _create: function() {
            if (this.options.media) {
                mediaCheck({
                    media: this.options.media,
                    entry: $.proxy(function() {
                        if (!this.options.disabled) {
                            this.element.stick_in_parent(this.options);
                        }
                    }, this),
                    exit: $.proxy(function() {
                        this.element.trigger('sticky_kit:detach');
                    }, this)
                });
            } else {
                this.element.stick_in_parent(this.options);
            }
        },

        enable: function() {
            this.element.stick_in_parent(this.options);
            return this._super();
        },

        disable: function() {
            this.element.trigger('sticky_kit:detach');
            return this._super();
        }
    });

    return $.argento.argentoSticky;
});
