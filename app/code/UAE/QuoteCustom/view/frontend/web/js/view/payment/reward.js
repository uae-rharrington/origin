/**
 * @category UAE
 * @package UAE_QuoteCustom
 * @copyright Copyright (c) 2018 ClassyLlama
 */
define([
    'Magento_Checkout/js/model/quote'
], function (quote) {
    'use strict';

    var mixin = {
        /**
         * @return {Boolean}
         */
        isAvailable: function () {
            var subtotal = parseFloat(quote.totals()['grand_total']),
                rewardConfig = window.checkoutConfig.payment.reward,
                rewardUsedAmount = quote.totals()['extension_attributes']
                    ? parseFloat(quote.totals()['extension_attributes']['base_reward_currency_amount'])
                    : 0;

            return rewardConfig.isAvailable && subtotal > 0 && rewardUsedAmount <= 0;
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
