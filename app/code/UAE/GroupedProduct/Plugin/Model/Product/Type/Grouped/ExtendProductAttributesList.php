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
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function afterGetAssociatedProductCollection(
        \Magento\GroupedProduct\Model\Product\Type\Grouped $subject,
        $collection
    ) {

        $collection->addAttributeToSelect(['color', 'thumbnail', 'short_description', 'description']);

        return $collection;
    }
}
