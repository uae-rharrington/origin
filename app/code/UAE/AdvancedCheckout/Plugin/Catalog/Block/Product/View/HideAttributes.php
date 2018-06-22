<?php
/**
 * @copyright   Copyright (c) 2018 Classy Llama Studios, LLC
 */

namespace UAE\AdvancedCheckout\Plugin\Catalog\Block\Product\View;

/**
 * Hides specific attributes with specific values on the "More Information" tab.
 */
class HideAttributes
{
    /**
     * Hide attributes.
     *
     * @param \Magento\Catalog\Block\Product\View\Attributes $subject
     * @param array $data
     * @return array
     */
    public function afterGetAdditionalData(\Magento\Catalog\Block\Product\View\Attributes $subject, array $data)
    {
        foreach ($data as $attrCode => $attrInfo) {
            if (($attrCode == 'qualifies_for_free_shipping' && in_array($attrInfo['value'], ['Yes', 'N/A']))
                || ($attrCode == 'ships_from_manufacturer' && in_array($attrInfo['value'], ['No', 'N/A']))
                || ($attrCode == 'delivery_time' && $attrInfo['value'] == 'N/A')) {
                unset($data[$attrCode]);
            }
        }

        return $data;
    }
}
