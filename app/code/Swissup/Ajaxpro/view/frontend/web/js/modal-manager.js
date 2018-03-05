define([
    'jquery'
], function ($) {
    'use strict';

    return {
        elements: {},
        timestart: {},

        /**
         *
         * @param  {String} id
         * @param  {Element} element
         */
        register: function (id, element) {
            this.elements[id] = element;
            this.timestart[id] = (new Date).getTime();
        },

        /**
         * Show window
         *
         * @param  {mixed} value
         * @param  {String} key
         */
        show: function (value, key) {
            var id = 'ajaxpro-' + key,
            self = this,
            element,
            timeOffset;

            if (self.elements[id]) {
                element = self.elements[id];
                self.hide();
                timeOffset = 5000;

                if ((new Date).getTime() - self.timestart[id] < timeOffset) {
                    return;
                }
                element.trigger('openModal');
            }
        },

        /**
         * Hide modal window
         */
        hide: function () {
            $('.block-ajaxpro').each(function (i, el) {
                $(el).trigger('closeModal');
            });
        }
    };
});
