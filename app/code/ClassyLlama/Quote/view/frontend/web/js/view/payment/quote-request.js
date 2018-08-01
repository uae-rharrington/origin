/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'quoterequest',
                component: 'ClassyLlama_Quote/js/view/payment/method-renderer/quote-request-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
