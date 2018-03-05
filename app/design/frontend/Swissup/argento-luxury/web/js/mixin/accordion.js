define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';


    return function (mage_accordion) { // mage_accordion == Result that mage/accordion.js returns.

        if ($('body').hasClass('page-layout-1column')) {
            // do not expand all filters if one-column layout enabled
            return mage_accordion;
        }

        // wrap _create method to force uncollapsed accordion for layered navigation filters
        // (possibly) can be removed in latest versions
        mage_accordion.prototype._create
            =  wrapper.wrap(mage_accordion.prototype._create, function (originalFunction) {

                var filterOptionsId = 'narrow-by-list';

                if ($(this.element).attr('id') == filterOptionsId) {
                    this.options.multipleCollapsible = true;
                };

                originalFunction();

                if ($(this.element).attr('id') == filterOptionsId) {
                    this.activate();
                };

            });

        return mage_accordion;
    };
});
