<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace ClassyLlama\Quote\Block\Onepage;

/**
 * One page checkout success page
 */
class Success extends \Magento\Checkout\Block\Onepage\Success
{
    /**
     * Store info name value
     */
    const XML_PATH_STORE_INFO_NAME = 'general/store_information/name';

    /**
     * @var string
     */
    protected $_template = 'ClassyLlama_Quote::onepage/success.phtml';

    /**
     * Prepares block data
     *
     * @return void
     */
    protected function prepareBlockData()
    {
        parent::prepareBlockData();

        $order = $this->_checkoutSession->getLastRealOrder();
        $this->setStoreName($order->getStore()->getConfig(self::XML_PATH_STORE_INFO_NAME));
        $this->setOrderRealId($order->getEntityId());
    }
}
