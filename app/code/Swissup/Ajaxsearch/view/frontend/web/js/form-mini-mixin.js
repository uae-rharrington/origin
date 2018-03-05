define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        //disable standard quikSearch widget
        $.widget('mage.quickSearch', widget, {
            /**
             * @return void
             */
            _create: function () {
                return;
                // return this._super();
            }
        });

        return $.mage.quickSearch;
    };
});
