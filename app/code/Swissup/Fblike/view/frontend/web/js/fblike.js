define([
    'jquery'
], function ($) {
    'use strict';

    $.widget('swissup.fblike', {

        options: {
            appId: ''
        },

        /**
         * [_init description]
         */
        _init: function () {
            if (typeof FB === 'undefined') {
                // set callback on facebook SDK load
                window.fbAsyncInit = this.fbInit.bind(this);
                var s = document.createElement('script');
                s.type = "text/javascript";
                s.src = '//connect.facebook.net/'
                    + document.documentElement.lang.replace('-', '_')
                    +'/sdk.js';
                $('head').append(s);
            } else {
                this.fbInit();
            }
        },

        /**
         * Initialize facebook buttons
         * @return {[type]} [description]
         */
        fbInit: function () {
            FB.init({
                appId: this.options.appId,
                xfbml: true,
                version: 'v2.10'
            });
            this.addObservers();
        },

        /**
         * Add click observer for custom like button
         */
        addObservers: function () {
            $('.fbl-custom .like').each(function () {
                if ($(this).hasClass('initialized')) {
                    return;
                }
                $(this).click(function () {
                    // variable 'this' - element with the observer
                    // call fb dialog to like product
                    // jscs:disable requireCamelCaseOrUpperCaseIdentifiers
                    FB.ui(
                        {
                            method: 'share_open_graph',
                            action_type: 'og.likes',
                            action_properties: JSON.stringify({
                                object: $(this).data('url')
                            })
                        },
                        function (response) {}
                    );
                    // jscs:enable requireCamelCaseOrUpperCaseIdentifiers
                });
                $(this).addClass('initialized');
            });
        }
    });

    return $.swissup.fblike;
});
