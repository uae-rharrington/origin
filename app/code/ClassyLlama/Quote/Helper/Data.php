<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var bool
     */
    private $isQuoteRequest;

    /**
     * @return bool
     */
    public function getIsQuoteRequest()
    {
        if (!isset($this->isQuoteRequest)) {
            $this->isQuoteRequest = $this->getRequestIsQuote();
        }

        return $this->isQuoteRequest;
    }

    /**
     * Returns a URL to the Add Quote Request To Cart endpoint
     *
     * @param int $orderId
     * @return string
     */
    public function getAddQuoteRequestToCartUrl($orderId)
    {
        return $this->_urlBuilder->getUrl('quoterequest/cart/add', ['id' => $orderId]);
    }

    /**
     * @return bool
     */
    protected function getRequestIsQuote()
    {
        $isQuote = $this->_getRequest()->getParam('quote');
        return $isQuote ? strtolower($isQuote) === 'true' ? true : false : false;
    }
}
