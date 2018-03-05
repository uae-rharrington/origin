<?php
namespace Swissup\SoldTogether\Block\Adminhtml\Product\Edit\Tab;

class Customer extends Order
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('soldtogether_customer_grid');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_model = $objectManager->get('Swissup\SoldTogether\Model\Customer');
    }

    public function getGridUrl()
    {
        return $this->getUrl(
            'soldtogether/product/customerGrid',
            ['_current' => true]
        );
    }
}
