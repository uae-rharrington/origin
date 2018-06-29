/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    $.widget('bottom.Bar', {
        options: {
            backToTop: '.back_to_top',
            addToCart: '#product-addtocart-button-bottom',
            addToCartForm: '#product_addtocart_form',
            qtyInput: '.qty',
            scrollSpeed: '300'
        },

        /**
         * @private
         */
        _create: function () {
            this._clickTop();
            this._clickAddToCart();
        },

        /**
         * Click on the button.
         *
         * @private
         */
        _clickTop: function () {
            $(document).on('click', this.options.backToTop, this._animateScroll.bind(this));
        },

        /**
         * Scroll to top.
         *
         * @private
         */
        _animateScroll: function () {
            $('html, body').animate({
                scrollTop:0
            }, this.options.scrollSpeed);
        },

        /**
         * Click add to cart button.
         *
         * @private
         */
        _clickAddToCart: function () {
            $(document).on('click', this.options.addToCart, this._submitForm.bind(this));
        },

        /**
         * Submit add to cart form and unset all qtys.
         *
         * @private
         */
        _submitForm: function() {
            $(this.options.addToCartForm).submit();
            $(this.options.qtyInput).val('');
        }
    });

    return $.bottom.Bar;
});
