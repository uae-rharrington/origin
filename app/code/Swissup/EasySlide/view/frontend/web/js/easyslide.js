define([
    'jquery',
    'Swissup_EasySlide/js/swiper'
], function ($, Swiper) {
    'use strict';

    $.widget('swissup.easyslide', {
        options: {
            autoHeight: true,
            centeredSlides: true,
            loop: true,
            roundLengths: true
        },

        _create: function () {
            this._super();

            this.swiper = new Swiper(this.element, this.options);

            // fix swiper initialization in hidden popups, dropdowns, etc
            var initSwiper = function() {
                if (!this.swiper.container.width()) {
                    return setTimeout(initSwiper, 200);
                }
                this.swiper.update();
            }.bind(this);

            if (!this.swiper.container.width()) {
                setTimeout(initSwiper, 200);
            }
        },

        _init: function () {
            this._super();
        }
    });

    return $.swissup.easyslide;
});
