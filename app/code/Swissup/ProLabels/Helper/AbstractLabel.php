<?php
/**
 * Copyright © 2015 Swissup. All rights reserved.
 */

namespace Swissup\ProLabels\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * ProLabels Abstract Label Helper
 *
 * @author     Swissup Team <core@magentocommerce.com>
 */
class AbstractLabel extends AbstractHelper
{
    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_pricingHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\CatalogInventory\Api\StockStateInterface
     */
    protected $_stockState;

    /**
     * Object manager
     *
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockStateInterface $_stockState
    ) {
        $this->_scopeConfig   = $context->getScopeConfig();
        $this->_localeDate    = $localeDate;
        $this->_storeManager  = $storeManager;
        $this->_pricingHelper = $pricingHelper;
        $this->_priceCurrency = $priceCurrency;
        $this->_objectManager = $objectManager;
        $this->_stockState    = $_stockState;
        $this->catalogLabel   = [];

        parent::__construct($context);
    }

    /**
     * @param array $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return \Magento\Framework\DataObject
     */
    public function getLabelOutputObject($config, $product, $mode)
    {
        $labelText = $this->getLabelText($config, $product, $mode);

        $labelData = new \Magento\Framework\DataObject(
            [
                'position'   => $config['position'],
                'text'       => $labelText,
                'image'      => $config['image'],
                'custom'     => $config['custom'],
                'custom_url' => $config['custom_url']
            ]
        );
        return $labelData;
    }

    /**
     * Label Text
     *
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return string
     */
    public function getLabelText($config, $product, $mode)
    {
        $variableData = array();
        preg_match_all('/#.+?#/', $config["text"], $vars);
        foreach (current($vars) as $var) {
            if (strpos($var, '#attr:') !== false) {
                $attribute = str_replace('#attr:', '', $var);
                $attribute = str_replace('#', '', $attribute);
                $attribute = $product->getResource()->getAttribute($attribute);
                $variableData[$var] = $attribute->getFrontend()->getValue($product);
                continue;
            }
            switch ($var) {
                case "#discount_percent#":
                    $variableData[$var] = $this->getDiscountPersentValue($config, $product, $mode);
                    break;
                case "#discount_amount#":
                    $variableData[$var] = $this->getDiscountAmountValue($config, $product, $mode);
                    break;
                case "#special_price#":
                    $variableData[$var] = $this->getSpecialPriceValue($config, $product, $mode);
                    break;

                case "#price#":
                    $variableData[$var] = $this->getPriceValue($config, $product, $mode);
                    break;

                case "#final_price#":
                    $variableData[$var] = $this->getFinalPriceValue($config, $product, $mode);
                    break;
                case "#stock_item#":
                    $variableData[$var] = $this->getStockItemsValue($config, $product, $mode);
                    break;
            }
        }

        return str_replace(array_keys($variableData), $variableData, $config["text"]);
    }

    /**
     * Get Product Discount Persent Value
     *
     * @param array $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return float
     */
    public function getDiscountPersentValue($config, $product, $mode)
    {
        if ('grouped' === $product->getTypeId()) {
            $discountValue = $this->getGroupedProductDiscountPersent($product);
            $discountValue = $discountValue / $config['round_value'];
        } elseif ('bundle' === $product->getTypeId()) {
            $discountValue = $product->getSpecialPrice() / $config['round_value'];
        } else {
            $finalPrice = $product->getFinalPrice();
            $regularPrice = $this->_priceCurrency->convertAndRound(
                $product->getData('price')
            );

            $discountValue = (100 - $finalPrice * 100 / $regularPrice) / $config['round_value'];
        }

        $roundMethod   = $config['round_method'];
        $discountValue = $roundMethod($discountValue);
        $discountValue = $discountValue * $config['round_value'];
        // if ('grouped' === $product->getTypeId()) {
        //     $discountValue = __('up to ') . $discountValue;
        // }

        return $discountValue;
    }

    /**
     * Get Product Discount Amount Value
     *
     * @param array $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return string
     */
    public function getDiscountAmountValue($config, $product, $mode)
    {
        if ('grouped' === $product->getTypeId()) {
            $discountValue = $this->getGroupedProductDiscountAmount($product);
            $discountValue = $discountValue / $config['round_value'];
        } elseif ('bundle' === $product->getTypeId()) {
            $price = $product->getPriceModel()->getTotalPrices($product);
            $fullPrice = ($price[1] * 100) / ($product->getSpecialPrice());
            $discountValue = $fullPrice - $price[1];
        } else {
            $regularPrice = $this->_priceCurrency->convertAndRound(
                $product->getData('price')
            );
            $discountValue = $regularPrice - $product->getFinalPrice();
        }

        $roundMethod = $config['round_method'];
        $discountValue = $discountValue / $config['round_value'];
        $discountValue = $roundMethod($discountValue);
        $discountValue = $discountValue * $config['round_value'];
        $discountValue = $this->_priceCurrency->convertAndFormat($discountValue);
        // if ('grouped' === $product->getTypeId() || 'bundle' === $product->getTypeId()) {
        //     $discountValue = __('up to ') . $discountValue;
        // }

        return $discountValue;
    }

