<?php

namespace Swissup\Navigationpro\Model\ResourceModel\Menu;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'menu_id';

    protected function _construct()
    {
        $this->_init(
            'Swissup\Navigationpro\Model\Menu',
            'Swissup\Navigationpro\Model\ResourceModel\Menu'
        );
    }
}
