<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */

namespace Swissup\ProLabels\Helper;

use Swissup\ProLabels\Helper\AbstractLabel;
use Magento\Store\Model\ScopeInterface;

/**
 * ProLabels Product Page Labels
 *
 * @author     Templates-Master Team <core@magentocommerce.com>
 */
class ProductLabels extends AbstractLabel
{
    /**
     * @return Get On Sale Label Data
     */
    public function getOnSaleLabel($product, $mode)
    {
        $isOnSaleConfig = $this->_scopeConfig->getValue("prolabels/on_sale/{$mode}", ScopeInterface::SCOPE_STORE);
        if (!$isOnSaleConfig["active"]
            || !$this->isOnSale($product)
            || !$this->validatePredefinedVariable($isOnSaleConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($isOnSaleConfig, $product, $mode);
    }

    /**
     * @return Get Is New Label Data
     */
    public function getIsNewLabel($product, $mode)
    {
        $isInSaleConfig = $this->_scopeConfig->getValue("prolabels/is_new/{$mode}", ScopeInterface::SCOPE_STORE);
        if (!$isInSaleConfig["active"]
            || !$this->isNew($product)
            || !$this->validatePredefinedVariable($isInSaleConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($isInSaleConfig, $product, $mode);
    }

    /**
     * @return Get Stock Label Data
     */
    public function getStockLabel($product, $mode)
    {
        $stockConfig = $this->_scopeConfig->getValue("prolabels/in_stock/{$mode}", ScopeInterface::SCOPE_STORE);
        if (!$stockConfig["active"]
            || !$this->getStockItemsValue($stockConfig, $product , $mode)
            || !$this->validatePredefinedVariable($stockConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($stockConfig, $product, $mode);
    }

    /**
     * @return Get Out Of Stock Label Data
     */
    public function getOutOfStockLabel($product, $mode)
    {
        $isSalable = $product->isSalable();
        $stockConfig = $this->_scopeConfig->getValue("prolabels/out_stock/{$mode}", ScopeInterface::SCOPE_STORE);
        if (!$stockConfig["active"]
            || $isSalable
            || !$this->validatePredefinedVariable($stockConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($stockConfig, $product, $mode);
    }

    /**
     * Check If Product Has Discount
     *
     * @param $product \Magento\Catalog\Model\Product
     * @return
     */
    public function isOnSale($product)
    {
        $store = $this->_storeManager->getStore()->getId();
        $regularPrice = $this->_priceCurrency->convertAndRound(
            $product->getData('price')
        );

        if ('bundle' === $product->getTypeId()) {
            if ($product->getSpecialPrice()) {
                $specialPriceFrom = $product->getSpecialFromDate();
                $specialPriceTo   = $product->getSpecialToDate();
                return $this->_localeDate->isScopeDateInInterval($store, $specialPriceFrom, $specialPriceTo);
            }
        } elseif ('grouped' === $product->getTypeId()) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProductIds */
            $simpleProductIds = $product->getTypeInstance()->getAssociatedProducts($product);
            foreach($simpleProductIds as $simpleProduct) {
                if (floatval($simpleProduct->getFinalPrice()) < floatval($simpleProduct->getPrice())) {
                    return true;
                }
            }
        } elseif ($product->getFinalPrice() < $regularPrice) {
            return true;
        }

        return false;
    }
}
