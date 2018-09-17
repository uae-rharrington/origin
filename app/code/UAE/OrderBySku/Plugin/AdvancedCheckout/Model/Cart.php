<?php
/**
 * @category    ClassyLlama
 * @copyright   Copyright (c) 2018 Classy Llama
 */

namespace UAE\OrderBySku\Plugin\AdvancedCheckout\Model;

class Cart
{
    /**
     * Capitalize SKUs entered on Quick Order page
     */
    public function beforePrepareAddProductsBySku(\Magento\AdvancedCheckout\Model\Cart $subject, $items)
    {
        foreach ($items as $key => $item) {
            $items[$key]['sku'] = strtoupper($item['sku']);
        }

        return array($items);
    }
}
