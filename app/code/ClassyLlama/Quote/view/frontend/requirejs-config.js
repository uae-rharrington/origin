/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */
var config = {
    map: {
        '*': {
            'Magento_Checkout/template/minicart/content.html': 'ClassyLlama_Quote/template/minicart/content.html',
            'Magento_Checkout/template/payment-methods/list.html': 'ClassyLlama_Quote/template/payment-methods/list.html',
            'Magento_Ui/js/lib/knockout/bindings/i18n': 'ClassyLlama_Quote/js/i18n-override',
            'Magento_Checkout/template/shipping-address/address-renderer/default.html': 'ClassyLlama_Quote/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/billing-address/details.html': 'ClassyLlama_Quote/template/billing-address/details.html',
            "componentVisibility": "ClassyLlama_Quote/js/component-visibility"
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'ClassyLlama_Quote/js/quoterequest-place-order-mixin': true
            }
        }
    }
};
