/**
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
var config = {
    map: {
        "*": {
            'ShipperHQ_Shipper/template/checkout/shipping.html':
                'UAE_QuoteCustom/template/checkout/shipperhq_shipping.html',
            'Magento_Checkout/template/shipping.html':
                'UAE_QuoteCustom/template/checkout/shipping.html',
            'Magento_NegotiableQuote/template/shipping.html':
                'UAE_QuoteCustom/template/checkout/negotiable_shipping.html',
            'ClassyLlama_Quote/js/add-customer-note':
                'UAE_QuoteCustom/js/add-customer-note'
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'UAE_QuoteCustom/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'UAE_QuoteCustom/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/checkout-data': {
                'UAE_QuoteCustom/js/checkout-data-mixin': true
            },
            'Magento_Reward/js/view/payment/reward': {
                'UAE_QuoteCustom/js/view/payment/reward': true
            }
        }
    }
};
