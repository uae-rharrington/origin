<?php

namespace Swissup\SeoUrls\Model\Filter;

use \Magento\CatalogInventory\Model\Stock as InventoryStock;

class Stock extends Attribute
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return $this->helper->getPredefinedFilterLabel('stock_filter');
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        if (!$this->hasData('options')) {
            $options = [
                (string)InventoryStock::STOCK_IN_STOCK => $this->helper->getSeoFriendlyString(__('In')),
                (string)InventoryStock::STOCK_OUT_OF_STOCK => $this->helper->getSeoFriendlyString(__('Out'))
            ];
            $this->setData('options', $options);
        }

        return $this->getData('options');
    }
}
