/**
 * @category UAE
 * @package UAE_OrderComments
 * @copyright Copyright (c) 2018 ClassyLlama
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-shipping-information': {
                'UAE_OrderComments/js/action/set-shipping-information-mixin': true
            }
        }
    }
};
