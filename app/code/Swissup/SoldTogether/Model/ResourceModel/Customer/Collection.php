<?php
namespace Swissup\SoldTogether\Model\ResourceModel\Customer;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Swissup\SoldTogether\Model\Customer', 'Swissup\SoldTogether\Model\ResourceModel\Customer');
    }
}
