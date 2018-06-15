<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\AdvancedCheckout\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

/**
 * Helper is needed because there is a bug in magento core that
 * Renderers block for quote and order items can't be overridden.
 */
class Data extends AbstractHelper
{
    /**
     * @var \UAE\AdvancedCheckout\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param Context $context
     * @param \UAE\AdvancedCheckout\Model\Config $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        \UAE\AdvancedCheckout\Model\Config $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * Prepare free shipping message to display on the store front.
     *
     * @return string
     */
    public function getFreeShippingMessage()
    {
        return $this->config->getFreeShippingMessage(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getCode()
        );
    }

    /**
     * Prepare ships from manufacturer message message to display on the store front.
     *
     * @return string
     */
    public function getShipsFromManufacturerMessage()
    {
        return $this->config->getShipsFromManufacturerMessage(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getCode()
        );
    }

    /**
     * Prepare delivery time message to display on the store front.
     *
     * @return string
     */
    public function getDeliveryTimeMessage()
    {
        return $this->config->getDeliveryTimeMessage(
            ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getCode()
        );
    }
}
