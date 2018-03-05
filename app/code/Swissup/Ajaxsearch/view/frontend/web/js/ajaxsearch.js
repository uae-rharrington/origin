define([
    'jquery',
    'uiComponent',
    'Magento_Catalog/js/price-utils',
    'AjaxsearchLoader',
    'mage/template',
    'typeaheadbundle'
], function ($, Component, utils, loader, mageTemplate) {
    'use strict';

    var _options = {
        elementId: '#search',
        classes: {
            container: '.block-swissup-ajaxsearch',
            mask: '.ajaxsearch-mask',
            formLabel: '#search_mini_form .search label'
        },
        templates: {
            autocomplete: '#swissup-ajaxsearch-autocomplete-template',
            product: '#swissup-ajaxsearch-product-template',
            page: '#swissup-ajaxsearch-page-template',
            category: '#swissup-ajaxsearch-category-template',
            notFound: '#swissup-ajaxsearch-template-not-found'
        }
    },
    bloodhound,
    _element;

    /**
     *
     * @param  {String} id
     * @return {String}
     */
    function _template(id) {
        return mageTemplate(id);
    }

    /**
     *
     * @param  {Object} item
     * @return {String}
     */
    function _renderSuggestion(item) {
        // debugger;
        // console.log(item);
        var type = item._type || false,
        template = _template(_options.templates.autocomplete);

        if (type === 'debug') {
            console.log(item._select);
        }

        if (type === 'product') {
            // item.description = item.short_description + '' || '';
            // if (50 < item.description.lenght) {
            //     item.description = item.description.substr(0, 50) + '...';
            // }
            template = _template(_options.templates.product);
        }

        if (type === 'category') {
            template = _template(_options.templates.category);
        }

        if (type === 'page') {
            template = _template(_options.templates.page);
        }

        return template({
            item: item,
            formatPrice: utils.formatPrice,
            priceFormat: _options.settings.priceFormat
        });
    }

    /**
     * Init folded design
     */
    function initFoldedDesign() {
        var search = {
            /**
             * Show
             */
            show: function () {
                search.calculateFieldPosition();

                // show fields
                $(_options.classes.container).addClass('shown');
                $(_options.classes.mask).addClass('shown');
                $(_options.elementId).focus();
            },

            /**
             * Hide
             */
            hide: function () {
                $(_options.classes.container).removeClass('shown');
                $(_options.classes.mask).removeClass('shown');
            },

            /**
             * Calculate and set
             */
            calculateFieldPosition: function () {
                // calculate offsetTop dynamically to guarantee that field
                // will appear in the right place (dynamic header height, etc.)
                var header = $('.header.content'),
                    headerOffset = header.offset(),
                    zoomOffset = $('.action.search', _options.classes.container).offset(),
                    offsetTop = zoomOffset.top - headerOffset.top;

                if (header.length === 0) {
                    header = $('.header .container');
                }

                if ($('body').width() < 768) {
                    // reset for small screens
                    offsetTop = '';
                } else if (offsetTop <= 0) {
                    return;
                }
                $('.action.close', _options.classes.container).css({
                    top: offsetTop
                });
                $('.field.search', _options.classes.container).css({
                    paddingTop: offsetTop
                });
            },

            /**
             *
             * @return {Boolean}
             */
            isVisible: function () {
                return $(_options.classes.container).hasClass('shown')
                 || !$(_options.classes.container).find('div.control').hasClass('inactive');
            }
        };

        $(_options.classes.container).append(
            '<div class="' + _options.classes.mask.substr(1) + '"></div>'
        );

        $(document.body).keydown(function (e) {
            if (e.which === 27) {
                search.hide();
            }
        });

        $(window).resize(function () {
            search.calculateFieldPosition();
        });

        $(_options.classes.mask).click(function () {
            search.hide();
        });

        $('.action.search', _options.classes.container).click(function (e) {
            if (!search.isVisible()) {
                e.preventDefault();
                search.show();
            }
        });

        $('.action.close', _options.classes.container).click(function (e) {
            e.preventDefault();
            search.hide();
        });
    }

    /**
     * On ready init
     * @param  {Object} Bloodhound
     */
    function _init(Bloodhound) {
        var loaderCall = 0, block;

        _element = $(_options.elementId);
        block = _element.closest('.block.block-search');

        block
            .addClass(_options.classes.container.replace('.', ''))
            .addClass(_options.classes.additional)
            ;
        $(document.body).removeClass('swissup-ajaxsearch-loading');

        //add close action
        if (block.find('.actions .action.close').length == 0 &&
            block.find('.actions .action.search').length > 0) {

            $("<span title='Close' class='action close'><span>×</span></span>" )
                .insertAfter(block.find('.actions .action.search'));
        }


        bloodhound = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('title'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: _options.url,
                wildcard: _options.wildcard
            }
        });
        bloodhound.initialize();

        loader
            .setContainer(_options.loader.container)
            .setLoaderImage(_options.loader.loaderImage);

        _element.typeahead(_options.typeahead.options, {
            name: 'product',
            source: bloodhound.ttAdapter(),
            displayKey: 'title',
            limit: _options.typeahead.limit,
            templates: {
                notFound: _template(_options.templates.notFound),
                // pending: _template('#swissup-ajaxsearch-product-template-pending'),
                // header: _template('#swissup-ajaxsearch-product-template-header'),
                // footer: _template('#swissup-ajaxsearch-template-footer'),
                suggestion: _renderSuggestion
            }

        }
        ).bind('typeahead:selected', function (event, item) {
            var type = item._type || false;

            if (type === 'product' && typeof item['product_url'] != 'undefined') {
                window.location.href = item['product_url'];
            } else if ((type === 'page' || type === 'category') &&
                typeof item.url != 'undefined') {

                window.location.href = item.url;
            } else {
                this.form.submit();
            }
        }).on('typeahead:asyncrequest', function () {
            if (loaderCall === 0) {
                loader.startLoader();
            }
            loaderCall++;
        }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
            loaderCall--;

            if (loaderCall === 0) {
                loader.stopLoader();
            }
        });

        _element.on('blur', $.proxy(function () {
            setTimeout($.proxy(function () {
                _element.closest('div').addClass('inactive');
            }, this), 250);
        }, this));
        _element.trigger('blur');

        _element.on('focus', function () {
            _element.closest('div').removeClass('inactive');
        });
        $(_options.classes.formLabel).on('click', function () {
            _element.closest('div').removeClass('inactive');
        });

        if ($(_options.classes.container).hasClass('folded')) {
            initFoldedDesign();
        }
    }

    return Component.extend({
        options: {
            url: '',
            wildcard: '_QUERY',
            loader: {
                container: '.block-swissup-ajaxsearch .actions .action',
                loaderImage: ''
            },
            typeahead: {
                options: {
                    highlight: true,
                    hint: true,
                    minLength: 3,
                    classNames: {}
                },
                limit: 10
            },
            settings: {}
        },

        /**
         * initialize
         * @return {this}
         */
        initialize: function () {
            this._super();
            this.setOptions(this.options);

            require([
                'bloodhound',
                'typeahead.js'
            ], function (Bloodhound) {
                $(_init(Bloodhound));
            });

            return this;
        },

        /**
         *
         * @return {String}
         */
        version: function () {
            return this.options.settings.version;
        },
        // getOptions: function() {
        //     return _options;
        // },
        /**
         *
         * @param {Object} options
         * @return {this}
         */
        setOptions: function (options) {
            $.extend(_options, options);

            return this;
        }
    });
});
