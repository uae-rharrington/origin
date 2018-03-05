define([
    'jquery',
    'mage/translate',
    'jquery/ui',
    'Magento_Catalog/js/catalog-add-to-cart'
], function ($, $t) {
    'use strict';

    $.widget('swissup.catalogAddToCart', $.mage.catalogAddToCart, {
        /**
         * Bind new Submit
         */
        _bindSubmit: function () {
            var self = this,
            isValidation = !!this.options.submitForcePreventValidation;

            if (isValidation) {
                this.element.mage('validation');
            }
            this.element.on('submit', function (e) {
                e.preventDefault();

                if (isValidation) {
                    if (self.element.valid()) {
                        self.submitForm($(this));
                    }
                } else {
                    self.submitForm($(this));
                }
            });
        },

        /**
         * Send ajax request
         * @param  {Element} form
         */
        ajaxSubmit: function (form) {
            var self = this;

            $(self.options.minicartSelector).trigger('contentLoading');
            self.disableAddToCartButton(form);

            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'post',
                dataType: 'json',

                /** @inheritdoc */
                beforeSend: function () {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },

                /** @inheritdoc */
                success: function (response) {
                    var eventData, parameters;

                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }

                    if (response.backUrl && !response.ajaxpro) {
                        eventData = {
                            'form': form,
                            'redirectParameters': []
                        };
                        // trigger global event, so other modules will be able add parameters to redirect url
                        $('body').trigger('catalogCategoryAddToCartRedirect', eventData);

                        if (eventData.redirectParameters.length > 0) {
                            parameters = response.backUrl.split('#');
                            parameters.push(eventData.redirectParameters.join('&'));
                            response.backUrl = parameters.join('#');
                        }
                        window.location = response.backUrl;

                        return;
                    }

                    if (response.messages) {
                        $(self.options.messagesSelector).html(response.messages);
                    }

                    if (response.minicart) {
                        $(self.options.minicartSelector).replaceWith(response.minicart);
                        $(self.options.minicartSelector).trigger('contentUpdated');
                    }

                    if (response.product && response.product.statusText) {
                        $(self.options.productStatusSelector)
                            .removeClass('available')
                            .addClass('unavailable')
                            .find('span')
                            .html(response.product.statusText);
                    }
                    self.enableAddToCartButton(form, response);
                }
            });
        },

        /**
         * Override default function and add ajax behaviour
         *
         * @param  {Element} form
         * @param  {Object} response
         */
        enableAddToCartButton: function (form, response) {
            var self = this,
            addToCartButton = $(form).find(this.options.addToCartButtonSelector),
            timeout = 1500,
            isAjaxproProductView = false;

            response = response || {};

            isAjaxproProductView = response && response.ajaxpro &&
                response.ajaxpro.product &&
                response.ajaxpro.product.has_options;

            if (!isAjaxproProductView) {
                setTimeout(function () {
                    var addToCartButtonTextAdded = self.options.addToCartButtonTextAdded || $t('Added');

                    addToCartButton.find('span').text(addToCartButtonTextAdded);
                    addToCartButton.attr('title', addToCartButtonTextAdded);
                }, timeout);
            }

            setTimeout(function () {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t('Add to Cart');

                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, timeout * 2);
        }
    });

    return $.swissup.catalogAddToCart;
});
