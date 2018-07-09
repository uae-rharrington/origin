<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * Key for Quote Request Payment Method in the Layout Config Array
     */
    const QUOTE_REQUEST_PAYMENT_KEY = 'quote-request';

    /**
     * @var bool
     */
    protected $isQuote = false;

    /**
     * @var \ClassyLlama\Quote\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \ClassyLlama\Quote\Helper\Data $data
     */
    public function __construct(\ClassyLlama\Quote\Helper\Data $data)
    {
        $this->dataHelper = $data;
    }

    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if ($this->dataHelper->getIsQuoteRequest()) {
            $jsLayout = $this->setQuotePaymentProcessors($jsLayout);
        }

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']
        ['component'] = 'ClassyLlama_Quote/js/view/summary/cart-items';

        $jsLayout['components']['checkout']['children']['sidebar']['children']['summary']['children']['cart_items']
            ['children']['details']['component'] = 'ClassyLlama_Quote/js/view/summary/item/details';

        return $jsLayout;
    }

    /**
     * Sets the Quote Request Payment Processor and removes all others
     *
     * @param array $jsLayout
     * @return array
     */
    protected function setQuotePaymentProcessors(array $jsLayout)
    {
        $quotePayment = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['renders']['children'][self::QUOTE_REQUEST_PAYMENT_KEY];

        $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']
            ['children']['renders']['children'] = [self::QUOTE_REQUEST_PAYMENT_KEY => $quotePayment];
        
        unset($jsLayout['components']['checkout']['children']['progressBar']);

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['customer-email']['tooltip']
            ['description'] = __('We will send your quote to this email');

        return $jsLayout;
    }
}
