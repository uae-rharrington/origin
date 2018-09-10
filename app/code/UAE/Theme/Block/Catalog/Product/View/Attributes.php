<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

/**
 * @inheritdoc
 */
namespace UAE\Theme\Block\Catalog\Product\View;

use Magento\Framework\Phrase;

/**
 * @inheritdoc
 */
class Attributes extends \Magento\Catalog\Block\Product\View\Attributes
{
    /**
     * @inheritdoc
     *
     * @param array $excludeAttr
     * @return array
     */
    public function getAdditionalData(array $excludeAttr = [])
    {
        $data = [];
        $product = $this->getProduct();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront() && !in_array($attribute->getAttributeCode(), $excludeAttr)) {
                $value = $attribute->getFrontend()->getValue($product);

                if ($value instanceof Phrase) {
                    $value = (string)$value;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }
//              EDIT: Only return data that has a value entered by admin.
                if (is_string($value) && strlen($value) && !ctype_space($value) && ($value !== 'No' || $attribute -> getFrontendInput() === 'boolean')) {
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
