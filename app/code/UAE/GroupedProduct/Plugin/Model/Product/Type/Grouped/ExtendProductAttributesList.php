<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\GroupedProduct\Plugin\Model\Product\Type\Grouped;

/**
 * Extends product attributes list.
 */
class ExtendProductAttributesList
{
    /**
     * Add additional attributes to product collection when retrieving associated products.
     *
     * @param \Magento\GroupedProduct\Model\Product\Type\Grouped $subject
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function afterGetAssociatedProductCollection(
        \Magento\GroupedProduct\Model\Product\Type\Grouped $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection $collection,
        \Magento\Catalog\Model\Product $product
    ) {
        $attributes = $product->getAttributes();
        $collection->addAttributeToSelect(array_keys($attributes));

        return $collection;
    }
}
