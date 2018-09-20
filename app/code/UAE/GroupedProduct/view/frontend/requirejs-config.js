/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

var config = {
    config: {
        mixins: {
            'mage/validation': {
                'UAE_GroupedProduct/js/validation-custom': true
            }
        }
    },
    map: {
        '*': {
            highlightSku: 'UAE_GroupedProduct/js/highlight-sku'
        }
    }
};
