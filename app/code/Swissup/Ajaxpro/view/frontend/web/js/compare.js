define([
    'jquery',
    'mage/translate',
    'Magento_Customer/js/customer-data',
    'Swissup_Ajaxpro/js/ajaxcian-data-post',
    'jquery/ui',
    'Magento_Catalog/js/compare'
], function ($, $t, customerData, AjaxproAjaxcianDataPost) {
    'use strict';

    $.widget('swissup.compareItems', $.mage.compareItems, {
        /**
         * Override Constructor
         * Add custom confirm logic and ajax
         */
        _create: function () {
            var self = this,
            sectionData = customerData.get('compare-products');

            this._super();

            sectionData.subscribe(function () {
                self.element.decorate('list', true);
                self._confirm(self.options.removeSelector, self.options.removeConfirmMessage);
                self._confirm(self.options.clearAllSelector, self.options.clearAllConfirmMessage);
            });
        },

        /**
         *
         * @param  {String} selector
         * @param  {String} message
         * @return {Boolean}
         */
        _confirm: function (selector, message) {
            var ret = false;

            $(selector).off('click').on('click', function (e) {
                var element = $(e.currentTarget),
                el;

                e.preventDefault();
                ret = confirm(message); // eslint-disable-line no-alert

                if (ret) {
                    el = AjaxproAjaxcianDataPost({}, element);
                    el._ajax(element);
                }
            });

            return ret;
        }
    });

    return $.swissup.compareItems;
});
