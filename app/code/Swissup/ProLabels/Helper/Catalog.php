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
 * @author     Swissup Team <core@magentocommerce.com>
 */
class Catalog extends AbstractLabel
{
    /**
     * @return Get On Sale Label Data
     */
    public function getProductLabels($product)
    {
        if (!isset($this->catalogLabel[$product->getId()])) {
            $this->load($product);
        }
        $output = '';
        if (empty($this->catalogLabel[$product->getId()])) {
            return $output;
        }

        foreach ($this->catalogLabel[$product->getId()] as $position => $labels) {
            if ('content' == $position) { continue; }
            $output .= '
                <div class="'.$position.' absolute">
            ';
            foreach ($labels as $label) {
                $output .= $this->generateLabelHtml($label);
            }
            $output .= '</div>';
        }
        return $output;
    }

    public function load($product)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        $customerGroupId = $customerSession->getCustomerGroupId();

        if ($onSale = $this->getOnSaleLabel($product)) {
            $this->catalogLabel[$product->getId()][$onSale->getPosition()][] = $onSale;
        }
        if ($isNew = $this->getIsNewLabel($product)) {
            $this->catalogLabel[$product->getId()][$isNew->getPosition()][] = $isNew;
        }
        if ($inStock = $this->getStockLabel($product)) {
            $this->catalogLabel[$product->getId()][$inStock->getPosition()][] = $inStock;
        }
        if ($outOfStock = $this->getOutStockLabel($product)) {
            $this->catalogLabel[$product->getId()][$outOfStock->getPosition()][] = $outOfStock;
        }

        if ($product->hasData('product_labels')) {
            $storeId = $this->_storeManager->getStore()->getId();
            $productManualLabels = $product->getData('product_labels');
            foreach ($productManualLabels as $label) {
                $labelStores = unserialize($label['store_id']);
                if (!in_array('0', $labelStores)) {
                    if (!in_array($storeId, $labelStores)) { continue; }
                }
                $labelGroupIds = unserialize($label['customer_groups']);
                if (!in_array($customerGroupId, $labelGroupIds)) {
                    continue;
                }
                $labelConfig = new \Magento\Framework\DataObject(
                    [
                        'position' => $label['category_position'],
                        'text' => $label['category_text'],
                        'custom' => $label['category_custom_style'],
                        'custom_url' => $label['category_custom_url'],
                        'round_method' => $label['category_round_method'],
                        'round_value' => $label['category_round_value'],
                        'image' => $label['category_image']
                    ]
                );
                $this->catalogLabel[$product->getId()][$labelConfig->getPosition()][] = $labelConfig;
            }
        }

        return $this;
    }

    public function getContentLabels($product)
    {
        $output = '';
        if (empty($this->catalogLabel[$product->getId()])) {
            return $output;
        }
        if (!array_key_exists('content', $this->catalogLabel[$product->getId()])) {
            return $output;
        }

        $output .= '<div class="prolabels-content-wrapper">';
        foreach ($this->catalogLabel[$product->getId()]['content'] as $label) {
            $output .= $this->generateLabelHtml($label);
        }
        $output .= '</div>';

        return $output;
    }

    public function generateLabelHtml($label)
    {
        $customUrlStart = '';
        $customUrlEnd = '';
        if ($label->getCustomUrl()) {
            $customUrlStart = '
                <a href="'.$label->getCustomUrl().'" target="_blank">
            ';
            $customUrlEnd = '</a>';
        }
        if ($label->getImage()) {
            $labelImage = $this->getUploadedLabelImage($label->getImage(), 'category');
            $labelImagePath = $this->getUploadedLabelImagePath($label->getImage(), 'category');
            list($productImageWidth, $productImageHeight) = getimagesize($labelImagePath);
            $dim = 'width: ' . $productImageWidth . 'px;height: ' . $productImageHeight. 'px;';
            $customStyle = $label->getCustom() . "background: url(" . $labelImage . ") no-repeat;" . $dim;
            $position = $label->getPosition();
            $text = $label->getText();
            return $customUrlStart . '
            <span class="prolabel" style="'.$customStyle.'">
                <span class="prolabel__inner">
                    <span class="prolabel__wrapper">
                        <span class="prolabel__content">'.$text.'</span>
                    </span>
                </span>
            </span>' . $customUrlEnd;
        } else {
            //custom label
            return $customUrlStart . '
            <span class="prolabel" style="'.$label->getCustom().'">
                <span class="prolabel__inner">
                    <span class="prolabel__wrapper">
                        <span class="prolabel__content">'.$label->getText().'</span>
                    </span>
                </span>
            </span>
            ' . $customUrlEnd;
        }
    }

    /**
     * @return Get On Sale Label
     */
    public function getOnSaleLabel($product)
    {
        $isOnSaleConfig = $this->_scopeConfig->getValue("prolabels/on_sale/category", ScopeInterface::SCOPE_STORE);
        if (!$isOnSaleConfig["active"]
            || !$this->isOnSale($product)
            || !$this->validatePredefinedVariable($isOnSaleConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($isOnSaleConfig, $product, 'category');
    }

    /**
     * @return Get Is New Label Data
     */
    public function getIsNewLabel($product)
    {
        $isInSaleConfig = $this->_scopeConfig->getValue("prolabels/is_new/category", ScopeInterface::SCOPE_STORE);
        if (!$isInSaleConfig["active"]
            || !$this->isNew($product)
            || !$this->validatePredefinedVariable($isInSaleConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($isInSaleConfig, $product, 'category');
    }

    /**
     * @return Get Stock Label Data
     */
    public function getStockLabel($product)
    {
        $stockConfig = $this->_scopeConfig->getValue("prolabels/in_stock/category", ScopeInterface::SCOPE_STORE);
        if (!$stockConfig["active"]
            || !$this->getStockItemsValue($stockConfig, $product , 'category')
            || !$this->validatePredefinedVariable($stockConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($stockConfig, $product, 'category');
    }

    /**
     * @return Get Stock Label Data
     */
    public function getOutStockLabel($product)
    {
        $isSalable = $product->isSalable();

        $stockConfig = $this->_scopeConfig->getValue("prolabels/out_stock/category", ScopeInterface::SCOPE_STORE);
        if (!$stockConfig["active"]
            || $isSalable
            || !$this->validatePredefinedVariable($stockConfig, $product))
        {
            return false;
        }

        return $this->getLabelOutputObject($stockConfig, $product, 'category');
    }

    /**
     * Check If Product Has Discount
     * @param $product \Magento\Catalog\Model\Product
     * @return bool
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
            /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $simpleProducts */
            $simpleProducts = $product->getTypeInstance()->getAssociatedProducts($product);
            foreach($simpleProducts as $simpleProduct) {
                if (floatval($simpleProduct->getFinalPrice()) < floatval($simpleProduct->getPrice())) {
                    return true;
                }
            }
        } elseif ('configurable' === $product->getTypeId()) {
            /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price $confPrice */
            $confPrice = $this->_objectManager->get(
                'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Price'
            );
            if ($confPrice->getFinalPrice(1, $product) < $regularPrice) {
                return true;
            }
        } elseif ($product->getFinalPrice() < $regularPrice) {
            return true;
        }
        return false;
    }
}
