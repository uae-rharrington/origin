/**
 * @category ClassyLlama
 * @package ClassyLlama_BreadcrumbsFix
 * @copyright Copyright (c) 2018 ClassyLlama
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (widget) {
        $.widget('mage.breadcrumbs', widget, {
            options: {
                breadcrumbPath: ''
            },

            /**
             * Returns category menu item.
             *
             * Tries to resolve category from url or from referrer as fallback and
             * find menu item from navigation menu by category url.
             *
             * @return {Object|null}
             * @private
             */
            _resolveCategoryMenuItem: function () {
                var categoryUrl = this._resolveCategoryUrl(),
                    menu = $(this.options.menuContainer),
                    categoryMenuItem = null;

                if (categoryUrl && menu.length) {
                    categoryMenuItem = menu.find('a[href="' + categoryUrl + '"]');
                }

                if (!categoryMenuItem.length) {
                    categoryUrl = this._resolveBreadcrumpsUrl();
                    categoryMenuItem = menu.find('a[href="' + categoryUrl + '"]');
                }

                return categoryMenuItem;
            },

            /**
             * Returns category url.
             *
             * @return {String}
             * @private
             */
            _resolveBreadcrumpsUrl: function () {
                var categoryUrl;

                if (this.options.useCategoryPathInUrl && this.options.breadcrumbPath) {
                    categoryUrl = window.location.href.split('?')[0];
                    categoryUrl = categoryUrl.substring(0, categoryUrl.lastIndexOf('/')) +
                        this.options.categoryUrlSuffix;
                    categoryUrl = categoryUrl.split('/').slice(-2).join('');
                    categoryUrl = this.options.breadcrumbPath[categoryUrl]
                        ? this.options.breadcrumbPath[categoryUrl].link
                        : '';
                } else {
                    categoryUrl = document.referrer;

                    if (categoryUrl.indexOf('?') > 0) {
                        categoryUrl = categoryUrl.substr(0, categoryUrl.indexOf('?'));
                    }
                }

                return categoryUrl;
            }
        });

        return $.mage.breadcrumbs;
    };
});
