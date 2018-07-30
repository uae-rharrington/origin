/**
 * @category ClassyLlama
 * @package ClassyLlama_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
var config = {
    map: {
        "*": {
            'ShipperHQ_Shipper/template/checkout/shipping.html':
                'ClassyLlama_QuoteCustom/template/checkout/shipperhq_shipping.html',
            'Magento_Checkout/template/shipping.html':
                'ClassyLlama_QuoteCustom/template/checkout/shipping.html'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'ClassyLlama_QuoteCustom/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'ClassyLlama_QuoteCustom/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'ClassyLlama_QuoteCustom/js/checkout-data-mixin': true
            }
        }
    }
};
