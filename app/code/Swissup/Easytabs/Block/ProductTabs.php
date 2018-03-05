<?php
namespace Swissup\Easytabs\Block;

class ProductTabs extends Tabs
{
    protected function _getCollection()
    {
        $collection = parent::_getCollection();
        $collection->addProductTabFilter();
        return $collection;
    }

    public function getInitOptions()
    {
        return $this->getVar('options');
    }
}
