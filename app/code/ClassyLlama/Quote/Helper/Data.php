<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** Regions Restrictions Config Path */
    const XML_PATH_QUOTE_LIFETIME = 'checkout/cart/delete_quote_after';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var bool */
    private $isQuoteRequest;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Get quote lifetime
     *
     * @return array
     **/
    public function getQuoteLifetime()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_QUOTE_LIFETIME,
            ScopeInterface::SCOPE_STORE
        );
    }

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