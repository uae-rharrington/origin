<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\GroupedProduct\Block\Product\View;

use Magento\Catalog\Model\Product;
use Magento\Framework\Phrase;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Attributes block for simple products on the grouped product page.
 */
class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Product
     */
    private $product = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Set current simple product.
     *
     * @param Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get current simple product.
     *
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->product) {
            $this->product = $this->coreRegistry->registry('product');
        }
        return $this->product;
    }

    /**
     * $excludeAttr is optional array of attribute codes to
     * exclude them from additional data array
     *
     * @param array $excludeAttr
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getAdditionalData(array $excludeAttr = [])
    {
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    $value = __('N/A');
                } elseif ((string)$value == '') {
                    $value = __('No');
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                if ($value instanceof Phrase || (is_string($value) && strlen($value))) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }
        return $data;
    }
}
