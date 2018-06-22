<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\AdvancedCheckout\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class is responsible for retrieving config settings for that extension.
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string
     */
    private $xmlPathFreeShippingMessage = 'uae_checkout/product_messages/free_shipping';

    /**
     * @var string
     */
    private $xmlPathShipsFromManufacturerMessage = 'uae_checkout/product_messages/ships_from_manufacturer';

    /**
     * @var string
     */
    private $xmlPathDeliveryTimeMessage = 'uae_checkout/product_messages/delivery_time';

    /**
     * @var string
     */
    private $xmlPathQualifyingItemsMessage = 'uae_checkout/product_messages/qualifying_items';

    /**
     * @var string
     */
    private $xmlPathQualifyingItemsRule = 'uae_checkout/cart_rules/qualifying_items';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve free shipping message.
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getFreeShippingMessage($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($this->xmlPathFreeShippingMessage, $scopeType, $scopeCode);
    }

    /**
     * Retrieve ships from manufacturer message.
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getShipsFromManufacturerMessage($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($this->xmlPathShipsFromManufacturerMessage, $scopeType, $scopeCode);
    }

    /**
     * Retrieve delivery time message.
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getDeliveryTimeMessage($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($this->xmlPathDeliveryTimeMessage, $scopeType, $scopeCode);
    }

    /**
     * Retrieve qualifying items message.
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getQualifyingItemsMessage($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($this->xmlPathQualifyingItemsMessage, $scopeType, $scopeCode);
    }

    /**
     * Retrieve qualifying items rule id.
     *
     * @param string $scopeType
     * @param string $scopeCode
     * @return string
     */
    public function getQualifyingItemsRule($scopeType = ScopeInterface::SCOPE_STORE, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($this->xmlPathQualifyingItemsRule, $scopeType, $scopeCode);
    }
}