    /**
     * Get Grouped Product Discount Value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getGroupedProductDiscountPersent($product)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProducts */
        $simpleProducts = $product->getTypeInstance()->getAssociatedProducts($product);
        $maxResult = 0;
        foreach ($simpleProducts as $simpleProduct) {
            $price = $simpleProduct->getData('price');
            $calculatedPrice = $simpleProduct->getFinalPrice();
            $result = 100- ($calculatedPrice * 100 / $price);
            if (floatval($price) > floatval($calculatedPrice)) {
                if ($result > $maxResult) {
                    $maxResult = $result;
                }
            }
        }
        return $maxResult;
    }

    /**
     * Get Grouped Product Discount Value
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float
     */
    public function getGroupedProductDiscountAmount($product)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProducts */
        $simpleProducts = $product->getTypeInstance()->getAssociatedProducts($product);
        $maxResult = 0;
        foreach ($simpleProducts as $simpleProduct) {
            $price = $simpleProduct->getData('price');
            $calculatedPrice = $simpleProduct->getFinalPrice();
            $result = $price - $calculatedPrice;
            if (floatval($price) > floatval($calculatedPrice)) {
                if ($result > $maxResult) {
                    $maxResult = $result;
                }
            }
        }
        return $maxResult;
    }

    /**
     * Get Product Special Price Value
     *
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return string
     */
    public function getSpecialPriceValue($config, $product, $mode)
    {
        if ($discountValue = $product->getSpecialPrice()) {
            $discountValue = $discountValue / $config['round_value'];
            $roundMethod = $config['round_method'];
            $discountValue = $roundMethod($discountValue);
            $discountValue = $discountValue * $config['round_value'];
            $discountValue = $this->_priceCurrency->convertAndFormat($discountValue);
            return $discountValue;
        }
    }

    /**
     * Get Product Price Value
     *
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return string
     */
    public function getPriceValue($config, $product, $mode)
    {
        $discountValue = $product->getPrice();
        $discountValue = $discountValue / $config['round_value'];
        $roundMethod = $config['round_method'];
        $discountValue = $roundMethod($discountValue);
        $discountValue = $discountValue * $config['round_value'];
        $discountValue = $this->_priceCurrency->convertAndFormat($discountValue);
        return $discountValue;
    }

    /**
     * Get Product Final Price Value
     *
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return string
     */
    public function getFinalPriceValue($config, $product, $mode)
    {
        $discountValue = $product->getFinalPrice();
        $store = $this->_storeManager->getStore()->getId();
        $discountValue = $discountValue / $config['round_value'];
        $roundMethod = $config['round_method'];
        $discountValue = $roundMethod($discountValue);
        $discountValue = $discountValue * $config['round_value'];
        $discountValue = $this->_priceCurrency->convertAndFormat($discountValue);
        return $discountValue;
    }

    /**
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @param $mode
     * @return bool|float|mixed
     */
    public function getStockItemsValue($config, $product, $mode)
    {
        if (!$product->isSalable()) {
            return false;
        }
        $simpleQty = array();
        if ('grouped' === $product->getTypeId()) {
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProducts */
            $childIds = $product->getTypeInstance()->getAssociatedProducts($product);
            foreach ($childIds as $simpleProduct) {
                $simpleQty[] = $this->_stockState->getStockQty($simpleProduct->getId());
            }
            $quantity = min($simpleQty);
        } elseif ('bundle' === $product->getTypeId()) {
            $optionIds = $product->getTypeInstance()->getOptionsIds($product);
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProducts */
            $simpleProducts = $product->getTypeInstance()->getSelectionsCollection($optionIds, $product);
            foreach ($simpleProducts as $simpleProduct) {
                $simpleQty[] = $this->_stockState->getStockQty($simpleProduct->getId());
            }
            $quantity = min($simpleQty);
        } elseif ('configurable' === $product->getTypeId()) {
            $simpleProducts = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($simpleProducts as $simpleProduct) {
                $simpleQty[] = $this->_stockState->getStockQty($simpleProduct->getId());
            }
            $quantity = min($simpleQty);
        } else {
            $quantity = $this->_stockState->getStockQty($product->getId());
        }

        if ($quantity < $config["stock_lower"]) {
            return $quantity;
        }

        return false;
    }

    /**
     * Check If Product Is New
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function isNew($product)
    {
        $store           = $this->_storeManager->getStore()->getId();
        $specialNewsFrom = $product->getNewsFromDate();
        $specialNewsTo   = $product->getNewsToDate();
        if ($specialNewsFrom ||  $specialNewsTo) {
            return $this->_localeDate->isScopeDateInInterval($store, $specialNewsFrom, $specialNewsTo);
        }

        return false;
    }

    /**
     * @param $config
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function validatePredefinedVariable($config, $product)
    {
        $productType = $product->getTypeId();
        if ('bundle' === $productType || 'grouped' === $productType) {
            preg_match_all('/#.+?#/', $config["text"], $vars);
            foreach (current($vars) as $var) {
                if (($var === '#special_price#') || ($var === '#special_date#')
                    || ($var === '#final_price#') || ($var === '#price#')) {
                    return false;
                }
            }
        }
        return true;
    }

    public function getUploadedLabelImage($imagePath, $mode)
    {
        $baseMediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $baseMediaUrl . 'prolabels/' . $mode . "/" . $imagePath;
    }

    public function getUploadedLabelImagePath($imagePath, $mode)
    {
        $baseMediaUrl = $this->_storeManager->getStore()->getBaseMediaDir();
        return $baseMediaUrl . '/' . 'prolabels/' . $mode . "/" . $imagePath;
    }
}
