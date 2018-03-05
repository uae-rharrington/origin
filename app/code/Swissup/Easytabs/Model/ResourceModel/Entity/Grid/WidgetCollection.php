<?php
namespace Swissup\Easytabs\Model\ResourceModel\Entity\Grid;

/**
 * Collection for displaying grid of tabs
 */
class WidgetCollection extends Collection
{
    protected function _beforeLoad()
    {
        $this->addWidgetTabFilter();
        return parent::_beforeLoad();
    }
}
